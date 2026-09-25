<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\PostContactCreateHandler;
use Light\App\Service\ContactService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class PostContactCreateHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PostContactCreateHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $contactService = $container->get(ContactService::class);
        assert($contactService instanceof ContactService);

        return new PostContactCreateHandler($template, $contactService);
    }
}
