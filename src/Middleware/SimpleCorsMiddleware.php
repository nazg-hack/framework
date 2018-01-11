<?hh // strict

namespace Nazg\Middleware;

use Nazg\Http\HttpMethod;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

type CorsSetting = shape('origin' => string, 'header' => string, 'methods' => Vector<HttpMethod>);

enum AccessControl: string as string {
  AllowOrigin = 'Access-Control-Allow-Origin';
  AllowHeaders = 'Access-Control-Allow-Headers';
  AllowMethods = 'Access-Control-Allow-Methods';
}

class SimpleCorsMiddleware implements MiddlewareInterface {
  
  protected string $allowOrigin = '*';
  protected string $allowHeaders = 'X-Requested-With, Content-Type, Accept, Origin, Authorization';
  protected Vector<HttpMethod> $allowMethods = Vector{
    HttpMethod::GET, 
    HttpMethod::HEAD,
    HttpMethod::POST,
  };

  public function __construct(protected CorsSetting $cors) {}

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    $response = $handler->handle($request);
    $origin = ($this->cors['origin']) ?? $this->allowOrigin;
    $header = ($this->cors['header']) ?? $this->allowHeaders;
    $methods = ($this->cors['methods']->isEmpty()) ? $this->allowMethods : $this->cors['methods'];
    return $response
            ->withHeader(AccessControl::AllowOrigin, $origin)
            ->withHeader(AccessControl::AllowHeaders, $header)
            ->withHeader(AccessControl::AllowMethods, $this->implodeMethods($methods));
  }

  protected function implodeMethods(Vector<HttpMethod> $methods): string {
    return implode(",", $methods);
  }
}
