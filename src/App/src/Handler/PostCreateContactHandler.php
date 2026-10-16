<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Dot\DependencyInjection\Attribute\Inject;
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
use function is_string;

class PostCreateContactHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        UrlHelper::class,
        ContactService::class,
        'config.rka.ip_address',
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected UrlHelper $urlHelper,
        protected ContactService $contactService,
        protected array $ipAddressConfig,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data       = $this->contactService->normalize($request->getParsedBody());
        $errors     = $this->contactService->validate($data);
        $mailFailed = false;

        $rawBody = $request->getParsedBody();
        $token   = is_array($rawBody) && is_string($rawBody['cf-turnstile-response'] ?? null)
            ? $rawBody['cf-turnstile-response']
            : '';
        $ip      = $request->getAttribute($this->ipAddressConfig['attribute_name']);

        if (! $this->contactService->verifyTurnstile($token, $ip)) {
            $errors['captcha'] = "We couldn't verify you're human. Please try again.";
        }

        if ($errors === [] && ! $this->contactService->send($data, $ip)) {
            $mailFailed = true;
        }

        if ($errors !== [] || $mailFailed) {
            return new HtmlResponse($this->template->render(GetCreateContactFormHandler::TEMPLATE, [
                'topics'              => ContactTopicEnum::cases(),
                'contact_errors'      => $errors,
                'contact_values'      => $data,
                'contact_mail_failed' => $mailFailed,
                'query'               => ContactService::extractQuery(is_array($rawBody) ? $rawBody : []),
                'turnstile_site_key'  => $this->contactService->turnstileSiteKey(),
            ]));
        }

        return new RedirectResponse(
            $this->urlHelper->generate('app::create-contact-form', [], ['contact' => 'sent'], 'contact-form')
        );
    }
}
