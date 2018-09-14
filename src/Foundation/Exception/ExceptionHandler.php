<?hh

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
namespace Nazg\Foundation\Exception;

use type Nazg\Http\StatusCode;
use type Nazg\Response\Emitter;
use type Nazg\Types\ExceptionImmMap;
use type Nazg\Exceptions\ExceptionHandleInterface;
use type Psr\Http\Message\ResponseInterface;
use type Zend\Diactoros\Response\JsonResponse;

use function call_user_func_array;
use function get_class;
use function is_array;

class ExceptionHandler implements ExceptionHandleInterface {

  public function __construct(protected Emitter $emitter) {}

  protected function render(
    ExceptionImmMap $em,
    \Throwable $_e
  ): ResponseInterface {
    return new JsonResponse(
      $em->toArray(),
      StatusCode::StatusInternalServerError,
    );
  }

  /**
   * @see https://github.com/zendframework/zend-diactoros/blob/master/doc/book/custom-responses.md
   */
  protected function respond(ExceptionImmMap $em, \Throwable $e): void {
    $this->emitter->emit($this->render($em, $e));
  }

  public function handleException(\Throwable $e): void {
    call_user_func_array([$this, 'respond'], [$this->toImmMap($e), $e]);
  }

  protected function toImmMap(\Throwable $e): ExceptionImmMap {
    return new ImmMap(
      [
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
      ],
    );
  }
}
