<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

class ActionModule extends ServiceModule
{
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      \Ytake\Adr\Action\IndexAction::class, 
      $container ==> new \Ytake\Adr\Action\IndexAction()
    );
  }
}
