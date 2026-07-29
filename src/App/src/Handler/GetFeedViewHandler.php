<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Laminas\Diactoros\Response\XmlResponse;
use Light\App\Service\FeedGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_get_contents;
use function filesize;
use function is_file;

class GetFeedViewHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly FeedGenerator $feedGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $feedFile = $this->feedGenerator->getFeedFile();

        if (! is_file($feedFile) || filesize($feedFile) === 0) {
            $this->feedGenerator->write();
        }

        return new XmlResponse(
            (string) file_get_contents($feedFile),
            200,
            ['content-type' => FeedGenerator::CONTENT_TYPE]
        );
    }
}
