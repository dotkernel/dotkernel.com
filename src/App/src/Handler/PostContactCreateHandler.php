<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Dot\DependencyInjection\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Light\App\Enum\ContactTopicEnum;
use Light\App\Service\ContactService;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class PostContactCreateHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        UrlHelper::class,
        ContactService::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected UrlHelper $urlHelper,
        protected ContactService $contactService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data       = $this->contactService->normalize($request->getParsedBody());
        $errors     = $this->contactService->validate($data);
        $mailFailed = false;

        if ($errors === [] && ! $this->contactService->send($data)) {
            $mailFailed = true;
        }

        if ($errors !== [] || $mailFailed) {
            $rawBody = $request->getParsedBody();

            return new HtmlResponse($this->template->render(GetContactCreateFormHandler::TEMPLATE, [
                'topics'              => ContactTopicEnum::cases(),
                'contact_errors'      => $errors,
                'contact_values'      => $data,
                'contact_mail_failed' => $mailFailed,
                'query'               => ContactService::extractQuery(is_array($rawBody) ? $rawBody : []),
            ]));
        }

        return new RedirectResponse(
            $this->urlHelper->generate(
                GetContactCreateFormHandler::TEMPLATE,
                [],
                ['contact' => 'sent'],
                'contact-form'
            ),
            StatusCodeInterface::STATUS_SEE_OTHER
        );
    }
}
