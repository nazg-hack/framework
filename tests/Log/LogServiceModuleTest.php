<?hh

namespace NazgTest\Middleware;

use Nazg\Log\LogServiceModule;
use PHPUnit\Framework\TestCase;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Psr\Log\LoggerInterface;

class LogServiceModuleTest extends TestCase {

  public function testShouldBeImplLoggerInsntace(): void {
     $container = new FactoryContainer();
     $container->register(LogServiceModule::class);
     $container->lockModule();
     $logger = $container->get(LoggerInterface::class);
     $this->assertInstanceOf(\Monolog\Logger::class, $logger);
  }
}
