<?hh // strict

namespace Ytake\Adr\Routing;

use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod as HackRouterHttpMethod;
use Interop\Http\Server\MiddlewareInterface;

type TResponder = classname<MiddlewareInterface>;

final class Router extends BaseRouter<TResponder> {

  protected function getRoutes(): ImmMap<HackRouterHttpMethod, ImmMap<string, TResponder>> {
    return ImmMap {
      HackRouterHttpMethod::GET => ImmMap {
        '/' => \Ytake\Adr\Action\IndexAction::class,
      },
    };
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
