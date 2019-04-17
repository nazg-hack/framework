namespace Nazg\Foundation;

use type Nazg\Glue\{Container, Scope};
use type Nazg\Routing\RouterProvider;
use type Facebook\HackRouter\BaseRouter;
use type Nazg\Foundation\Exception\ExceptionHandlerProvider;
use type Nazg\Exceptions\ExceptionHandleInterface;
use type Nazg\Foundation\Emitter\EmitterProvider;
use type HackLogging\Logger;
use namespace Nazg\Logger;
use namespace Nazg\HttpExecutor\Emitter;
use namespace Nazg\Foundation\Middleware;
use namespace HH\Lib\Experimental\IO;

class ApplicationProvider extends AggregateServiceProvider {

  public function __construct(
    protected Container $container,
    private ApplicationConfig $config,
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
  ) {
    parent::__construct($container);
  }

  <<__Override>>
  public function apply(): void {
    //
    $this->container
      ->bind(ApplicationConfig::class)
      ->provider(new ApplicationConfigProvider($this->config))
      ->in(Scope::SINGLETON);
    // router
    $this->container
      ->bind(BaseRouter::class)
      ->provider(new RouterProvider());
    $this->container
      ->bind(Logger::class)
      ->provider(new Logger\LoggerProvider())
      ->in(Scope::SINGLETON);
    //
    $this->container
      ->bind(Middleware\RouteDispatchMiddleware::class)
      ->provider(new Middleware\RouteDispatchMiddlewareProvider());
    //
    $this->container
      ->bind(Emitter\EmitterInterface::class)
      ->provider(new EmitterProvider());
    //
    $this->container
      ->bind(ExceptionHandleInterface::class)
      ->provider(new ExceptionHandlerProvider(
        $this->readHandle,
        $this->writeHandle,
        $this->container->get(Emitter\EmitterInterface::class))
      );
  }
}
