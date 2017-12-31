<?hh

namespace Ytake\Adr\Response;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\EmitterInterface;

class HttpResponse {
  
  public function __construct(protected ResponseInterface $response) {}

  public function send(): void {
    $this->emitter()->emit($this->response);
  }

  protected function emitter(): EmitterInterface {
    return new SapiEmitter();
  }
}
