<?hh // strict

use Ytake\HHContainer\FactoryContainer;
use Ytake\HHContainer\ServiceModule;

class TestingServiceModule extends ServiceModule {
  public function provide(FactoryContainer $container): void {
    $container->set(stdClass::class, $container ==> new stdClass());
    $container->set(
      \IndexAction::class, 
      $container ==> new \IndexAction()
    );
  }
}
