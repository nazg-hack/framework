<?hh // strict

namespace Nazg\Foundation\Validation;

use Psr\Http\Message\ServerRequestInterface;

class ValidatorFactory {
  
  public function __construct(
    protected Validator $validatorName,
    protected ServerRequestInterface $request
  ) {}

  public function validator(): Validator {
    $validator = $this->validatorName;
    $validator->validateRequest($this->request);
    return $validator;
  }
}
