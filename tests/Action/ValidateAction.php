<?hh

namespace NazgTest\Action;

use Nazg\Foundation\Validation\Attribute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use NazgTest\Validation\MockValidateActionFaild;

final class ValidateAction implements MiddlewareInterface {

  <<RequestValidation(MockValidateActionFaild::class)>>
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    return new JsonResponse([]);
  }
}
