use type HackLogging\Logger;
use type Nazg\Glue\{Container, Scope, DependencyFactory};
use type Nazg\Heredity\AsyncMiddlewareStack;
use type Facebook\HackTest\HackTest;
use type Ytake\Hungrr\ServerRequestFactory;
use type Nazg\RequestHandler\AsyncFallbackHandler;
use type Nazg\Foundation\ApplicationConfig;
use namespace Nazg\Logger;
use namespace Nazg\Middleware;
use namespace HH\Lib\IO;
use function Facebook\FBExpect\expect;

final class SimpleCorsMiddlewareTest extends HackTest {

  private function getDependency(): Container {
    $container = new Container(new DependencyFactory());
    $container->bind(Middleware\SimpleCorsMiddleware::class)
      ->provider(new Middleware\SimpleCorsMiddlewareProvider());
    $container->bind(FakeThrowExceptionMiddleware::class)
      ->to(FakeThrowExceptionMiddleware::class);
    $container->bind(FakeActionMiddleware::class)
      ->to(FakeActionMiddleware::class);
    \HH\Asio\join($container->lockAsync());
    return $container;
  }

  public async function testShouldReturnCorsHeader(): Awaitable<void> {
    $filename = __DIR__ . '/../storages/testing.log';
    list($read, $write) = IO\pipe();
    $heredity = new Middleware\Dispatcher(
      new AsyncMiddlewareStack(
        vec[
          Middleware\SimpleCorsMiddleware::class,
          FakeActionMiddleware::class
        ],
        new Middleware\GlueResolver($this->getDependency())
      ),
      new AsyncFallbackHandler(),
    );
    $res = await $heredity->handleAsync(
      $write,
      ServerRequestFactory::fromGlobals($read)
    );
		$headers = $res->getHeaders();
		expect($headers)->toContainKey('Access-Control-Allow-Origin');
		expect($headers)->toContainKey('Access-Control-Allow-Headers');
		expect($headers)->toContainKey('Access-Control-Allow-Methods');

		expect($headers['Access-Control-Allow-Origin'])
			->toBeSame(vec['*']);
		expect($headers['Access-Control-Allow-Headers'])
			->toBeSame(vec['X-Requested-With, Content-Type, Accept, Origin, Authorization']);
		expect($headers['Access-Control-Allow-Methods'])
			->toBeSame(vec['GET,HEAD,POST']);
  }
}
