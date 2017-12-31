<?hh

namespace Ytake\Adr\RequestHandler;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class FallbackHandler implements RequestHandlerInterface {
  public function handle(ServerRequestInterface $request): ResponseInterface {
    return new JsonResponse([]);
  }
}
