<?hh // strict

namespace Nazg\Foundation;

use Facebook\HackRouter\BaseRouter;
use Ytake\Heredity\Heredity;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;
use Nazg\RequestHandler\FallbackHandler;
use Nazg\Foundation\Dependency\DependencyInterface;
use Nazg\Response\Response;
use Nazg\Routing\HttpMethod;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

type TMiddlewareClass = classname<MiddlewareInterface>;
type TServiceModule = classname<\Ytake\HHContainer\ServiceModule>;

class Application {
  
  protected ImmVector<TMiddlewareClass> $im = ImmVector{};

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
    list($middleware, $path) = $router->routePsr7Request($serverRequest);
    $heredity = $this->middlewareProcessor($middleware, $container);
    $response = new Response($heredity->process($serverRequest, new FallbackHandler()));
    $response->send();
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
  ): MiddlewareInterface {
    $appMiddleware = $this->im->concat($this->middleware())
    |>$$->concat(Set{$middleware})->toArray();
    return new Heredity(
      new MiddlewareStack(
        $appMiddleware,
        new PsrContainerResolver($container)
      ),
    );    
  }
}
