/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2017-2019 Yuuki Takezawa
 *
 */
namespace Nazg\Foundation;

use type Nazg\Heredity\AsyncHeredity;
use type Nazg\Heredity\AsyncMiddlewareStack;
use type Nazg\RequestHandler\AsyncFallbackHandler;
use type Nazg\Foundation\Bootstrap\BootstrapRegister;
use type Facebook\HackRouter\BaseRouter;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\AsyncRequestHandlerInterface;
use type Nazg\Glue\Container;
use type Nazg\HttpExecutor\AsyncRequestHandleExecutor;

use namespace Nazg\HttpExecutor\Emitter;
use namespace HH\Lib\IO;
use namespace Nazg\Middleware;
use namespace HH\Lib\Vec;

<<__ConsistentConstruct>>
class Application {

  protected vec<classname<\Nazg\Http\Server\AsyncMiddlewareInterface>> $middlewares = vec[

  ];

  protected vec<classname<\Nazg\Foundation\ConsistentServiceProvider>> $appProviders = vec[
    \Nazg\Cache\AggregateCacheServiceProvider::class,
  ];

  protected ?AsyncRequestHandlerInterface $requestHandler;

  protected ?BootstrapRegister $bootstrapRegister;

  protected bool $attributeValidation = false;

  public function __construct(
    private Container $container,
    private IO\ReadHandle $readHandle,
    private IO\CloseableWriteHandle $writeHandle,
  ) {}

  public function build(ApplicationConfig $config): this {
    $provider = new ApplicationProvider(
      $this->container,
      $config,
      $this->readHandle,
      $this->writeHandle
    );
    $provider->apply();
    $this->middlewares = Vec\concat(
      $this->middlewares,
      $config->getApplicationGlobalMiddlewares()
    );
    $this->registerDependency($config);
    \HH\Asio\join($this->container->lockAsync());
    return $this;
  }

  protected function registerDependency(ApplicationConfig $config): void {
    $providers = $config->getServiceProviders();
    foreach(Vec\concat($this->appProviders, $providers) as $provider) {
      (new $provider($this->container))->apply();
    }
  }

  public async function runAsync(
    ServerRequestInterface $serverRequest
  ): Awaitable<void> {
    // register bootstrap for framework application
    $this->bootstrap($this->container);
    $router = $this->container->get(BaseRouter::class);
    list($middleware, $attributes) = $router->routeRequest($serverRequest);
    if ($attributes->count()) {
      $serverRequest = $serverRequest->withServerParams(dict($attributes));
    }
    await $this->executor(
      $this->middlewareProcessor(
        $middleware['middleware'],
        $this->container
      ),
      $this->container->get(Emitter\EmitterInterface::class),
      $serverRequest
    )->runAsync();
  }

  protected function executor(
    AsyncRequestHandlerInterface $handler,
    Emitter\EmitterInterface $emitter,
    ServerRequestInterface $serverRequest
  ): AsyncRequestHandleExecutor {
    return new AsyncRequestHandleExecutor(
      $this->readHandle,
      $this->writeHandle,
      $handler,
      $emitter,
      $serverRequest
    );
  }

  private function bootstrap(Container $container): void {
    $bootstrap = $this->bootstrapRegister ?: new BootstrapRegister($container);
    $bootstrap->register();
  }

  public function setBootstrap(BootstrapRegister $br): void {
    $this->bootstrapRegister = $br;
  }

  public function setRequestHandler(
    AsyncRequestHandlerInterface $handler
  ): void {
    $this->requestHandler = $handler;
  }

  /**
   * Middleware always executed by the application
   * must override application class
   *
   * <code>
   * <<__Override>>
   * protected function middleware(): vec<\Nazg\Types\TMiddlewareClass> {
   *   return vec[];
   * }
   * </code>
   */
  protected function getAppMiddleware(): vec<classname<\Nazg\Http\Server\AsyncMiddlewareInterface>> {
    return vec[];
  }

  public function setValidateAttribute(bool $attributeValidation): void {
    $this->attributeValidation = $attributeValidation;
  }

  protected function middlewareProcessor(
    vec<classname<\Nazg\Http\Server\AsyncMiddlewareInterface>> $middleware,
    Container $container,
  ): AsyncRequestHandlerInterface {
    // sync middleware
    $appMiddleware = Vec\concat(
      $this->middlewares,
      $this->getAppMiddleware(),
      $middleware
    );
    $stack = new AsyncMiddlewareStack(
      $appMiddleware,
      new Middleware\GlueResolver($container),
    );
    if ($this->attributeValidation) {
      $dispatcher = new Middleware\Dispatcher($stack, $this->getRequestHandler());
      $dispatcher->setContainer($container);
      return $dispatcher;
    }
    return new AsyncHeredity($stack, $this->getRequestHandler());
  }

  public function getContainer(): Container {
    return $this->container;
  }

  public function getRequestHandler(): AsyncRequestHandlerInterface {
    return $this->requestHandler ?: new AsyncFallbackHandler();
  }
}
