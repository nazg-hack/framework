<?hh // strict

namespace NazgTest\Middleware;

use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\RequestHandlerInterface;

class FakeAttributeMiddleware implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    $request = $request->withAttribute('id', 'changed_value');
    return $handler->handle($request);
  }
}
