<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetFeedViewHandler;
use Light\App\Service\FeedGenerator;
use Psr\Container\ContainerInterface;

use function assert;

class GetFeedViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetFeedViewHandler
    {
        $feedGenerator = $container->get(FeedGenerator::class);
        assert($feedGenerator instanceof FeedGenerator);

        return new GetFeedViewHandler($feedGenerator);
    }
}
