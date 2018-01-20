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
namespace Nazg\Log;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Monolog;
use Monolog\Handler\StreamHandler;

class LogServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      LoggerInterface::class,
      $container ==> $this->defaultLogger(),
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }

  protected function defaultLogger(): LoggerInterface {
    $monolog = new Logger("Nazg.Log");
    $monolog->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
    return $monolog;
  }
}
