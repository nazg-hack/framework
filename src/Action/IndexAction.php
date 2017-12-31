<?hh

namespace Ytake\Adr\Action;

use Ytake\Adr\Middleware\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

final class IndexAction extends AbstractMiddleware {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    return new JsonResponse([
      'language' => 'HHVM/Hack',
      'version' => phpversion()
    ]);
  }
}
