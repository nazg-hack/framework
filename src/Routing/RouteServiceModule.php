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
namespace Nazg\Routing;

use type Ytake\HHContainer\ServiceModule;
use type Ytake\HHContainer\FactoryContainer;
use type Nazg\Foundation\Service;
use type Nazg\Exceptions\NotFoundHttpException;
use type Facebook\HackRouter\BaseRouter;

use function is_array;
use function array_key_exists;

class RouteServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(BaseRouter::class, 
      $container ==> new \Nazg\Routing\Router(
        $this->resolveRoutes($container),
      ),
      \Ytake\HHContainer\Scope::SINGLETON,
    );
  }

  protected function resolveRoutes(FactoryContainer $container): ImmRouteMap {
    $config = $container->get(Service::CONFIG);
    if (is_array($config)) {
      if (array_key_exists(Service::ROUTES, $config)) {
        return $config[Service::ROUTES];
      }
    }
    throw new NotFoundHttpException("No routes.");
  }
}
