/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2017-2019 Yuuki Takezawa
 *
 */
namespace Nazg\Middleware;

use type HackLogging\Logger;
use type HackLogging\LogLevel;
use type HH\Lib\IO\CloseableWriteHandle;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\AsyncMiddlewareInterface;
use type Nazg\Http\Server\AsyncRequestHandlerInterface;

class LogExceptionMiddleware implements AsyncMiddlewareInterface {

  public function __construct(
    protected Logger $log
  ) {}

  public async function processAsync(
    CloseableWriteHandle $writeHandle,
    ServerRequestInterface $request,
    AsyncRequestHandlerInterface $handler,
  ): Awaitable<ResponseInterface> {
    try {
      return await $handler->handleAsync($writeHandle, $request);
    } catch (\Exception $e) {
      await $this->log->writeAsync(
        LogLevel::DEBUG,
        $e->getMessage(),
        dict[
          'file' => $e->getFile(),
          'line' => $e->getLine(),
          'trace' => $e->getTraceAsString(),
        ],
      );
      throw $e;
    }
  }
}
