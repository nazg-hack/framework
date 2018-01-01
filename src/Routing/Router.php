<?hh // strict

namespace Ytake\Adr\Routing;

use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod;
use Interop\Http\Server\MiddlewareInterface;

type TResponder = classname<MiddlewareInterface>;

final class Router extends BaseRouter<TResponder> {

  protected function getRoutes(): ImmMap<HttpMethod, ImmMap<string, TResponder>> {
    return ImmMap {
      HttpMethod::GET => ImmMap {
        '/' => \Ytake\Adr\Action\IndexAction::class,
      },
    };
  }
}
