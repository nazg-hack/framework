<?hh

namespace NazgTest;

use type Psr\Http\Server\RequestHandlerInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Message\ResponseInterface;
use type Zend\Diactoros\Response\JsonResponse;

class StubRequestHandler implements RequestHandlerInterface {

  public function handle(
    ServerRequestInterface $_request
  ): ResponseInterface {
    return new JsonResponse([]);
  }
}
