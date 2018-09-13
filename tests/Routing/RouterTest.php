<?hh

namespace NazgTest\Routing;

use type PHPUnit\Framework\TestCase;
use type Nazg\Routing\Router;
use type Nazg\Http\HttpMethod;
use type Zend\Diactoros\ServerRequestFactory;

class RouterTest extends TestCase {

  public function testShouldBeMatchRoute(): void {
    $router = new Router(ImmMap{
      HttpMethod::GET => ImmMap {
        '/' => shape(
            'middleware' => ImmVector{
              \NazgTest\Action\IndexAction::class
            },
          )
        },
    });
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/',
        'REQUEST_METHOD' => 'GET'
      ])
    );
    $this->assertInternalType('array', $match);
    $this->assertContains(\NazgTest\Action\IndexAction::class, $match[0]['middleware'][0]);
  }

  /**
   * @expectedException \Facebook\HackRouter\NotFoundException
   */
  public function testShouldNotBeMatchRoute(): void {
    $router = new Router(ImmMap{
      HttpMethod::GET => ImmMap {
        '/' => shape(
          'middleware' => ImmVector{\NazgTest\Action\IndexAction::class},
        )
      },
    });
    $match = $router->routePsr7Request(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/testing',
        'REQUEST_METHOD' => 'GET'
      ])
    );
  }

  public function testShouldFindRoute(): void {
    $router = new Router(ImmMap{
      HttpMethod::GET => ImmMap {
        '/' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
          'named' => 'home'
        ),
        '/testing' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
          'named' => 'home.testing'
        ),
        '/noname' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
        ),
        '/testing/two' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
          'named' => 'home.testing.two'
        ),
        '/noname/two' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
        ),
        '/noname/three' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
        ),
        '/testing/three' => shape(
          'middleware' => ImmVector{
            \NazgTest\Action\IndexAction::class
          },
          'named' => 'home.testing.three'
        ),
      },
    });
    $this->assertNull($router->findRoute('hello'));
    $this->assertSame('/testing/two', $router->findRoute('home.testing.two'));
    $this->assertSame('/testing', $router->findRoute('home.testing'));
    $this->assertSame('/', $router->findRoute('home'));
    $this->assertSame('/testing/three', $router->findRoute('home.testing.three'));
  }
}
