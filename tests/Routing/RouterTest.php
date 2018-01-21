<?hh 

use PHPUnit\Framework\TestCase;
use Nazg\Routing\Router;
use Nazg\Foundation\Service;
use Nazg\Http\HttpMethod;
use Zend\Diactoros\ServerRequestFactory;

class RouterTest extends TestCase {
  
  public function testShouldBeMatchRoute(): void {
    $router = new Router(ImmMap{
      HttpMethod::GET => ImmMap {
        '/' => ImmVector{
          IndexAction::class
        },
      },
    });
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/',
        'REQUEST_METHOD' => 'GET'
      ])
    );
    $this->assertInternalType('array', $match);
    $this->assertContains(\IndexAction::class, $match[0]);
  }

  /**
   * @expectedException \Facebook\HackRouter\NotFoundException
   */
  public function testShouldNotBeMatchRoute(): void {
    $router = new Router(ImmMap{
      HttpMethod::GET => ImmMap {
        '/' => ImmVector{IndexAction::class},
      },
    });
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/testing',
        'REQUEST_METHOD' => 'GET'
      ])
    );
  }
}
