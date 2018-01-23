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

use Nazg\Http\HttpMethod;
use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod as HackRouterHttpMethod;
use Psr\Http\Server\MiddlewareInterface;

type TResponder = ImmVector<classname<MiddlewareInterface>>;
type ImmRouteMap = ImmMap<HttpMethod, ImmMap<string, TResponder>>;

final class Router extends BaseRouter<TResponder> {

  public function __construct(private ImmRouteMap $routeMap) {}

  protected function getRoutes(
  ): ImmMap<HackRouterHttpMethod, ImmMap<string, TResponder>> {
    $i = $this->routeMap->getIterator();
    $map = [];
    while ($i->valid()) {
      $map[$this->convertHttpMethod($i->key())] = $i->current();
      $i->next();
    }
    return new ImmMap($map);
  }

  private function convertHttpMethod(
    HttpMethod $method,
  ): HackRouterHttpMethod {
    switch (HttpMethod::assert($method)) {
      case HttpMethod::GET:
        return HackRouterHttpMethod::GET;
      case HttpMethod::HEAD:
        return HackRouterHttpMethod::HEAD;
      default:
        return HackRouterHttpMethod::POST;
    }
  }
}
