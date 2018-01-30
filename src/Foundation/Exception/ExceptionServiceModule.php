<?hh // strict

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

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Response\Emitter;
use Nazg\Exceptions\ExceptionHandleInterface;

class ExceptionServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      ExceptionHandleInterface::class,
      $container ==> new ExceptionHandler(new Emitter()),
    );
    $container->set(
      ExceptionRegister::class,
      $container ==> new ExceptionRegister(
        $this->invariantExceptionHandler($container),
      ),
    );
  }

  protected function invariantExceptionHandler(
    FactoryContainer $container,
  ): ExceptionHandleInterface {
    $instance = $container->get(ExceptionHandleInterface::class);
    invariant(
      $instance instanceof ExceptionHandleInterface,
      "Interface '\Nazg\Exceptions\ExceptionHandleInterface' is not implemented by this class",
    );
    return $instance;
  }
}
