use type Facebook\HackTest\HackTest;
use type Nazg\Routing\Router;
use type Ytake\Hungrr\ServerRequestFactory;
use type Facebook\HackRouter\HttpMethod as HackRouterHttpMethod;
use type Facebook\Experimental\Http\Message\HTTPMethod;
use type Facebook\HackRouter\BaseRouter;

use namespace HH\Lib\Experimental\IO;

use function Facebook\FBExpect\expect;

final class RouterTest extends HackTest {

  public function testShouldReturnRouterInstance(): void {
    $router = new Router(dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      ),
    }]);
    expect($router)->toBeInstanceOf(BaseRouter::class);
    $router = new Router(dict[]);
    expect($router)->toBeInstanceOf(BaseRouter::class);
  }

  public function testShouldReturnNullRoute(): void {
    $router = new Router(dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      ),
    }]);
    expect($router->findRoute('noname'))->toBeNull();
  }

  public function testShouldFindRoute(): void {
    $router = new Router(dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      ),
    }]);
    expect($router->findRoute('testing'))->toBeSame('/');
  }

  public function testShouldDetectRoute(): void {
    $router = new Router(dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      ),
    }]);
    $result = $router->routeMethodAndPath(HackRouterHttpMethod::GET, '/');
    expect($result)->toNotBeEmpty();
    expect($result[0]['middleware'])->toBeSame(vec[]);
    expect(Shapes::keyExists($result[0], 'named'))->toNotBeNull();
  }

  public function testShouldDetectRouteByRequest(): void {
    list($read, $_) = IO\pipe_non_disposable();
    $request = ServerRequestFactory::fromGlobals($read, dict[
      'REQUEST_METHOD' => 'GET',
      'REQUEST_URI' => '/testing?param=testing'
    ], dict[], dict['param' => 'testing']);
    $router = new Router(dict[HTTPMethod::GET => ImmMap{
      '/{message}' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      ),
    }]);
    $result = $router->routeRequest($request);
    expect($result)->toNotBeEmpty();
    expect($result[0]['middleware'])->toBeSame(vec[]);
    expect(Shapes::keyExists($result[0], 'named'))->toNotBeNull();
  }

  public function testShouldNotFoundRouteByRequest(): void {
    list($read, $_) = IO\pipe_non_disposable();
    $request = ServerRequestFactory::fromGlobals($read, dict[
      'REQUEST_METHOD' => 'GET',
      'REQUEST_URI' => '/testing'
    ]);
    $router = new Router(dict[]);
    expect(() ==> $router->routeRequest($request))
      ->toThrow(\Facebook\HackRouter\NotFoundException::class);
  }
}
