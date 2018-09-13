<?hh // strict

namespace Nazg\Foundation\Middleware;

use type ReflectionMethod;
use type Nazg\Heredity\Heredity;
use type Nazg\Foundation\Validation\Attribute;
use type Nazg\Foundation\Validation\Validator;
use type Nazg\Foundation\Validation\ValidatorFactory;
use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Container\ContainerInterface;
use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function array_key_exists;

enum InterceptorMethod : string {
  Process = 'process';
}

class Dispatcher extends Heredity {

  protected int $validatorIndex = 0;
  protected ?ContainerInterface $container;

  <<__Override>>
  protected function processor(
    MiddlewareInterface $middleware,
    ServerRequestInterface $request,
  ): ResponseInterface {
    $this->validateInterceptor($middleware, $request);
    return $middleware->process($request, $this);
  }

  protected function validateInterceptor(
    MiddlewareInterface $middleware,
    ServerRequestInterface $request,
  ): void {
    $rm = new ReflectionMethod($middleware, InterceptorMethod::Process);
    $attribute = $rm->getAttribute(Attribute::Named);
    if (is_array($attribute)) {
      if (array_key_exists($this->validatorIndex, $attribute)) {
        $validator =
          $this->container?->get((string) $attribute[$this->validatorIndex]);
        if ($validator instanceof Validator) {
          $factory = new ValidatorFactory($validator, $request);
          $factory->validator()->validate();
        }
      }
    }
  }

  public function setContainer(ContainerInterface $container): void {
    $this->container = $container;
  }
}
