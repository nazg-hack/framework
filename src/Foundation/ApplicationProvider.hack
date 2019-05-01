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
namespace Nazg\Foundation;

use type Nazg\Glue\{Container, Scope};
use type Nazg\Routing\RouterProvider;
use type Facebook\HackRouter\BaseRouter;
use type Nazg\Emitter\EmitterProvider;
use type HackLogging\Logger;
use namespace Nazg\Logger;
use namespace Nazg\Exception;
use namespace Nazg\HttpExecutor\Emitter;
use namespace Nazg\Middleware;
use namespace HH\Lib\Experimental\IO;

final class ApplicationProvider extends ServiceProvider {

  public function __construct(
    protected Container $container,
    private ApplicationConfig $config,
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
  ) {
    parent::__construct($container);
  }

  <<__Override>>
  public function apply(): void {
    //
    $this->container
      ->bind(Emitter\EmitterInterface::class)
      ->provider(new EmitterProvider());
    //
    $this->container
      ->bind(Exception\ExceptionHandleInterface::class)
      ->provider(new Exception\ExceptionHandlerProvider(
        $this->readHandle,
        $this->writeHandle,
        $this->container->get(Emitter\EmitterInterface::class))
      );
    $this->container
      ->bind(Exception\ExceptionRegister::class)
      ->provider(new Exception\ExceptionRegisterProvider());
    //
    $this->container
      ->bind(ApplicationConfig::class)
      ->provider(new ApplicationConfigProvider($this->config))
      ->in(Scope::SINGLETON);
    // router
    $this->container
      ->bind(BaseRouter::class)
      ->provider(new RouterProvider());
    $this->container
      ->bind(Logger::class)
      ->provider(new Logger\LoggerProvider())
      ->in(Scope::SINGLETON);
  }
}
