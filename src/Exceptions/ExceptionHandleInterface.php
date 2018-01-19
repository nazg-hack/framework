<?hh // strict

namespace Nazg\Exceptions;

interface ExceptionHandleInterface {

  public function handleException(\Exception $e): void;
}
