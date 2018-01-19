<?hh // strict

namespace Nazg\Exceptions;

class ExceptionRegister {

  public function __construct(
    protected ExceptionHandleInterface $handler
  ) {}

  public function register(): void {
    set_exception_handler([$this->handler, 'handleException']);
  }
}
