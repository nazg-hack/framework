<?hh // strict

namespace Nazg\Foundation\Middleware;

use ReflectionMethod;
use Ytake\Heredity\Heredity;
use Nazg\Foundation\Validation\Attribute;
use Nazg\Foundation\Validation\Validation;
use Nazg\Foundation\Validation\ValidatorFactory;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

enum InterceptorMethod: string {
  Process = 'process';
}

class Dispatcher extends Heredity {
  
  protected int $validatorIndex = 0;
  protected ?ContainerInterface $container;

  <<__Override>>  
  protected function processor(
    MiddlewareInterface $middleware,
    ServerRequestInterface $request
  ): ResponseInterface {
    $this->findAttributse($middleware, $request);
    return $middleware->process($request, $this);
  }

  protected function findAttributse(
    MiddlewareInterface $middleware, 
    ServerRequestInterface $request
  ): void {
    $rm = new ReflectionMethod($middleware, InterceptorMethod::Process);
    $attribute = $rm->getAttribute(Attribute::Named);
    if (is_array($attribute)) {
      if(array_key_exists($this->validatorIndex, $attribute)) {
        $v = new ValidatorFactory(
          $this->container?->get((string)$attribute[$this->validatorIndex])
        );
        var_dump($v->validate());
      }
    }
  }

  public function setContainer(ContainerInterface $container): void {
    $this->container = $container;
  }
}
