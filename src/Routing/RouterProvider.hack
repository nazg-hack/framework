namespace Nazg\Routing;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Nazg\Foundation\Service;
use type Nazg\Foundation\ApplicationConfig;
use type Nazg\Exceptions\NotFoundHttpException;
use type Facebook\HackRouter\BaseRouter;
use type Facebook\Experimental\Http\Message\HTTPMethod;

final class RouterProvider
  implements ProviderInterface<BaseRouter<\Nazg\Routing\TResponder>> {

  public function get(Container $container): BaseRouter<\Nazg\Routing\TResponder> {
    return new Router($this->resolveRoutes($container));
  }

  protected function resolveRoutes(
    Container $container
  ): dict<HTTPMethod, ImmMap<string, TResponder>> {
    $config = $container->get(ApplicationConfig::class);
    return $config->getRoutes();
  }
}
