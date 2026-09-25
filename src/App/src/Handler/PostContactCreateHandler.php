<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Light\App\Service\ContactService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class PostContactCreateHandler implements RequestHandlerInterface
{
    public const string TEMPLATE = 'page::contact';

    public function __construct(
        protected TemplateRendererInterface $template,
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

            return new HtmlResponse($this->template->render(self::TEMPLATE, [
                'contact_errors'      => $errors,
                'contact_values'      => $data,
                'contact_mail_failed' => $mailFailed,
                'query'               => ContactService::extractQuery(is_array($rawBody) ? $rawBody : []),
            ]));
        }

        return new RedirectResponse('/contact/?contact=sent#contact-form', 303);
    }
}
