<?hh // strict

namespace Nazg\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

class LogExceptionMiddleware implements MiddlewareInterface {

  public function __construct(protected LoggerInterface $log) {}

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    try {
      return $handler->handle($request);
    } catch(\Exception $e) {
      $this->log->warning($e->getMessage());
      throw $e;
    }
  }
}
