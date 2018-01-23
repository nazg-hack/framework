<?hh // strict

namespace Nazg\Foundation\Middleware;

use ReflectionMethod;
use Nazg\Heredity\Heredity;
use Nazg\Foundation\Validation\Attribute;
use Nazg\Foundation\Validation\Validator;
use Nazg\Foundation\Validation\ValidatorFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
