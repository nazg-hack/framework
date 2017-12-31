<?hh // strict

namespace Ytake\Adr\Foundation\Dependency;

use Psr\Container\ContainerInterface;

interface DependencyInterface {  

  public function registerConfig(array<mixed, mixed> $config): void;

  public function register(): ContainerInterface;
}
