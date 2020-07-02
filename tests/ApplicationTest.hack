use type Facebook\HackTest\HackTest;
use type Nazg\Glue\{Container, DependencyFactory};
use type Ytake\Hungrr\ServerRequestFactory;
use type Facebook\Experimental\Http\Message\HTTPMethod;
use namespace HH\Lib\IO;
use namespace Nazg\Foundation;
use function Facebook\FBExpect\expect;

final class ApplicationTest extends HackTest {

  public function testShouldReturnApplicationInstance(): void {
    list($read, $write) = IO\pipe_nd();
    $container = new Container(new DependencyFactory());
    $app = new Foundation\Application($container, $read, $write);
    expect($app)->toBeInstanceOf(Foundation\Application::class);
  }

  public function testShouldApplyApplication(): void {
    list($read, $write) = IO\pipe_nd();
    $container = new Container(new DependencyFactory());
    $app = new Foundation\Application($container, $read, $write);
    $app->build(new Foundation\ApplicationConfig());
    expect($app)->toBeInstanceOf(Foundation\Application::class);
  }

  public function testApplicationRunThrowException(): void {
    list($read, $write) = IO\pipe_nd();
    $container = new Container(new DependencyFactory());
    $app = new Foundation\Application($container, $read, $write);
    $app->build(new Foundation\ApplicationConfig());
    expect(async () ==> await $app->runAsync(ServerRequestFactory::fromGlobals($read)))
      ->toThrow(\Facebook\HackRouter\NotFoundException::class);
  }

  public async function testShouldReturnServerResponseCaseFoundRoute(): Awaitable<void> {
    list($read, $write) = IO\pipe_nd();
    await $write->writeAsync(json_encode(dict[]));
    await $write->closeAsync();
    $container = new Container(new DependencyFactory());
    $app = new Foundation\Application($container, $read, $write);
    $config = new Foundation\ApplicationConfig();
    await $config->setRoutesAsync(
      dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      )
    }]);
    $app->build($config);
    ob_start();
    await $app->runAsync(ServerRequestFactory::fromGlobals($read, dict[
      'REQUEST_METHOD' => 'GET',
      'REQUEST_URI' => '/'
    ]));
    $buffer = ob_get_contents();
    ob_clean();
    expect($buffer)->toBeSame('{}');
  }
}
