<?hh // strict

namespace Ytake\Adr\Foundation;

use Facebook\HackRouter\BaseRouter;
use Facebook\HackRouter\HttpMethod;
use Ytake\Adr\Middleware\AbstractMiddleware;

type TResponder = classname<AbstractMiddleware>;

final class Router extends BaseRouter<TResponder> {

  protected function getRoutes(): ImmMap<HttpMethod, ImmMap<string, TResponder>> {
    return ImmMap {
      HttpMethod::GET => ImmMap {
        '/' => \Ytake\Adr\Action\IndexAction::class,
      },
    };
  }
}
