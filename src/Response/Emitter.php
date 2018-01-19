<?hh 

namespace Nazg\Response;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\EmitterInterface;

class Emitter {

  public function emit(ResponseInterface $response): void {
    $this->emitter()->emit($response);
  }

  public function emitter(): EmitterInterface {
    return new SapiEmitter();
  }  
}
