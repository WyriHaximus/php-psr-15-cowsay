<?php declare(strict_types=1);

namespace WyriHaximus\Psr15\Cowsay;

use Cowsayphp\AnimalInterface;
use Cowsayphp\Farm;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function WyriHaximus\Psr7\asciiArtHeaders;

final class CowsayMiddleware implements MiddlewareInterface
{
    const HEADER = 'X-Cowsay';

    /** @var AnimalInterface */
    private $animal;

    public function __construct(AnimalInterface $animal = null)
    {
        if (!($animal instanceof AnimalInterface)) {
            $animal = Farm::create(Farm\Cow::class);
        }

        $this->animal = $animal;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $line = (string)$response->getStatusCode() . ' Moooo!';

        return asciiArtHeaders($response, self::HEADER, ...preg_split('/(\r\n?|\n)/', $this->animal->say($line)));
    }
}
