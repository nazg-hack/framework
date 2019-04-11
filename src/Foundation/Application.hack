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

use type Nazg\Heredity\Heredity;
use type Nazg\Heredity\MiddlewareStack;
use type Nazg\RequestHandler\FallbackHandler;
use type Nazg\Foundation\Middleware\Dispatcher;
use type Nazg\Foundation\Bootstrap\BootstrapRegister;
use type Facebook\HackRouter\BaseRouter;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\RequestHandlerInterface;
use type Nazg\Glue\Container;
use type Nazg\HttpExecutor\RequestHandleExecutor;

use namespace Nazg\HttpExecutor\Emitter;
use namespace HH\Lib\Experimental\IO;
use namespace Nazg\Foundation\Middleware;
use namespace HH\Lib\Vec;


class Application {

  protected vec<classname<\Nazg\Http\Server\MiddlewareInterface>> $middlewares = vec[
    Middleware\RouteDispatchMiddleware::class,
  ];

  protected ?RequestHandlerInterface $requestHandler;

  protected ?BootstrapRegister $bootstrapRegister;

  protected bool $flag = false;

  public function __construct(
    private Container $container,
    private IO\ReadHandle $readHandle,
    private IO\WriteHandle $writeHandle,
  ) {}

  public function build(ApplicationConfig $config): this {
    $provider = new ApplicationProvider($this->container, $config, $this->readHandle, $this->writeHandle);
    $provider->apply();
    \HH\Asio\join($this->container->lockAsync());
    return $this;
  }

  public function run(
    ServerRequestInterface $serverRequest
  ): void {
    // register bootstrap for framework application
    $this->bootstrap($this->container);
    $router = $this->container->get(BaseRouter::class);
    list($middleware, $attributes) = $router->routeRequest($serverRequest);
    if ($attributes->count()) {
      $serverRequest = $serverRequest->withServerParams(dict($attributes));
    }
    $this->executor(
      $this->middlewareProcessor(
        $middleware['middleware'],
        $this->container
      ),
      $this->container->get(Emitter\EmitterInterface::class),
      $serverRequest
    )->run();
  }

  protected function executor(
    RequestHandlerInterface $handler,
    Emitter\EmitterInterface $emitter,
    ServerRequestInterface $serverRequest
  ): RequestHandleExecutor {
    return new RequestHandleExecutor(
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

  public function setRequestHandler(RequestHandlerInterface $handler): void {
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
  protected function getAppMiddleware(): vec<classname<\Nazg\Http\Server\MiddlewareInterface>> {
    return vec[];
  }

  public function setValidateAttribute(bool $flag): void {
    $this->flag = $flag;
  }

  protected function middlewareProcessor(
    vec<classname<\Nazg\Http\Server\MiddlewareInterface>> $middleware,
    Container $container,
  ): RequestHandlerInterface {
    // sync middleware
    $appMiddleware = Vec\concat(
      $this->middlewares,
      $this->getAppMiddleware(),
      $middleware
    );
    $stack = new MiddlewareStack(
      new Vector($appMiddleware),
      new Middleware\GlueResolver($container),
    );
    if ($this->flag) {
      $dispatcher = new Dispatcher($stack, $this->requestHandler ?: new FallbackHandler());
      $dispatcher->setContainer($container);
      return $dispatcher;
    }
    return new Heredity($stack, $this->requestHandler ?: new FallbackHandler());
  }
}
