<?hh

namespace NazgTest\Action;

use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Server\RequestHandlerInterface;
use type Zend\Diactoros\Response\JsonResponse;
use type NazgTest\Validation\MockValidateActionFaild;

final class ValidateAction implements MiddlewareInterface {

  <<RequestValidation(MockValidateActionFaild::class)>>
  public function process(
    ServerRequestInterface $_request,
    RequestHandlerInterface $_handler,
  ): ResponseInterface {
    return new JsonResponse([]);
  }
}
