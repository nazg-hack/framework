<?hh 

namespace NazgTest\Middleware;

use Nazg\Log\LogServiceModule;
use Nazg\Middleware\LogExceptionMiddleware;
use PHPUnit\Framework\TestCase;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\ServerRequestFactory;
use Nazg\Heredity\MiddlewareStack;
use Nazg\Heredity\PsrContainerResolver;
use Nazg\Foundation\Middleware\Dispatcher;
use NazgTest\StubRequestHandler;

class LogExceptionMiddlewareTest extends TEstCase {
  /**
   * @expectedException \Exception
   */
  public function testShouldThrowException(): void {
    $container = $this->getDependencyContainer();
    $heredity = new Dispatcher(
      new MiddlewareStack(
        [LogExceptionMiddleware::class, FakeThrowExceptionMiddleware::class],
        new PsrContainerResolver($container),
      ),
      new StubRequestHandler(),
    );
    $response = $heredity->handle(
      ServerRequestFactory::fromGlobals()
    );
  }
  
  /**
   * @depends testShouldThrowException
   */
  public function testShouldCreateLogFile(): void {
    $this->assertFileExists(OverrideLogServiceModule::LOG_FILE);
    unlink(OverrideLogServiceModule::LOG_FILE);
  }
  
  private function getDependencyContainer(): FactoryContainer {
    $container = new FactoryContainer();
    $container->register(OverrideLogServiceModule::class);
    $container->set(
      LogExceptionMiddleware::class,
      $container ==> new LogExceptionMiddleware($this->resolveLogger($container)),
    );
    $container->set(
      FakeThrowExceptionMiddleware::class,
      $container ==> new FakeThrowExceptionMiddleware(),
    );
    $container->lockModule();
    return $container;
  }

  private function resolveLogger(FactoryContainer $container): LoggerInterface {
    $instance = $container->get(LoggerInterface::class);
    if($instance instanceof LoggerInterface) {
      return $instance;
    }
    return new \Monolog\Logger("Nazg.Log");
  }
}
