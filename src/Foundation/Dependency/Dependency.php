<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Psr\Container\ContainerInterface;
use Ytake\HHContainer\FactoryContainer;
use Ytake\Adr\Foundation\Service;
use Ytake\Adr\Foundation\Dependency\DependencyInterface;
use Ytake\Adr\Routing\RouteServiceModule;

type TServiceModule = classname<\Ytake\HHContainer\ServiceModule>;

class Dependency implements DependencyInterface {
  
  protected FactoryContainer $container;
  
  protected Vector<TServiceModule> $modules = Vector{
    RouteServiceModule::class,
  };

  public function __construct() {
    $this->container = new \Ytake\HHContainer\FactoryContainer();
  }

  public function registerConfig(array<mixed, mixed> $config): void {
    $this->container->set(
      Service::CONFIG, 
      $container ==> $config,
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }

  protected function registerServiceModule(): void {
    foreach($this->modules->getIterator() as $i) {
      $this->container->register($i);
    }
    $this->container->lockModule();
  }
  
  public function register(): void {
    $this->registerServiceModule();
  }

  public function appendModules(Vector<TServiceModule> $modules): void {
    $this->modules->addAll($modules);
  }

  public function getContainer(): ContainerInterface {
    return $this->container;
  }
}
