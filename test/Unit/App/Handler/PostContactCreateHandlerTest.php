<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Dot\Mail\Email;
use Dot\Mail\Result\MailResult;
use Dot\Mail\Service\MailServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Light\App\Handler\PostContactCreateHandler;
use Light\App\Service\ContactService;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

use function array_key_exists;

class PostContactCreateHandlerTest extends TestCase
{
    /**
     * @return array<string, string>
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
        ];
    }

    private function service(MailResult|Throwable $sendResult): ContactService
    {
        $message = $this->createStub(Email::class);

        $mailService = $this->createStub(MailServiceInterface::class);
        $mailService->method('getMessage')->willReturn($message);
        if ($sendResult instanceof Throwable) {
            $mailService->method('send')->willThrowException($sendResult);
        } else {
            $mailService->method('send')->willReturn($sendResult);
        }

        return new ContactService($mailService);
    }

    /**
     * @throws Exception
     */
    public function testValidSubmissionRedirectsOnSuccess(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($this->validData());

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->never())->method('render');

        $handler  = new PostContactCreateHandler($template, $this->service(new MailResult(true)));
        $response = $handler->handle($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/contact/?contact=sent#contact-form', $response->getHeaderLine('Location'));
    }

    /**
     * @throws Exception
     */
    public function testMissingRequiredFieldsRerendersWithErrors(): void
    {
        $data            = $this->validData();
        $data['name']    = '';
        $data['message'] = '';

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($data);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with(
                'page::contact',
                $this->callback(function (array $params): bool {
                    return $params['contact_mail_failed'] === false
                        && array_key_exists('name', $params['contact_errors'])
                        && array_key_exists('message', $params['contact_errors'])
                        && $params['contact_values']['email'] === 'jane@example.com';
                })
            )
            ->willReturn('<html></html>');

        $handler  = new PostContactCreateHandler($template, $this->service(new MailResult(true)));
        $response = $handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testRerenderKeepsAnyExtraQueryParamsAsDynamicHiddenFields(): void
    {
        $data               = $this->validData();
        $data['name']       = '';
        $data['utm_source'] = 'google';
        $data['ref']        = 'partner';

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($data);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with(
                'page::contact',
                $this->callback(fn (array $params): bool => $params['query'] === [
                    'utm_source' => 'google',
                    'ref'        => 'partner',
                ])
            )
            ->willReturn('<html></html>');

        $handler = new PostContactCreateHandler($template, $this->service(new MailResult(true)));
        $handler->handle($request);
    }

    /**
     * @throws Exception
     */
    public function testMailTransportFailureRerendersWithMailFailedFlag(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($this->validData());

        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with(
                'page::contact',
                $this->callback(function (array $params): bool {
                    return $params['contact_errors'] === []
                        && $params['contact_mail_failed'] === true;
                })
            )
            ->willReturn('<html></html>');

        $handler  = new PostContactCreateHandler(
            $template,
            $this->service(new RuntimeException('SMTP connection refused'))
        );
        $response = $handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }
}
