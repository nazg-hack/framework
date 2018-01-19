<?hh // strict

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
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      \Facebook\HackRouter\BaseRouter::class, 
      $container ==> new \Nazg\Routing\Router(
        $this->resolveRoutes($container)
      ),
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }
  
  protected function resolveRoutes(FactoryContainer $container): ImmRouteMap {
    $config = $container->get(Service::CONFIG);
    if(is_array($config)) {
      if(array_key_exists(Service::ROUTES, $config)) {
        return $config[Service::ROUTES];
      }
    }
    throw new NotFoundHttpException("No routes.");
  }
}
