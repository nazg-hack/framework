<?hh // strict

use PHPUnit\Framework\TestCase;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Response\Emitter;
use Nazg\Exceptions\ExceptionHandleInterface;
use Nazg\Foundation\Exception\ExceptionHandler;
use Nazg\Foundation\Exception\ExceptionRegister;
use Nazg\Foundation\Exception\ExceptionServiceModule;
use function Facebook\FBExpect\expect;
use Psr\Http\Message\ResponseInterface;

class ExceptionHandlerTest extends TestCase {

  public function testShouldReturnExceptionHandlerInterface(): void {
     $container = new FactoryContainer();
     $container->register(ExceptionServiceModule::class);
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
      $d = json_decode($r->getBody()->getContents(), true);
      expect($d['message'])->toBeSame('Exception for testing');
      expect($r->getStatusCode())->toBeSame(500);
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
