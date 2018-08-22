<?hh

namespace NazgTest\Responder;

use type Psr\Http\Message\ResponseInterface;
use type Zend\Diactoros\Response\JsonResponse;
use type NazgTest\IndexStructure;

final class IndexResponder {

  public function __construct(private IndexStructure $shape) {}

  public function response():ResponseInterface  {
    return new JsonResponse(Shapes::toArray($this->shape));
  }
}
