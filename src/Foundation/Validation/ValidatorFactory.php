<?hh // strict

namespace Nazg\Foundation\Validation;

class ValidatorFactory {
  
  public function __construct(
    protected mixed $validatorName
  ) {}

  public function validate(): bool {
    if($this->validatorName instanceof Validation) {
      return $this->validatorName->validate();
    }
    return true;
  }
}
