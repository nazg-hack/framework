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

type ImmRouteMap = ImmMap<HttpMethod, ImmMap<string, TResponder>>;
type MiddlewareVector = ImmVector<classname<MiddlewareInterface>>;
type TResponder = shape(
  'middleware' => MiddlewareVector,
  ?'named' => string,
);

final class Router extends BaseRouter<TResponder> {

  public function __construct(private ImmRouteMap $routeMap) {}

  <<__Override>>
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

  public function findRoute(string $named): ?string {
    $collect = $this->collectRoutes();
    if($collect->contains($named)) {
      return $collect->get($named);
    }
    return null;
  }
  
  <<__Memoize>>
  protected function collectRoutes(): ImmMap<?string, ?string> {
    $i = $this->routeMap->getIterator();
    $named = [];
    while ($i->valid()) {
      $current = $i->current();
      $keys = $current->keys();
      $index = 0;
      foreach($current as $method => $v) {
        if(Shapes::keyExists($v, 'named')) {
          $named[Shapes::idx($v, 'named')] = $keys[$index];
        }
        $index++;
      }
      $i->next();
    }
    return new ImmMap($named);
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
