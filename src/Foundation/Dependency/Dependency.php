<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Psr\Container\ContainerInterface;
use Ytake\HHContainer\FactoryContainer;
use Ytake\Adr\Foundation\Service;
use Ytake\Adr\Foundation\Dependency\DependencyInterface;

class Dependency implements DependencyInterface {
  public function __construct(protected FactoryContainer $container) {}

  public function registerConfig(array<mixed, mixed> $config): void {
    $this->container->set(Service::CONFIG, $container ==> $config);
  }

  public function register(): ContainerInterface {
    $this->container->register(ApplicationModule::class);
    $this->container->register(ActionModule::class);
    $this->container->lockModule();
    return $this->container;
  }
}
