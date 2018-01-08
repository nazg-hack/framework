<?hh 

use PHPUnit\Framework\TestCase;
use Nazg\Response\Response;
use Zend\Diactoros\Response\JsonResponse;

class ResponseTest extends TestCase {

  public function testShouldReturnResponse(): void {
    $response = new Response(new JsonResponse(['message' => 'testing']));
    $this->assertInstanceOf(Response::class, $response);
  }
}
