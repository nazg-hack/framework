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
 * Copyright (c) 2017-2018 Yuuki Takezawa
 *
 */
namespace Nazg\Exception;

use type Nazg\Types\ExceptionImmMap;
use type Nazg\HttpExecutor\Emitter\EmitterInterface;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Ytake\Hungrr\StatusCode;
use type Ytake\Hungrr\Response\JsonResponse;
use namespace HH\Lib\Experimental\IO;

use function get_class;
use function is_array;
use function HH\Lib\Vec\map;
use function json_encode;

class ExceptionHandler implements ExceptionHandleInterface {

  public function __construct(
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
    protected EmitterInterface $emitter
  ) {}

  protected async function renderAsync(
    dict<string, mixed> $em,
    \Throwable $_e
  ): Awaitable<ResponseInterface> {
    await $this->writeHandle->writeAsync(json_encode($em));
    return new JsonResponse(
      $this->writeHandle,
      StatusCode::INTERNAL_SERVER_ERROR,
    );
  }

  protected function respond(
    dict<string, mixed> $em,
    \Throwable $e
  ): void {
    $this->emitter->emit(
      $this->readHandle,
      \HH\Asio\join($this->renderAsync($em, $e))
    );
  }

  public function handleException(\Throwable $e): void {
    $this->respond($this->dictErrors($e), $e);
  }

  protected function dictErrors(\Throwable $e): dict<string, mixed> {
    return dict[
      'message' => $e->getMessage(),
      'exception' => get_class($e),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => map(
        $e->getTrace(),
        $v ==> {
          if(is_array($v)) {
            return (new Map($v))->removeKey('args')->toArray();
          }
          return [];
        }
      ),
    ];
  }
}
