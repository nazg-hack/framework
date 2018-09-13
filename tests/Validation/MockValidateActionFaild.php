<?hh // strict

namespace NazgTest\Validation;

use type Nazg\Foundation\Validation\Validator;

final class MockValidateActionFaild extends Validator {

  protected bool $shouldThrowException = true;

  <<__Override>>
  protected function assertValidateResult(): Vector<string> {
    return new Vector(['error1']);
  }
}
