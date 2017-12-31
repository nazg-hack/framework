<?hh // strict

namespace Ytake\Adr\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;

type MessageRespopse = shape('language' => string, 'version' => string);

class ResponseShape extends AbstractMiddleware {
  public function process(
    ServerRequestInterface $request, 
    RequestHandlerInterface $handler
  ): ResponseInterface {
    $response = $handler->handle($request);
    return $response;
  }
}
