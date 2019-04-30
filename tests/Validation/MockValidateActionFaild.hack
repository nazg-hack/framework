use type Nazg\Validation\Validator;

final class MockValidateActionFaild extends Validator {
  protected bool $shouldThrowException = true;

  <<__Override>>
  protected function assertValidateResult(): vec<string> {
    return vec['error1'];
  }
}
