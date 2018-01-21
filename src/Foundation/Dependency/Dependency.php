<?hh // strict

namespace Nazg\Foundation\Dependency;

use Psr\Container\ContainerInterface;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Foundation\Service;
use Nazg\Foundation\Dependency\DependencyInterface;
use Nazg\Routing\RouteServiceModule;

class Dependency implements DependencyInterface {

  protected FactoryContainer $container;

  protected Vector<\Nazg\Types\TServiceModule>
    $modules = Vector {RouteServiceModule::class};

  public function __construct() {
    $this->container = new \Ytake\HHContainer\FactoryContainer();
  }

  public function registerConfig(array<mixed, mixed> $config): void {
    $this->container->set(
      Service::CONFIG,
      $container ==> $config,
      \Ytake\HHContainer\Scope::SINGLETON,
    );
  }

  protected function registerServiceModule(): void {
    foreach ($this->modules->getIterator() as $i) {
      $this->container->register($i);
    }
    $this->container->lockModule();
  }

  public function register(): void {
    $this->registerServiceModule();
  }

  public function appendModules(
    Vector<\Nazg\Types\TServiceModule> $modules,
  ): void {
    $this->modules->addAll($modules);
  }

  public function getContainer(): ContainerInterface {
    return $this->container;
  }
}
