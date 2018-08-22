<?hh // strict

namespace NazgTest\Routing;

use type PHPUnit\Framework\TestCase;
use type Nazg\Routing\RouteServiceModule;
use type Ytake\HHContainer\ServiceModule;
use type Ytake\HHContainer\FactoryContainer;

class RouterServiceModlueTest extends TestCase {

  public function testShouldBeMatchRoute(): void {
    $container = new FactoryContainer();
    $serviceModule = new RouteServiceModule();
    $serviceModule->provide($container);
    // \var_dump($container->get(\Facebook\HackRouter\BaseRouter::class));
    $module = new \NazgTest\TestingServiceModule();
    $module->provide($container);
    \var_dump($container->get(\NazgTest\Action\IndexAction::class));
  }
}
