<?php

declare(strict_types=1);

namespace Light\Blog\Factory;

use Light\Blog\Service\BlogService;
use Light\Blog\Service\BlogServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function getcwd;

class BlogServiceFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): BlogServiceInterface
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new BlogService($template, getcwd() . '/public/md-articles');
    }
}
