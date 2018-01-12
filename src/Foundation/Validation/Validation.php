<?hh // strict

namespace Nazg\Foundation\Validation;

use Psr\Http\Message\ServerRequestInterface;

enum Attribute: string as string {
    Named = 'RequestValidation';
}

<<__ConsistentConstruct>>
abstract class Validation {
  
  public function __construct(
    protected ServerRequestInterface $request
  ) {}

  public function validate(): bool {
    return true;
  }
}
