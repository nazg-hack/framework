<?hh 

use Nazg\Foundation\Validation\Attribute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

final class ValidateAction implements MiddlewareInterface {
  <<RequestValidation(\ValidateActionValid::class)>>
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    return new JsonResponse([]);
  }
}
