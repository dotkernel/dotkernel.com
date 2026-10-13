<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Closure;
use Dot\Mail\Email;
use Dot\Mail\Result\MailResult;
use Dot\Mail\Service\MailServiceInterface;
use Light\App\Service\ContactService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

use function array_shift;
use function str_contains;

class ContactServiceTest extends TestCase
{
    private const array TURNSTILE = ['site_key' => 'site-key', 'secret_key' => 'secret-key', 'hostname' => ''];

    private const string SITEVERIFY_SUCCESS = '{"success":true,"hostname":"www.dotkernel.com","action":"contact"}';

    /**
     * @return array{
     *     topic: string, name: string, email: string, company: string, stack: string, message: string,
     *     query: string
     * }
     */
    private function validData(): array
    {
        return [
            'topic'   => 'migration',
            'name'    => 'Jane Doe',
            'email'   => 'jane@example.com',
            'company' => 'Acme Inc',
            'stack'   => 'ZF3, PHP 7.4',
            'message' => 'We would like to talk about migrating our platform.',
            'query'   => '',
        ];
    }

    private function service(): ContactService
    {
        return new ContactService($this->createStub(MailServiceInterface::class), new NullLogger());
    }

    /**
     * @param array{site_key: string, secret_key: string, hostname: string} $turnstile
     * @param list<array<string, string>> $calls
     */
    private function turnstileService(array $turnstile, ?string $response, array &$calls = []): ContactService
    {
        $responses  = [$response];
        $siteverify = function (array $data) use (&$responses, &$calls): ?string {
            $calls[] = $data;

            return array_shift($responses);
        };

        $mailService = $this->createStub(MailServiceInterface::class);

        return new class ($mailService, $turnstile, $siteverify) extends ContactService {
            /**
             * @param array{site_key: string, secret_key: string, hostname: string} $turnstile
             */
            public function __construct(
                MailServiceInterface $mailService,
                array $turnstile,
                private readonly Closure $siteverifyFn,
            ) {
                parent::__construct($mailService, new NullLogger(), $turnstile);
            }

            protected function siteverify(array $data): ?string
            {
                return ($this->siteverifyFn)($data);
            }
        };
    }

    public function testNormalizeExtractsAndTrimsKnownFields(): void
    {
        $data = $this->service()->normalize([
            'name'    => '  Jane Doe  ',
            'email'   => 'jane@example.com',
            'unknown' => 'ignored',
        ]);

        $this->assertSame('Jane Doe', $data['name']);
        $this->assertSame('jane@example.com', $data['email']);
        $this->assertSame('', $data['company']);
        $this->assertArrayNotHasKey('unknown', $data);
    }

    public function testNormalizeDropsAnUnknownTopic(): void
    {
        $data = $this->service()->normalize(['topic' => 'not-a-topic']);

        $this->assertSame('', $data['topic']);
    }

    public function testNormalizeKeepsAKnownTopic(): void
    {
        $data = $this->service()->normalize(['topic' => 'oss']);

        $this->assertSame('oss', $data['topic']);
    }

    public function testNormalizeReturnsAllEmptyWhenNotAnArray(): void
    {
        $data = $this->service()->normalize('not-an-array');

        $this->assertSame(
            [
                'topic'   => '',
                'name'    => '',
                'email'   => '',
                'company' => '',
                'stack'   => '',
                'message' => '',
                'query'   => '',
            ],
            $data
        );
    }

    public function testNormalizeBuildsQueryStringFromAnyExtraField(): void
    {
        $data = $this->service()->normalize([
            'utm_source' => 'google',
            'ref'        => 'partner',
        ]);

        $this->assertSame('utm_source=google&ref=partner', $data['query']);
    }

    public function testNormalizeExcludesKnownContactFieldsFromQueryString(): void
    {
        $data = $this->service()->normalize([
            'name'       => 'Jane Doe',
            'email'      => 'jane@example.com',
            'contact'    => 'sent',
            'utm_source' => 'google',
        ]);

        $this->assertSame('utm_source=google', $data['query']);
    }

    public function testExtractQueryReturnsAnyNonReservedParam(): void
    {
        $query = ContactService::extractQuery([
            'utm_source' => 'newsletter',
            'ref'        => 'partner site',
        ]);

        $this->assertSame(['utm_source' => 'newsletter', 'ref' => 'partner site'], $query);
    }

