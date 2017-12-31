<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

class MiddlewareModule extends ServiceModule
{
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      \Ytake\Adr\Middleware\ResponseShape::class, 
      $container ==> new \Ytake\Adr\Middleware\ResponseShape()
    );
  }
}
