<?hh // strict

namespace Nazg\Foundation\Dependency;

use Psr\Container\ContainerInterface;

interface DependencyInterface {  

  public function registerConfig(array<mixed, mixed> $config): void;

  public function register(): void;

  public function getContainer(): ContainerInterface;
}
