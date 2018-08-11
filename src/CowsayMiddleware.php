<?php declare(strict_types=1);

namespace WyriHaximus\Psr15\Cowsay;

use Cowsayphp\Farm;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function WyriHaximus\Psr7\asciiArtHeaders;

final class CowsayMiddleware implements MiddlewareInterface
{
    const HEADER = 'X-Cowsay';

    private $cowSay;

    public function __construct()
    {
        $this->cowSay = Farm::create(Farm\Cow::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $line = (string)$response->getStatusCode() . ' Moooo!';

        return asciiArtHeaders($response, self::HEADER, ...preg_split('/(\r\n?|\n)/', $this->cowSay->say($line)));
    }
}
