<?hh

namespace NazgTest;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class StubRequestHandler implements RequestHandlerInterface {

  public function handle(
    ServerRequestInterface $request
  ): ResponseInterface {
    return new JsonResponse([]);
  }
}
