use type HH\Lib\Experimental\IO\WriteHandle;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\MiddlewareInterface;
use type Nazg\Http\Server\RequestHandlerInterface;

final class FakeThrowExceptionMiddleware implements MiddlewareInterface {

  public function process(
    WriteHandle $writeHandle,
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    throw new \Exception("error");
    return $handler->handle($writeHandle, $request);
  }
}
