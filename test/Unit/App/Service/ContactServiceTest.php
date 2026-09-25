<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Dot\Mail\Email;
use Dot\Mail\Result\MailResult;
use Dot\Mail\Service\MailServiceInterface;
use Light\App\Service\ContactService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function str_contains;

class ContactServiceTest extends TestCase
{
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
        return new ContactService($this->createStub(MailServiceInterface::class));
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

        $service = new ContactService($mailService);

        $this->assertTrue($service->send($this->validData()));
    }

    public function testSendReturnsFalseWhenTransportThrows(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createStub(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService->method('send')->willThrowException(new RuntimeException('SMTP connection refused'));

        $service = new ContactService($mailService);

        $this->assertFalse($service->send($this->validData()));
    }

    public function testSendReturnsFalseWhenResultIsInvalid(): void
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createStub(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        $mailService->method('send')->willReturn(new MailResult(false, 'Invalid message'));

        $service = new ContactService($mailService);

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

        $service = new ContactService($mailService);

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

        $service = new ContactService($mailService);

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

        (new ContactService($mailService))->send($this->validData());
    }
}
