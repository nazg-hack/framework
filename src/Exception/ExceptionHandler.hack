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

use type Nazg\Http\VndErrorResponse;
use type Nazg\HttpExecutor\Emitter\EmitterInterface;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\UriInterface;
use type Ytake\Hungrr\StatusCode;
use namespace HH\Lib\{Dict, C, IO};
use function get_class;
use function is_array;
use function json_encode;

class ExceptionHandler implements ExceptionHandleInterface {

  public function __construct(
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
    protected EmitterInterface $emitter
  ) {}

  protected async function renderAsync(
    dict<arraykey, mixed> $em,
    \Exception $e
  ): Awaitable<ResponseInterface> {
    await $this->writeHandle->writeAsync(json_encode($em));
    return new VndErrorResponse(
      $this->writeHandle,
      $this->resolveStatusCode($e->getCode())
    );
  }

  protected function respond(
    dict<arraykey, mixed> $em,
    \Exception $e
  ): void {
    $this->emitter->emit(
      $this->readHandle,
      \HH\Asio\join($this->renderAsync($em, $e))
    );
  }

  public function handle(\Exception $e): void {
    $this->respond($this->resolveError($e), $e);
  }

  protected function resolveError(\Exception $e): dict<arraykey, mixed> {
    $shape = shape();
    $factory = new VndErrorFactory($e);
    if($e is AbstractVndErrorException) {
      $shape['logref'] = $e->getLogRef();
      $path = $e->getPath();
      if($path is UriInterface) {
        $shape['path'] = $path->toString();
      }
    }
    return $factory->invoke($this->dictErrors($e), $shape)->toDict();
  }

  protected function dictErrors(\Exception $e): dict<arraykey, mixed> {
    return dict[
      'exception' => get_class($e),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => Dict\map($e->getTrace(), ($v) ==> {
        if(is_array($v)) {
          if(C\contains_key($v, 'args')) {
            return Dict\filter_with_key($v, ($k, $_) ==> $k !== 'args');
          }
        }
        return $v;
      }),
    ];
  }

  protected function resolveStatusCode(
    mixed $exceptionCode
  ): StatusCode {
    $exceptionCode as int;
    try {
      return StatusCode::assert($exceptionCode);
    } catch(\UnexpectedValueException $e) {
      return StatusCode::INTERNAL_SERVER_ERROR;
    }
  }
}
