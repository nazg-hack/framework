<?hh // strict

namespace NazgTest\Validation;

use Nazg\Foundation\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;

final class MockValidateActionFaild extends Validator {

  protected bool $shouldThrowException = true;

  <<__Override>>
  protected function assertValidateResult(): Vector<string> {
    return new Vector(['error1']);
  }
}
