<?hh // strict

namespace NazgTest\Exception;

use type PHPUnit\Framework\TestCase;
use type Ytake\HHContainer\FactoryContainer;
use type Nazg\Response\Emitter;
use type Nazg\Exceptions\ExceptionHandleInterface;
use type Nazg\Foundation\Exception\ExceptionHandler;
use type Nazg\Foundation\Exception\ExceptionRegister;
use type Nazg\Foundation\Exception\ExceptionServiceModule;
use function Facebook\FBExpect\expect;
use type Psr\Http\Message\ResponseInterface;

class ExceptionHandlerTest extends TestCase {

  public function testShouldReturnExceptionHandlerInterface(): void {
     $container = new FactoryContainer();
     $container->registerModule(ExceptionServiceModule::class);
     $container->lockModule();
     $e = $container->get(ExceptionHandleInterface::class);
     expect($e)->toBeInstanceOf(ExceptionHandler::class);
  }

  public function testFunctionalExceptionRegister(): void {
    $emitter = new OverrideEmitter();
    $e = new ExceptionHandler($emitter);
    $register = new ExceptionRegister($e);
    $register->register();
    $e->handleException(new \Exception('Exception for testing'));
    $r = $emitter->getResponse();
    if($r instanceof ResponseInterface) {
      expect($r)->toBeInstanceOf(ResponseInterface::class);
      $d = \json_decode($r->getBody()->getContents(), true);
      expect($d['message'])->toBeSame('Exception for testing');
      expect($r->getStatusCode())->toBeSame(500);
      $this->assertArrayHasKey('exception', $d);
      $this->assertArrayHasKey('file', $d);
      $this->assertArrayHasKey('line', $d);
      $this->assertArrayHasKey('trace', $d);
    }
  }
}

class OverrideEmitter extends Emitter {

  private ?ResponseInterface $response;

  <<__Override>>
  public function emit(ResponseInterface $response): void {
    $this->response = $response;
  }

  public function getResponse(): ?ResponseInterface {
    return $this->response;
  }
}
