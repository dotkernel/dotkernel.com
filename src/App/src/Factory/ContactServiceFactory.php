<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Dot\Mail\Service\MailServiceInterface;
use Light\App\Service\ContactService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class ContactServiceFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ContactService
    {
        $mailService = $container->get('dot-mail.service.contact');
        assert($mailService instanceof MailServiceInterface);

        return new ContactService($mailService);
    }
}
