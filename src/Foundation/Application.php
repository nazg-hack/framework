<?hh // strict

namespace Ytake\Adr\Foundation;

use Facebook\HackRouter\BaseRouter;
use Ytake\Heredity\Heredity;
use Ytake\Heredity\MiddlewareStack;
use Ytake\Heredity\PsrContainerResolver;
use Ytake\Adr\RequestHandler\FallbackHandler;
use Ytake\Adr\Foundation\Dependency\DependencyInterface;
use Ytake\Adr\Response;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class Application {

  public function __construct(
    protected DependencyInterface $dependency
  ) {}

  public function run(
    ServerRequestInterface $serverRequest
  ): void {
    $container = $this->registerContainer();
    $router = $container->get(BaseRouter::class);
    invariant(
      $router instanceof BaseRouter, 
      "%s class must extend %s", 
      get_class($router), 
      BaseRouter::class
    );
    list($middleware, $path) = $router->routePsr7Request($serverRequest);    
    $heredity = new Heredity(
      new MiddlewareStack(
        $this->middleware()->concat(Set{$middleware})->toArray(),
        new PsrContainerResolver($container)
      ),
    );
    $response = new Response($heredity->process($serverRequest, new FallbackHandler()));
    $response->send();
  }

  public function setApplicationConfig(array<mixed, mixed> $config): void {
    $this->dependency->registerConfig($config);
  }

  protected function registerContainer(): ContainerInterface {
    return $this->dependency->register();
  }

  protected function middleware(): ImmVector<string> {
    return ImmVector{};
  }
}
