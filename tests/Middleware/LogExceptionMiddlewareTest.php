<?hh

namespace NazgTest\Middleware;

use type Nazg\Middleware\LogExceptionMiddleware;
use type PHPUnit\Framework\TestCase;
use type Ytake\HHContainer\FactoryContainer;
use type Psr\Log\LoggerInterface;
use type Zend\Diactoros\ServerRequestFactory;
use type Nazg\Heredity\MiddlewareStack;
use type Nazg\Heredity\PsrContainerResolver;
use type Nazg\Foundation\Middleware\Dispatcher;
use type NazgTest\StubRequestHandler;

class LogExceptionMiddlewareTest extends TestCase {
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
    $container->registerModule(OverrideLogServiceModule::class);
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
