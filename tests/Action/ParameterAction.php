<?hh // decl

namespace NazgTest\Action;

use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Server\RequestHandlerInterface;
use type Zend\Diactoros\Response\JsonResponse;

final class ParameterAction implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $_handler,
  ): ResponseInterface {
    return new JsonResponse(
      $request->getAttributes() + $request->getQueryParams()
    );
  }
}
