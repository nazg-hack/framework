namespace Nazg\Foundation;

use type Nazg\Glue\{Container, ProviderInterface};
use type Nazg\Routing\RouterProvider;
use type Facebook\HackRouter\BaseRouter;
use type Nazg\Foundation\Exception\ExceptionHandlerProvider;
use type Nazg\Exceptions\ExceptionHandleInterface;
use type Nazg\HttpExecutor\Emitter\SapiEmitter;
use namespace HH\Lib\Experimental\IO;

class ApplicationProvider {

  public function __construct(
    private Container $container,
    private ApplicationConfig $config,
    protected IO\ReadHandle $readHandle,
    protected IO\WriteHandle $writeHandle,
  ) {}

  public function apply(): void {
    //
    $this->container
      ->bind(ApplicationConfig::class)
      ->provider(new ApplicationConfigProvider($this->config));

    //
    $this->container
      ->bind(BaseRouter::class)
      ->provider(new RouterProvider());
    
    // 
    $this->container
      ->bind(ExceptionHandleInterface::class)
      ->provider(new ExceptionHandlerProvider($this->readHandle, $this->writeHandle, new SapiEmitter()));
  }
}
