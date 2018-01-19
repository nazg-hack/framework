<?hh // strict

namespace Nazg\Foundation\Exception;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Exceptions\ExceptionHandler;
use Nazg\Exceptions\ExceptionHandleInterface;

class ExceptionServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(
    FactoryContainer $container
  ): void {
    $container->set(
      ExceptionHandleInterface::class, 
      $container ==> new ExceptionHandler(),
    );
  }
}
