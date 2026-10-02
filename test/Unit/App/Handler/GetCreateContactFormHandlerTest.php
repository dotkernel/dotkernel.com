<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Enum\ContactTopicEnum;
use Light\App\Handler\GetCreateContactFormHandler;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;

class GetCreateContactFormHandlerTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testHandleRendersTheContactFormWithTheTopics(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with('page::contact', $this->callback(
                fn (array $params): bool => $params['topics'] === ContactTopicEnum::cases()
                    && $params['contact_success'] === false
                    && $params['query'] === []
            ))
            ->willReturn('<form></form>');

        $response = (new GetCreateContactFormHandler($template))->handle($this->createRequest([]));

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('<form></form>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleFlagsSuccessAndKeepsExtraQueryParams(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with('page::contact', $this->callback(
                fn (array $params): bool => $params['contact_success'] === true
                    && $params['query'] === ['utm_source' => 'newsletter']
            ))
            ->willReturn('');

        (new GetCreateContactFormHandler($template))->handle(
            $this->createRequest(['contact' => 'sent', 'utm_source' => 'newsletter'])
        );
    }

    /**
     * @param array<string, mixed> $query
     * @throws Exception
     */
    private function createRequest(array $query): ServerRequestInterface
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($query);

        return $request;
    }
}