    public function testExtractQueryExcludesReservedFormFields(): void
    {
        $query = ContactService::extractQuery([
            'name'       => 'Jane Doe',
            'contact'    => 'sent',
            'utm_source' => 'google',
        ]);

        $this->assertSame(['utm_source' => 'google'], $query);
    }

    public function testExtractQueryReturnsEmptyArrayWhenNoParams(): void
    {
        $this->assertSame([], ContactService::extractQuery([]));
    }

    public function testNormalizeIgnoresNonStringValues(): void
    {
        $data = $this->service()->normalize(['name' => ['not', 'a', 'string']]);

        $this->assertSame('', $data['name']);
    }

    public function testValidSubmissionHasNoErrors(): void
    {
        $errors = $this->service()->validate($this->validData());

        $this->assertSame([], $errors);
    }

    public function testMissingRequiredFieldsProduceErrors(): void
    {
        $data            = $this->validData();
        $data['name']    = '';
        $data['email']   = '';
        $data['message'] = '';

        $errors = $this->service()->validate($data);

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('message', $errors);
        $this->assertArrayNotHasKey('company', $errors);
        $this->assertArrayNotHasKey('stack', $errors);
    }

    public function testInvalidEmailFormatProducesError(): void
    {
        $data          = $this->validData();
        $data['email'] = 'not-an-email';

        $errors = $this->service()->validate($data);

        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * @throws Exception
     */
    public function testSendReturnsTrueOnSuccess(): void
    {
        $message = $this->createMock(Email::class);
        $message->expects($this->once())->method('setReplyTo')->with('jane@example.com', 'Jane Doe');

        $mailService = $this->createMock(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService
            ->expects($this->once())
            ->method('setSubject')
            ->with('New contact form submission on dotkernel.com');
        $mailService->expects($this->once())->method('setBody');
        $mailService->method('send')->willReturn(new MailResult(true));

        $service = new ContactService($mailService, new NullLogger());

        $this->assertTrue($service->send($this->validData()));
    }

    public function testSendReturnsFalseWhenTransportThrows(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createStub(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService->method('send')->willThrowException(new RuntimeException('SMTP connection refused'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with($this->anything(), $this->callback(
                fn (array $context): bool => $context['message'] === 'SMTP connection refused'
                    && $context['exception'] instanceof RuntimeException
            ));

        $service = new ContactService($mailService, $logger);

        $this->assertFalse($service->send($this->validData()));
    }

    public function testSendReturnsFalseWhenResultIsInvalid(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createStub(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService->method('send')->willReturn(new MailResult(false, 'Invalid message'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with($this->anything(), ['message' => 'Invalid message']);

        $service = new ContactService($mailService, $logger);

        $this->assertFalse($service->send($this->validData()));
    }

    public function testSendIncludesQueryParamsInBodyWhenPresent(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createMock(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService
            ->expects($this->once())
            ->method('setBody')
            ->with($this->callback(
                fn (string $body): bool => str_contains($body, 'Query params')
                    && str_contains($body, 'utm_source=google&amp;ref=partner')
            ));
        $mailService->method('send')->willReturn(new MailResult(true));

        $service = new ContactService($mailService, new NullLogger());

        $data          = $this->validData();
        $data['query'] = 'utm_source=google&ref=partner';

        $service->send($data);
    }

    public function testSendOmitsQueryRowWhenAbsent(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createMock(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService
            ->expects($this->once())
            ->method('setBody')
            ->with($this->callback(
                fn (string $body): bool => ! str_contains($body, 'Query params')
            ));
        $mailService->method('send')->willReturn(new MailResult(true));

        $service = new ContactService($mailService, new NullLogger());

        $service->send($this->validData());
    }

    public function testSendUsesTheTopicLabelInBody(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createMock(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService
            ->expects($this->once())
            ->method('setBody')
            ->with($this->callback(
                fn (string $body): bool => str_contains($body, '<strong>Topic:</strong><br>Migration')
            ));
        $mailService->method('send')->willReturn(new MailResult(true));

        (new ContactService($mailService, new NullLogger()))->send($this->validData());
    }

    public function testExtractQueryExcludesTheTurnstileToken(): void
    {
        $query = ContactService::extractQuery([
            'cf-turnstile-response' => 'token',
            'utm_source'            => 'google',
        ]);

        $this->assertSame(['utm_source' => 'google'], $query);
    }

    public function testTurnstileIsOffWithEmptyKeys(): void
    {
        $calls   = [];
        $service = $this->turnstileService(
            ['site_key' => '', 'secret_key' => '', 'hostname' => ''],
            self::SITEVERIFY_SUCCESS,
            $calls
        );

        $this->assertSame('', $service->turnstileSiteKey());
        $this->assertTrue($service->verifyTurnstile('', null));
        $this->assertSame([], $calls);
    }

    public function testTurnstileIsOffWithoutASecretKey(): void
    {
        $calls   = [];
        $service = $this->turnstileService(
            ['site_key' => 'site-key', 'secret_key' => '', 'hostname' => ''],
            self::SITEVERIFY_SUCCESS,
            $calls
        );

        $this->assertSame('', $service->turnstileSiteKey());
        $this->assertTrue($service->verifyTurnstile('', null));
        $this->assertSame([], $calls);
    }

    public function testTurnstileIsOffByDefault(): void
    {
        $this->assertSame('', $this->service()->turnstileSiteKey());
        $this->assertTrue($this->service()->verifyTurnstile('', null));
    }

    public function testTurnstileSiteKeyIsExposedWhenBothKeysAreSet(): void
    {
        $this->assertSame('site-key', $this->turnstileService(self::TURNSTILE, null)->turnstileSiteKey());
    }

    public function testEmptyTokenFailsWithoutCallingSiteverify(): void
    {
        $calls   = [];
        $service = $this->turnstileService(self::TURNSTILE, self::SITEVERIFY_SUCCESS, $calls);

        $this->assertFalse($service->verifyTurnstile('', '203.0.113.5'));
        $this->assertSame([], $calls);
    }

    public function testValidTokenPassesAndSendsSecretResponseAndRemoteIp(): void
    {
        $calls   = [];
        $service = $this->turnstileService(self::TURNSTILE, self::SITEVERIFY_SUCCESS, $calls);

        $this->assertTrue($service->verifyTurnstile('token', '203.0.113.5'));
        $this->assertSame([
            [
                'secret'   => 'secret-key',
                'response' => 'token',
                'remoteip' => '203.0.113.5',
            ],
        ], $calls);
    }

    public function testRemoteIpIsOmittedWhenNull(): void
    {
        $calls   = [];
        $service = $this->turnstileService(self::TURNSTILE, self::SITEVERIFY_SUCCESS, $calls);

        $this->assertTrue($service->verifyTurnstile('token', null));
        $this->assertSame([['secret' => 'secret-key', 'response' => 'token']], $calls);
    }

    /**
     * @return array<string, array{array{site_key: string, secret_key: string, hostname: string}, ?string}>
     */
    public static function rejectedSiteverifyProvider(): array
    {
        $withHostname = ['site_key' => 'site-key', 'secret_key' => 'secret-key', 'hostname' => 'www.dotkernel.com'];

        return [
            'success false'     => [self::TURNSTILE, '{"success":false,"error-codes":["invalid-input-response"]}'],
            'action mismatch'   => [self::TURNSTILE, '{"success":true,"action":"login"}'],
            'missing action'    => [self::TURNSTILE, '{"success":true,"hostname":"www.dotkernel.com"}'],
            'hostname mismatch' => [$withHostname, '{"success":true,"hostname":"evil.example","action":"contact"}'],
            'invalid json'      => [self::TURNSTILE, 'not json'],
            'network failure'   => [self::TURNSTILE, null],
        ];
    }

    /**
     * @param array{site_key: string, secret_key: string, hostname: string} $turnstile
     */
    #[DataProvider('rejectedSiteverifyProvider')]
    public function testRejectedSiteverifyFails(array $turnstile, ?string $response): void
    {
        $this->assertFalse($this->turnstileService($turnstile, $response)->verifyTurnstile('token', null));
    }

    public function testMatchingHostnamePasses(): void
    {
        $service = $this->turnstileService(
            ['site_key' => 'site-key', 'secret_key' => 'secret-key', 'hostname' => 'www.dotkernel.com'],
            self::SITEVERIFY_SUCCESS
        );

        $this->assertTrue($service->verifyTurnstile('token', null));
    }

    public function testTestingKeyResponsePassesEvenWithAHostnameConfigured(): void
    {
        $service = $this->turnstileService(
            ['site_key' => 'site-key', 'secret_key' => 'secret-key', 'hostname' => 'www.dotkernel.com'],
            '{"success":true,"hostname":"example.com","metadata":{"result_with_testing_key":true}}'
        );

        $this->assertTrue($service->verifyTurnstile('token', null));
    }
}
