<?hh 

use PHPUnit\Framework\TestCase;
use Ytake\Adr\Routing\Router;
use Zend\Diactoros\ServerRequestFactory;

class RouterTest extends TestCase {
  
  public function testShouldBeMatchRoute(): void {
    $router = new Router();
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/',
        'REQUEST_METHOD' => 'GET'
      ])
    );
    $this->assertInternalType('array', $match);
    $this->assertSame(\Ytake\Adr\Action\IndexAction::class, $match[0]);
  }

  /**
   * @expectedException \Facebook\HackRouter\NotFoundException
   */
  public function testShouldNotBeMAtchRoute(): void {
    $router = new Router();
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/testing',
        'REQUEST_METHOD' => 'GET'
      ])
    );
  }
}
