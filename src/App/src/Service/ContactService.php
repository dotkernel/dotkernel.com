<?php

declare(strict_types=1);

namespace Light\App\Service;

use Dot\Mail\Service\MailServiceInterface;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\NotEmpty;
use Throwable;

use function htmlspecialchars;
use function http_build_query;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function nl2br;
use function sprintf;
use function trim;

use const ENT_QUOTES;

/**
 * @phpstan-type ContactData array{
 *     topic: string,
 *     name: string,
 *     email: string,
 *     company: string,
 *     stack: string,
 *     message: string,
 *     query: string,
 * }
 */
final class ContactService
{
    public const array TOPICS = [
        'migration' => 'Migration',
        'project'   => 'Project work',
        'oss'       => 'Open source',
        'other'     => 'Something else',
    ];

    private const array REQUIRED_FIELDS = [
        'name'    => 'Name',
        'email'   => 'Work email',
        'message' => 'Message',
    ];

    private const array RESERVED_FIELDS = ['topic', 'name', 'email', 'company', 'stack', 'message', 'contact'];

    public function __construct(private readonly MailServiceInterface $mailService)
    {
    }

    /**
     * @param array<array-key, mixed> $source
     * @return array<string, string>
     */
    public static function extractQuery(array $source): array
    {
        $params = [];
        foreach ($source as $param => $value) {
            if (is_string($param) && ! in_array($param, self::RESERVED_FIELDS, true) && is_scalar($value)) {
                $params[$param] = (string) $value;
            }
        }

        return $params;
    }

    /**
     * @return ContactData
     */
    public function normalize(mixed $parsedBody): array
    {
        $raw   = is_array($parsedBody) ? $parsedBody : [];
        $topic = $this->stringValue($raw, 'topic');

        return [
            'topic'   => isset(self::TOPICS[$topic]) ? $topic : '',
            'name'    => $this->stringValue($raw, 'name'),
            'email'   => $this->stringValue($raw, 'email'),
            'company' => $this->stringValue($raw, 'company'),
            'stack'   => $this->stringValue($raw, 'stack'),
            'message' => $this->stringValue($raw, 'message'),
            'query'   => http_build_query(self::extractQuery($raw)),
        ];
    }

    /**
     * @param ContactData $data
     * @return array<string, string>
     */
    public function validate(array $data): array
    {
        $errors   = [];
        $notEmpty = new NotEmpty();

        foreach (self::REQUIRED_FIELDS as $field => $label) {
            if (! $notEmpty->isValid($data[$field])) {
                $errors[$field] = $label . ' is required.';
            }
        }

        if ($data['email'] !== '' && ! (new EmailAddress())->isValid($data['email'])) {
            $errors['email'] = 'Enter a valid email address.';
        }

        return $errors;
    }

    /**
     * @param ContactData $data
     */
    public function send(array $data): bool
    {
        $this->mailService->getMessage()->setReplyTo($data['email'], $data['name']);
        $this->mailService->setSubject('New contact form submission on dotkernel.com');
        $this->mailService->setBody($this->buildBody($data));

        try {
            return $this->mailService->send()->isValid();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param array<array-key, mixed> $raw
     */
    private function stringValue(array $raw, string $key): string
    {
        $value = $raw[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    /**
     * @param ContactData $data
     */
    private function buildBody(array $data): string
    {
        $rows = [
            'Topic'         => self::TOPICS[$data['topic']] ?? '',
            'Name'          => $data['name'],
            'Work email'    => $data['email'],
            'Company'       => $data['company'],
            'Current stack' => $data['stack'],
            'Message'       => $data['message'],
            'Query params'  => $data['query'],
        ];

        $html = '';
        foreach ($rows as $label => $value) {
            if ($value === '') {
                continue;
            }

            $html .= sprintf(
                '<p><strong>%s:</strong><br>%s</p>',
                htmlspecialchars($label, ENT_QUOTES),
                nl2br(htmlspecialchars($value, ENT_QUOTES))
            );
        }

        return $html;
    }
}
