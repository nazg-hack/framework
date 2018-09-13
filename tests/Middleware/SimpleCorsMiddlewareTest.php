<?hh

namespace NazgTest\Middleware;

use type Nazg\Http\HttpMethod;
use type Nazg\Middleware\SimpleCorsMiddleware;
use type Nazg\Middleware\AccessControl;
use type PHPUnit\Framework\TestCase;
use type Ytake\HHContainer\FactoryContainer;
use type Zend\Diactoros\ServerRequestFactory;
use type Nazg\Heredity\MiddlewareStack;
use type Nazg\Heredity\PsrContainerResolver;
use type Nazg\Foundation\Middleware\Dispatcher;

class SimpleCorsMiddlewareTest extends TestCase {

  public function testShouldThrowException(): void {
    $container = $this->getDependencyContainer();
    $heredity = new Dispatcher(
      new MiddlewareStack(
        [SimpleCorsMiddleware::class],
        new PsrContainerResolver($container),
      ),
      new \NazgTest\StubRequestHandler(),
    );
    $response = $heredity->handle(
      ServerRequestFactory::fromGlobals(),
    );
    $headers = $response->getHeaders();
    $this->assertArrayHasKey(AccessControl::AllowHeaders, $headers);
    $this->assertArrayHasKey(AccessControl::AllowMethods, $headers);
    $this->assertArrayHasKey(AccessControl::AllowOrigin, $headers);
  }

  private function getDependencyContainer(): FactoryContainer {
    $container = new FactoryContainer();
    $container->set(
      SimpleCorsMiddleware::class,
      $container ==> new SimpleCorsMiddleware(
        shape(
          'origin' => '*',
          'header' => 'testing',
          'methods' => Vector{
            HttpMethod::GET
          }
        )
      )
    );
    $container->lockModule();
    return $container;
  }
}
