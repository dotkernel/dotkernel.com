<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Enum\ContactTopicEnum;
use Light\App\Service\ContactService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateContactFormHandler implements RequestHandlerInterface
{
    public const string TEMPLATE = 'page::contact';

    #[Inject(
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();

        return new HtmlResponse($this->template->render(self::TEMPLATE, [
            'topics'          => ContactTopicEnum::cases(),
            'contact_success' => ($query['contact'] ?? null) === 'sent',
            'query'           => ContactService::extractQuery($query),
        ]));
    }
}
