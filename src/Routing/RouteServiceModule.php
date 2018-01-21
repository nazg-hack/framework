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

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Foundation\Service;
use Nazg\Routing\HttpMethod;
use Nazg\Exceptions\NotFoundHttpException;
use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod as HackRouterHttpMethod;
use Interop\Http\Server\MiddlewareInterface;

class RouteServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      \Facebook\HackRouter\BaseRouter::class,
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
