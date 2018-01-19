<?hh // strict

namespace Nazg\Foundation;

use Facebook\HackRouter\BaseRouter;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;
use Nazg\Http\HttpMethod;
use Nazg\Response\Emitter;
use Nazg\RequestHandler\FallbackHandler;
use Nazg\Foundation\Middleware\Dispatcher;
use Nazg\Foundation\Dependency\DependencyInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class Application {
  
  protected ImmVector<\Nazg\Types\TMiddlewareClass> $im = ImmVector{};

  protected ?RequestHandlerInterface $requestHandler;

  public function __construct(
    protected DependencyInterface $dependency
  ) {}

  public function run(
    ServerRequestInterface $serverRequest
  ): void {
    $container = $this->getContainer();
    $router = $container->get(BaseRouter::class);
    invariant(
      $router instanceof BaseRouter, 
      "%s class must extend %s", 
      get_class($router), 
      BaseRouter::class
    );
    list($middleware, $attributes) = $router->routePsr7Request($serverRequest);
    if ($attributes->count()) {
      foreach($attributes as $key => $attribute) {
        $serverRequest = $serverRequest->withAttribute($key, $attribute);
      }
    }
    $heredity = $this->middlewareProcessor($middleware, $container);
    $this->send(
      $heredity->handle(
        $this->marshalAttributes($serverRequest, $attributes)
      )
    );
  }

  protected function marshalAttributes(
    ServerRequestInterface $request, 
    ImmMap<string, string> $attributes
  ): ServerRequestInterface {
    if ($attributes->count()) {
      foreach($attributes as $key => $attribute) {
        $request = $request->withAttribute($key, $attribute);
      }
    }
    return $request;
  }

  public function setRequestHandler(RequestHandlerInterface $handler): void {
    $this->requestHandler = $handler;
  }

  public function setApplicationConfig(array<mixed, mixed> $config): void {
    $this->dependency->registerConfig($config);
    $config = $this->getContainer()->get(Service::CONFIG);
    $this->registerDependencies($config);
    $this->registerMiddlewares($config);
    $this->dependency->register();
  }

  public function getContainer(): ContainerInterface {
    return $this->dependency->getContainer();
  }
  
  /**
   * Middleware always executed by the application
   * must override application class
   *
   * <code>
   * <<__Override>> 
   * protected function middleware(): ImmVector<\Nazg\Types\TMiddlewareClass> {
   *   return ImmVector{};
   * }
   * </code>
   */
  protected function middleware(): ImmVector<\Nazg\Types\TMiddlewareClass> {
    return ImmVector{};
  }

  private function registerDependencies(mixed $config): void {
    if(is_array($config)) {
      if(array_key_exists(Service::MODULES, $config)) {
        if($this->dependency instanceof \Nazg\Foundation\Dependency\Dependency) {
          $vModule = $config[Service::MODULES];
          if ($vModule instanceof ImmVector) {
            $this->dependency->appendModules($vModule->toVector());
          }
        }
      }
    }
  }

  private function registerMiddlewares(mixed $config): void {
    if(is_array($config)) {
      if(array_key_exists(Service::MIDDLEWARES, $config)) {
        if ($config[Service::MIDDLEWARES] instanceof ImmVector) {
          $this->im = $config[Service::MIDDLEWARES];
        }
      }
    }
  }

  protected function middlewareProcessor(
    ImmVector<\Nazg\Types\TMiddlewareClass> $middleware,
    ContainerInterface $container
  ): RequestHandlerInterface {
    $appMiddleware = $this->im->concat($this->middleware())
    |>$$->concat($middleware)->toArray();
    $dispatcher = new Dispatcher(
      new MiddlewareStack(
        $appMiddleware,
        new PsrContainerResolver($container)
      ),
      $this->requestHandler ?: new FallbackHandler()
    );
    $dispatcher->setContainer($container);
    return $dispatcher;
  }
    
  protected function send(ResponseInterface $response): void {
    (new Emitter())->emit($response);
  }
}
