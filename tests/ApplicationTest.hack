use type Facebook\HackTest\HackTest;
use type Nazg\Glue\{Container, ProviderInterface};
use type Ytake\Hungrr\ServerRequestFactory;
use type Facebook\Experimental\Http\Message\HTTPMethod;
use namespace HH\Lib\Experimental\IO;
use namespace Nazg\Foundation;

use function Facebook\FBExpect\expect;

final class ApplicationTest extends HackTest {

  public function testShouldReturnApplicationInstance(): void {
    list($read, $write) = IO\pipe_non_disposable();
    $container = new Container();
    $app = new Foundation\Application($container, $read, $write);
    expect($app)->toBeInstanceOf(Foundation\Application::class);
  }

  public function testShouldApplyApplication(): void {
    list($read, $write) = IO\pipe_non_disposable();
    $container = new Container();
    $app = new Foundation\Application($container, $read, $write);
    $app->build(new Foundation\ApplicationConfig());
    expect($app)->toBeInstanceOf(Foundation\Application::class);
  }

  public function testApplicationRunThrowException(): void {
    list($read, $write) = IO\pipe_non_disposable();
    $container = new Container();
    $app = new Foundation\Application($container, $read, $write);
    $app->build(new Foundation\ApplicationConfig());
    expect(() ==> $app->run(ServerRequestFactory::fromGlobals($read)))
      ->toThrow(\Facebook\HackRouter\NotFoundException::class);
  }

  public async function testShouldReturnServerResponseCaseFoundRoute(): Awaitable<void> {
    list($read, $write) = IO\pipe_non_disposable();
    await $write->writeAsync(json_encode(dict[]));
    await $write->closeAsync();
    $container = new Container();
    $app = new Foundation\Application($container, $read, $write);
    $config = new Foundation\ApplicationConfig();
    $config->setRoutes(
      dict[HTTPMethod::GET => ImmMap{
      '/' => shape(
        'middleware' => vec[],
        'named' => 'testing',
      )
    }]);
    $app->build($config);
    ob_start();
    $app->run(ServerRequestFactory::fromGlobals($read, dict[
      'REQUEST_METHOD' => 'GET',
      'REQUEST_URI' => '/'
    ]));
    $buffer = ob_get_contents();
    ob_clean();
    expect($buffer)->toBeSame('{}');
  }
}
