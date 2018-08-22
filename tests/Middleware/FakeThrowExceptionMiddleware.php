<?hh // strict

namespace NazgTest\Middleware;

use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\RequestHandlerInterface;

class FakeThrowExceptionMiddleware implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    throw new \Exception("error");
    return $handler->handle($request);
  }
}
