use type HackLogging\Logger;
use type Nazg\Glue\{Container, Scope};
use type Nazg\Heredity\MiddlewareStack;
use type Facebook\HackTest\HackTest;
use type Ytake\Hungrr\ServerRequestFactory;
use type Nazg\RequestHandler\FallbackHandler;
use type Nazg\Foundation\ApplicationConfig;
use namespace Nazg\Logger;
use namespace Nazg\Middleware;
use namespace HH\Lib\Experimental\IO;
use function Facebook\FBExpect\expect;

final class LogExceptionMiddlewareTest extends HackTest {

  private function getDependency(): Container {
    $container = new Container();
    $container->bind(Logger::class)
      ->provider(new Logger\LoggerProvider())
      ->in(Scope::SINGLETON);
    $container->bind(Middleware\LogExceptionMiddleware::class)
      ->provider(new Middleware\LogExceptionMiddlewareProvider());
    $container->bind(ApplicationConfig::class)
      ->to(LogExceptionMiddlewareTestConfig::class)
      ->in(Scope::SINGLETON);
    $container->bind(FakeThrowExceptionMiddleware::class)
      ->to(FakeThrowExceptionMiddleware::class);
    \HH\Asio\join($container->lockAsync());
    return $container;
  }

  public function testShouldPutTestingLog(): void {
    $filename = __DIR__ . '/../storages/testing.log';
    list($read, $write) = IO\pipe_non_disposable();
    $heredity = new Middleware\Dispatcher(
      new MiddlewareStack(
        Vector{
          Middleware\LogExceptionMiddleware::class,
          FakeThrowExceptionMiddleware::class
        },
        new Middleware\GlueResolver($this->getDependency())
      ),
      new FallbackHandler(),
    );
    expect(() ==> {
      $heredity->handle(
        $write,
        ServerRequestFactory::fromGlobals($read)
      );
    })->toThrow(\Exception::class);
    expect(file_exists($filename))->toBeTrue();
    unlink($filename);
  }
}

final class LogExceptionMiddlewareTestConfig extends ApplicationConfig {
  protected shape('logfile' => string, 'logname' => string) $logfile = shape(
    'logfile' => __DIR__ . '/../storages/testing.log',
    'logname' => 'testing'
  );
}
