<?hh // strict

namespace Nazg\Foundation\Validation;

use Psr\Http\Message\ServerRequestInterface;
use Facebook\TypeAssert;

enum Attribute: string as string {
  Named = 'RequestValidation';
}

<<__ConsistentConstruct>>
abstract class Validator {

  protected ?ServerRequestInterface $request;
  
  protected Vector<string> $messages = Vector{};

  protected Vector<string> $validateMethods = Vector{};

  protected bool $shouldThrowException = false;

  // disabled type assert for request parameters 
  protected bool $skipValidateStructure = true; 

  public function validateRequest(ServerRequestInterface $request): void {
    $this->request = $request;
  }

  public function validate(): bool {
    if (!is_null($this->request)) {
      $this->assertStructure(); 
    }
    if($this->errors()->count()) {
      if ($this->shouldThrowException) {
        throw new ValidationException($this);
      }
      return false;
    }
    return true;
  }
  
  <<__Memoize>>
  public function errors(): ImmVector<string> {
    return $this->assertValidateResult()->immutable();
  }

  protected function assertStructure(): void {
    if(!$this->skipValidateStructure) {
      // here
    }
  }

  abstract protected function assertValidateResult(): Vector<string>;
}
