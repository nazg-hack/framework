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

use type Nazg\Http\Server\AsyncMiddlewareInterface;
use type Facebook\Experimental\Http\Message\HTTPMethod;
use namespace Nazg\Cache;

/**
 * Framework Configure Class
 */
<<__ConsistentConstruct>>
class ApplicationConfig implements ConfigInterface {

  use Cache\CacheConfigTrait,
      GlueConfigTrait;

  protected dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes = dict[];
  protected shape('logfile' => string, 'logname' => string) $logfile = shape(
    'logfile' => '',
    'logname' => 'nazg'
  );
  protected vec<classname<ConsistentServiceProvider>> $providers = vec[];
  protected vec<classname<AsyncMiddlewareInterface>> $middlewares = vec[];

  public function setRoutes(
    dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes
  ): void {
    $this->routes = $routes;
  }

  public function getRoutes(): dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> {
    return $this->routes;
  }

  public function setLogConfig(shape('logfile' => string, 'logname' => string) $logConfig): void {
    $this->logfile = $logConfig;
  }

  public function getLogConfig(): shape('logfile' => string, 'logname' => string) {
    return $this->logfile;
  }

  public function setServiceProviders(
    vec<classname<ConsistentServiceProvider>> $providers
  ): void {
    $this->providers = $providers;
  }

  public function getServiceProviders(): vec<classname<ConsistentServiceProvider>> {
    return $this->providers;
  }

  public function setMiddlewares(
    vec<classname<AsyncMiddlewareInterface>> $middlewares
  ): void {
    $this->middlewares = $middlewares;
  }

  public function getMiddlewares(): vec<classname<AsyncMiddlewareInterface>> {
    return $this->middlewares;
  }
}
