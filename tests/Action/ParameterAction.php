<?hh 

namespace NazgTest\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

final class ParameterAction implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    return new JsonResponse(
      $request->getAttributes() + $request->getQueryParams()
    );
  }
}
