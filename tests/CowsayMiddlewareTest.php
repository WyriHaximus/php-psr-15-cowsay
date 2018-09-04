<?php declare(strict_types=1);

namespace WyriHaximus\Tests\Psr15\Cowsay;

use ApiClients\Tools\TestUtilities\TestCase;
use Cowsayphp\AnimalInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WyriHaximus\Psr15\Cowsay\CowsayMiddleware;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

final class CowsayMiddlewareTest extends TestCase
{
    public function testHeader()
    {
        $cows = new CowsayMiddleware();
        $response = $cows->process(new ServerRequest(), new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        });

        self::assertTrue(isset($response->getHeaders()[CowsayMiddleware::HEADER]));
        self::assertInternalType('array', $response->getHeaders()[CowsayMiddleware::HEADER]);
        foreach ($response->getHeaders()[CowsayMiddleware::HEADER] as $line) {
            self::assertInternalType('string', $line);
        }
    }

    public function testCustomAnimal()
    {
        $cows = new CowsayMiddleware(new class() implements AnimalInterface {
            public function say($text)
            {
                return $text;
            }
        });
        $response = $cows->process(new ServerRequest(), new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        });

        self::assertTrue(isset($response->getHeaders()[CowsayMiddleware::HEADER]));
        self::assertInternalType('array', $response->getHeaders()[CowsayMiddleware::HEADER]);
        self::assertCount(1, $response->getHeaders()[CowsayMiddleware::HEADER]);
        self::assertSame('200 Moooo!', $response->getHeaders()[CowsayMiddleware::HEADER][0]);
    }
}
