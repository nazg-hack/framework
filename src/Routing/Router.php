<?hh // strict

namespace Ytake\Adr\Routing;

use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod as HackRouterHttpMethod;
use Interop\Http\Server\MiddlewareInterface;

type TResponder = classname<MiddlewareInterface>;
type ImmRouteMap = ImmMap<HttpMethod, ImmMap<string, TResponder>>;

final class Router extends BaseRouter<TResponder> {
  
  public function __construct(private ImmRouteMap $routeMap) {}

  protected function getRoutes(): ImmMap<HackRouterHttpMethod, ImmMap<string, TResponder>> {
    $i = $this->routeMap->getIterator();
    $map = [];
    while($i->valid()) {
      $map[$this->convertHttpMethod($i->key())] = $i->current();
      $i->next();
    }
    return new ImmMap($map);
  }

  private function convertHttpMethod(HttpMethod $method): HackRouterHttpMethod {
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
