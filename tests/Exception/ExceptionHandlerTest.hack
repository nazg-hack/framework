use type Nazg\Glue\{Container, DependencyFactory};
use type Facebook\HackTest\HackTest;
use function Facebook\FBExpect\expect;
use type Nazg\Exception\ExceptionHandleInterface;
use type Nazg\Exception\ExceptionHandler;
use type Nazg\Exception\ExceptionRegister;
use type Nazg\Exception\ExceptionHandlerProvider;
use type Nazg\Exception\NotFoundHttpException;
use namespace HH\Lib\IO;
use namespace Nazg\HttpExecutor\Emitter;

final class ExceptionHandlerTest extends HackTest {

  public function testShouldReturnExceptionHandlerInterface(): void {
    $container = new Container(new DependencyFactory());
    list($read, $write) = IO\pipe();
    $container->bind(ExceptionHandleInterface::class)
      ->provider(new ExceptionHandlerProvider($read, $write, new Emitter\SapiEmitter()));
    \HH\Asio\join($container->lockAsync());
     $e = $container->get(ExceptionHandleInterface::class);
     expect($e)->toBeInstanceOf(ExceptionHandler::class);
  }

  public function testFunctionalExceptionRegister(): void {
    list($read, $write) = IO\pipe();
    $e = new ExceptionHandler($read, $write, new Emitter\SapiEmitter());
    $register = new ExceptionRegister($e);
    $register->register();
    ob_start();
    $e->handle(new \Exception('Exception for testing'));
    $buffer = ob_get_contents();
    ob_clean();
    $d = \json_decode($buffer, true);
    expect($d['message'])->toBeSame('Exception for testing');
    expect($d)->toContainKey('exception');
    expect($d)->toContainKey('file');
    expect($d)->toContainKey('line');
    expect($d)->toContainKey('trace');
  }

  public function testFunctionalThrowNotFoundHttpException(): void {
    list($read, $write) = IO\pipe();
    $e = new ExceptionHandler($read, $write, new Emitter\SapiEmitter());
    $register = new ExceptionRegister($e);
    $register->register();
    ob_start();
    $e->handle(new NotFoundHttpException('Exception for testing'));
    $buffer = ob_get_contents();
    ob_clean();
    $d = \json_decode($buffer, true);
    expect($d['message'])->toBeSame('Exception for testing');
    expect($d)->toContainKey('exception');
    expect($d)->toContainKey('file');
    expect($d)->toContainKey('line');
    expect($d)->toContainKey('trace');
    expect($d)->toContainKey('logref');
  }
}
