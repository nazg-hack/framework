<?hh

namespace Ytake\Adr\Responder;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;
use Ytake\Adr\Struct\IndexStructure;

final class IndexResponder {

  public function __construct(private IndexStructure $shape) {}

  public function response():ResponseInterface  {
    return new JsonResponse(Shapes::toArray($this->shape));
  }  
}
