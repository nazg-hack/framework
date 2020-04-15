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

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Nazg\HttpExecutor\Emitter\EmitterInterface;
use namespace HH\Lib\IO;

class ExceptionHandlerProvider implements ProviderInterface<ExceptionHandleInterface> {

  public function __construct(
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
    protected EmitterInterface $emitter
  ) {}

  public function get(
    Container $_container
  ): ExceptionHandleInterface {
    return new ExceptionHandler(
      $this->readHandle,
      $this->writeHandle,
      $this->emitter
    );
  }
}
