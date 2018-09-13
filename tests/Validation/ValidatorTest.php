<?hh

namespace NazgTest\Validation;

use namespace Facebook\TypeAssert;
use type PHPUnit\Framework\TestCase;
use type Nazg\Foundation\Validation\Validator;
use type Zend\Diactoros\ServerRequestFactory;

class ValidatorTest extends TestCase {

  public function testShouldBeValidatorFailed(): void {
    $v = new MockValidateFalied();
    $this->assertNotCount(0, $v->errors());
    $this->assertFalse($v->validate());
  }

  /**
   * @expectedException \Facebook\TypeAssert\IncorrectTypeException
   */
  public function testShouldThrowTypeException(): void {
    $v = new MockStructureValidateFalied();
    $request = ServerRequestFactory::fromGlobals([
      'REQUEST_URI' => '/testing/12?message=testing&server=hhvm',
      'QUERY_STRING' => 'message=testing&server=hhvm',
      'REQUEST_METHOD' => 'GET'
    ],
    [
      'message' => 'testing',
      'server' => 'hhvm',
    ]);
    $v->validateRequest($request);
    $v->validate();
  }

  public function testShouldThrow(): void {
    $v = new MockStructureValidateSuccess();
    $request = ServerRequestFactory::fromGlobals([
      'REQUEST_URI' => '/testing/12?message=testing&server=hhvm',
      'QUERY_STRING' => 'message=testing&server=hhvm',
      'REQUEST_METHOD' => 'GET'
    ],
    [
      'message' => 'testing',
      'server' => 'hhvm',
    ]);
    $v->validateRequest($request);
    $this->assertTrue($v->validate());
  }
}

class MockValidateFalied extends Validator {

  protected bool $shouldThrowException = false;

  <<__Override>>
  protected function assertValidateResult(): Vector<string> {
    return new Vector(['error1']);
  }
}

class MockStructureValidateFalied extends Validator {

  const type TestRequest = shape(
    'message' => string,
    'server' => int,
  );

  protected bool $shouldThrowException = false;
  protected bool $skipValidateStructure = false;
  <<__Override>>
  protected function assertValidateResult(): Vector<string> {
    return new Vector(['error1']);
  }

  <<__Override>>
  protected function assertStructure(): void {
    if(!$this->skipValidateStructure) {
      TypeAssert\matches_type_structure(
        type_structure(self::class, 'TestRequest'),
        $this->request?->getQueryParams(),
      );
    }
  }
}

class MockStructureValidateSuccess extends Validator {

  const type TestRequest = shape(
    'message' => string,
    'server' => string,
  );

  protected bool $shouldThrowException = false;
  protected bool $skipValidateStructure = false;
  <<__Override>>
  protected function assertValidateResult(): Vector<string> {
    $params = $this->request?->getQueryParams();
    $v = Vector{};
    if (is_array($params)) {
      if ($params['message'] !== 'testing') {
        $v = $v->concat(['error!']);
      }
    }
    return $v;
  }

  <<__Override>>
  protected function assertStructure(): void {
    if(!$this->skipValidateStructure) {
      TypeAssert\matches_type_structure(
        type_structure(self::class, 'TestRequest'),
        $this->request?->getQueryParams(),
      );
    }
  }
}
