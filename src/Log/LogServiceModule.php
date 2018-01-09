<?hh 

namespace Nazg\Log;

use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Monolog;
use Monolog\Handler\StreamHandler;

class LogServiceModule extends ServiceModule {

  public function provide(FactoryContainer $container): void
  {
    $container->set(
      LoggerInterface::class,
      $container ==> $this->defaultLogger(),
      \Ytake\HHContainer\Scope::SINGLETON
    );
  }

  protected function defaultLogger(): LoggerInterface {
    $monolog = new Logger("Nazg.Log");
    $monolog->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
    return $monolog;
  }
}
