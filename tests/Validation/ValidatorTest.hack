use type Nazg\Validation\Validator;
use type Nazg\Validation\ValidationException;
use type Facebook\HackTest\HackTest;
use type Ytake\Hungrr\ServerRequestFactory;
use namespace Facebook\TypeAssert;
use namespace HH\Lib\Experimental\IO;
use function Facebook\FBExpect\expect;

class ValidatorTest extends HackTest {
  public function testShouldBeValidatorFailed(): void {
    $v = new MockValidateFalied();
    expect($v->errors())->toNotBeSame(0);
    expect($v->validate())->toBeFalse();
  }

  public function testShouldThrowTypeException(): void {
    list($read, $_) = IO\pipe_non_disposable();
    $v = new MockStructureValidateFalied();
    $request = ServerRequestFactory::fromGlobals(
      $read,
      dict[
        'REQUEST_URI' => '/testing/12?message=testing&server=hhvm',
        'QUERY_STRING' => 'message=testing&server=hhvm',
        'REQUEST_METHOD' => 'GET'
      ],
      dict[],
      dict[
        'message' => 'testing',
        'server' => 'hhvm',
      ]
    );
    $v->validateRequest($request);
    expect(() ==> $v->validate())->toThrow(ValidationException::class);
  }

  public function testShouldThrow(): void {
    list($read, $_) = IO\pipe_non_disposable();
    $v = new MockStructureValidateSuccess();
    $request = ServerRequestFactory::fromGlobals(
      $read,
      dict[
        'REQUEST_URI' => '/testing/12?message=testing&server=hhvm',
        'QUERY_STRING' => 'message=testing&server=hhvm',
        'REQUEST_METHOD' => 'GET'
      ],
      dict[],
      dict[
        'message' => 'testing',
        'server' => 'hhvm',
      ]
    );
    $v->validateRequest($request);
    expect($v->validate())->toBeTrue();
  }
}

final class MockValidateFalied extends Validator {
  protected bool $shouldThrowException = false;
  <<__Override>>
  protected function assertValidateResult(): vec<string> {
    return vec['error1'];
  }
}

final class MockStructureValidateFalied extends Validator {
  const type TestRequest = shape(
    'message' => string,
    'server' => int,
  );
  protected bool $shouldThrowException = false;
  protected bool $skipValidateStructure = false;
  <<__Override>>
  protected function assertValidateResult(): vec<string> {
    return vec['error1'];
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

final class MockStructureValidateSuccess extends Validator {
  const type TestRequest = shape(
    'message' => string,
    'server' => string,
  );
  protected bool $shouldThrowException = false;
  protected bool $skipValidateStructure = false;
  <<__Override>>
  protected function assertValidateResult(): vec<string> {
    $params = $this->request?->getQueryParams();
    $v = vec[];
    if (is_array($params)) {
      if ($params['message'] !== 'testing') {
        $v[] = 'error!';
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
