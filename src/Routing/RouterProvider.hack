namespace Nazg\Routing;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Nazg\Foundation\Service;
use type Nazg\Foundation\ServiceConfig;
use type Nazg\Exceptions\NotFoundHttpException;
use type Facebook\HackRouter\BaseRouter;
use type Facebook\Experimental\Http\Message\HTTPMethod;

final class RouterProvider 
  implements ProviderInterface<BaseRouter<\Nazg\Routing\TResponder>> {

  public function get(Container $container): BaseRouter<\Nazg\Routing\TResponder> {
    return new Router($this->resolveRoutes());
  }

  protected function resolveRoutes(
    Container $container
  ): dict<HTTPMethod, dict<string, \Nazg\Routing\TResponder>> {
    $config = $container->get(ServiceConfig::class);
    return $config->getRoutes();
  }
}
