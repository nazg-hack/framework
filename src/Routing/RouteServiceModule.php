<?hh // strict

namespace Ytake\Adr\Routing;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Ytake\Adr\Foundation\Service;

class RouteServiceModule extends ServiceModule
{
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      \Facebook\HackRouter\BaseRouter::class, 
      $container ==> new \Ytake\Adr\Routing\Router(
        $container->get(Service::CONFIG)|>$$[Service::ROUTES]
      ),
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }
}
