<?hh

namespace Nazg\Foundation;

use Facebook\HackRouter\BaseRouter;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;
use Nazg\Http\HttpMethod;
use Nazg\RequestHandler\FallbackHandler;
use Nazg\Foundation\Middleware\Dispatcher;
use Nazg\Foundation\Dependency\DependencyInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Response\EmitterInterface;

type TMiddlewareClass = classname<MiddlewareInterface>;
type TServiceModule = classname<\Ytake\HHContainer\ServiceModule>;

class Application {
  
  protected ImmVector<TMiddlewareClass> $im = ImmVector{};

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

  protected function middleware(): ImmVector<TMiddlewareClass> {
    return ImmVector{};
  }

  private function registerDependencies(mixed $config): void {
    if(is_array($config)) {
      if(array_key_exists(Service::MODULES, $config)) {
        if($this->dependency instanceof \Nazg\Foundation\Dependency\Dependency) {
          $this->dependency->appendModules(new Vector($config[Service::MODULES]));
        }
      }
    }
  }

  private function registerMiddlewares(mixed $config): void {
    if(is_array($config)) {
      if(array_key_exists(Service::MIDDLEWARES, $config)) {
        $this->im = new ImmVector($config[Service::MIDDLEWARES]);
      }
    }
  }

  protected function middlewareProcessor(
    TMiddlewareClass $middleware,
    ContainerInterface $container
  ): RequestHandlerInterface {
    $appMiddleware = $this->im->concat($this->middleware())
    |>$$->concat(Set{$middleware})->toArray();
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
    $this->emitter()->emit($response);
  }

  protected function emitter(): EmitterInterface {
    return new SapiEmitter();
  }
}
