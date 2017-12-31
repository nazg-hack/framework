<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

class ApplicationModule extends ServiceModule
{
  public function provide(FactoryContainer $container): void
  {
    $container->set(
      \Facebook\HackRouter\BaseRouter::class, 
      $container ==> new \Ytake\Adr\Foundation\Router(),
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }
}
