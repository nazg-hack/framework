<?hh // strict

namespace NazgTest\Action;

use type NazgTest\Responder\IndexResponder;
use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Server\RequestHandlerInterface;

final class IndexAction implements MiddlewareInterface {
  public function process(
    ServerRequestInterface $_request,
    RequestHandlerInterface $_handler,
  ): ResponseInterface {
    $responder = new IndexResponder(
      shape(
      'language' => 'HHVM/Hack',
      'version' => \phpversion()
    ));
    return $responder->response();
  }
}
