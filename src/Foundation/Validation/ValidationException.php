<?hh // strict

namespace Nazg\Foundation\Validation;

use Exception;

class ValidationException extends Exception {

  protected int $status = 400;

  protected Validator $validator;

  public function __construct(Validator $validator) {
      parent::__construct('The given data was invalid.');
      $this->validator = $validator;
  }

  public function errors(): array<string> {
     return $this->validator->errors()->toArray();
  }
}
