<?hh

namespace NazgTest\Responder;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;
use NazgTest\IndexStructure;

final class IndexResponder {

  public function __construct(private IndexStructure $shape) {}

  public function response():ResponseInterface  {
    return new JsonResponse(Shapes::toArray($this->shape));
  }  
}
