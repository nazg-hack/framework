use type Facebook\HackTest\HackTest;
use type Nazg\Glue\Container;
use type Ytake\Hungrr\ServerRequestFactory;
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

  public function testApplicationRun(): void {
    list($read, $write) = IO\pipe_non_disposable();
    $container = new Container();
    $app = new Foundation\Application($container, $read, $write);
    $app->build(new Foundation\ApplicationConfig());
    ob_start();
    /* HH_FIXME[4119] ignore types for testing */
    expect($app->run(ServerRequestFactory::fromGlobals($read)))->toBeNull();
    ob_end_clean();
  }
}
