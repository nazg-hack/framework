<?hh 

namespace NazgTest\Middleware;

use Nazg\Http\HttpMethod;
use Nazg\Middleware\SimpleCorsMiddleware;
use Nazg\Middleware\AccessControl;
use PHPUnit\Framework\TestCase;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Zend\Diactoros\ServerRequestFactory;
use Nazg\Heredity\MiddlewareStack;
use Nazg\Heredity\PsrContainerResolver;
use Nazg\Foundation\Middleware\Dispatcher;

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
