<?hh // strict

namespace NazgTest\Action;

use NazgTest\Responder\IndexResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

final class IndexAction implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    $responder = new IndexResponder(
      shape(
      'language' => 'HHVM/Hack',
      'version' => phpversion()
    ));
    return $responder->response();
  }
}
