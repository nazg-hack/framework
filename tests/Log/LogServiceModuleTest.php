<?hh

namespace NazgTest\Middleware;

use type Nazg\Log\LogServiceModule;
use type PHPUnit\Framework\TestCase;
use type Ytake\HHContainer\FactoryContainer;
use type Psr\Log\LoggerInterface;

class LogServiceModuleTest extends TestCase {

  public function testShouldBeImplLoggerInsntace(): void {
     $container = new FactoryContainer();
     $container->registerModule(LogServiceModule::class);
     $container->lockModule();
     $logger = $container->get(LoggerInterface::class);
     $this->assertInstanceOf(\Monolog\Logger::class, $logger);
  }
}
