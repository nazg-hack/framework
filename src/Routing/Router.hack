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

use namespace HH\Lib\Dict;
use type Facebook\HackRouter\BaseRouter;
use type Facebook\Experimental\Http\Message\HTTPMethod;

final class Router extends BaseRouter<TResponder> {

  public function __construct(
    private dict<HTTPMethod, ImmMap<string, TResponder>> $routeMap
  ) {}

  <<__Override>>
  protected function getRoutes(
  ): ImmMap<\Facebook\HackRouter\HttpMethod, ImmMap<string, TResponder>> {
    return new ImmMap($this->dictRoutes());
  }

  <<__Rx>>
  protected function dictRoutes(): dict<\Facebook\HackRouter\HttpMethod, ImmMap<string, TResponder>> {
    return Dict\map_keys($this->routeMap, ($k) ==> {
      return $this->convertHttpMethod($k);
    });
  }

  public function findRoute(string $named): string {
    $collect = $this->collectRoutes();
    if($collect->contains($named)) {
      return $collect->at($named);
    }
    throw new Exception\NotFoundException();
  }

  <<__Memoize>>
  protected function collectRoutes(): ImmMap<string, string> {
    $map = new Map($this->routeMap);
    $i = $map->getIterator();
    $named = [];
    while ($i->valid()) {
      $current = $i->current();
      $keys = $current->keys();
      $index = 0;
      foreach($current as $method => $v) {
        if(Shapes::keyExists($v, 'named')) {
          $named[Shapes::idx($v, 'named', '')] = $keys[$index];
        }
        $index++;
      }
      $i->next();
    }
    return new ImmMap($named);
  }

  <<__Memoize, __Rx>>
  private function convertHttpMethod(
    HTTPMethod $method,
  ): \Facebook\HackRouter\HttpMethod {
    return \Facebook\HackRouter\HttpMethod::assert(
      HTTPMethod::assert($method)
    );
  }
}
