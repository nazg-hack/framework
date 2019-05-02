use type HH\Lib\Experimental\IO\WriteHandle;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\AsyncMiddlewareInterface;
use type Nazg\Http\Server\AsyncRequestHandlerInterface;

final class FakeThrowExceptionMiddleware implements AsyncMiddlewareInterface {

  public async function processAsync(
    WriteHandle $writeHandle,
    ServerRequestInterface $request,
    AsyncRequestHandlerInterface $handler,
  ): Awaitable<ResponseInterface> {
    throw new \Exception("error");
    return $handler->handleAsync($writeHandle, $request);
  }
}
