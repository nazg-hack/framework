<?hh 

use Nazg\Routing\HttpMethod;
use Nazg\Middleware\SimpleCorsMiddleware;
use Nazg\Middleware\AccessControl;
use PHPUnit\Framework\TestCase;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Zend\Diactoros\ServerRequestFactory;
use Ytake\Heredity\Heredity;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;

class SimpleCorsMiddlewareTest extends TestCase {
  
  public function testShouldThrowException(): void {
    $container = $this->getDependencyContainer();
    $heredity = new Heredity(
      new MiddlewareStack(
        [SimpleCorsMiddleware::class],
        new PsrContainerResolver($container),
      ),
    );
    $response = $heredity->process(
      ServerRequestFactory::fromGlobals(),
      new StubRequestHandler(),
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
