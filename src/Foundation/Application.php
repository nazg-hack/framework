<?hh // strict

namespace Ytake\Adr\Foundation;

use Facebook\HackRouter\BaseRouter;
use Ytake\Heredity\Heredity;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;
use Ytake\Adr\RequestHandler\FallbackHandler;
use Ytake\Adr\Foundation\Dependency\DependencyInterface;
use Ytake\Adr\Response;
use Ytake\Adr\Routing\HttpMethod;
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
    $appMiddleware = $this->im->concat($this->middleware())
    |>$$->concat(Set{$middleware})->toArray();
    $heredity = new Heredity(
      new MiddlewareStack(
        $appMiddleware,
        new PsrContainerResolver($container)
      ),
    );
    $response = new Response($heredity->process($serverRequest, new FallbackHandler()));
    $response->send();
  }

  public function setApplicationConfig(array<mixed, mixed> $config): void {
    $this->dependency->registerConfig($config);
    $config = $this->getContainer()->get(Service::CONFIG);
    $this->registerDependencies($config[(string) Service::MODULES]);
    $this->registerMiddlewares($config[(string) Service::MIDDLEWARES]);
    $this->dependency->register();
  }

  public function getContainer(): ContainerInterface {
    return $this->dependency->getContainer();
  }

  protected function middleware(): ImmVector<TMiddlewareClass> {
    return ImmVector{};
  }

  private function registerDependencies(array<TServiceModule> $dependecies): void {
    if($this->dependency instanceof \Ytake\Adr\Foundation\Dependency\Dependency) {
      $this->dependency->appendModules(new Vector($dependecies));
    }
  }

  private function registerMiddlewares(array<TMiddlewareClass> $middlewares): void {
    $this->im = new ImmVector($middlewares);
  }
}
