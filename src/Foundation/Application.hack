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

use type Facebook\HackRouter\BaseRouter;
use type Nazg\Heredity\Heredity;
use type Nazg\Heredity\{MiddlewareStack, PsrContainerResolver};
use type Nazg\Response\Emitter;
use type Nazg\RequestHandler\FallbackHandler;
use type Nazg\Foundation\Middleware\Dispatcher;
use type Nazg\Foundation\Bootstrap\BootstrapRegister;
use type Nazg\Foundation\Dependency\DependencyInterface;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\RequestHandlerInterface;
use type Nazg\Glue\Container;

use function get_class;
use function is_array;
use function array_key_exists;

class Application {

  protected ImmVector<\Nazg\Types\TMiddlewareClass> $im = ImmVector {};

  protected ?RequestHandlerInterface $requestHandler;

  protected ?BootstrapRegister $bootstrapRegister;

  protected bool $flag = false;

  public function __construct(protected DependencyInterface $dependency) {}

  public function run(
    ServerRequestInterface $serverRequest
  ): void {
    $container = $this->getContainer();
    // register bootstrap for framework application
    $this->bootstrap($container);
    $router = $container->get(BaseRouter::class);
    list($middleware, $attributes) = $router->routeRequest($serverRequest);
    if ($attributes->count()) {
      $serverRequest = $serverRequest->withServerParams(dict($attributes));
    }
    $heredity = $this->middlewareProcessor(
      $middleware['middleware'],
      $container
    );
    $this->send(
      $heredity->handle(
        $this->marshalAttributes($serverRequest, $attributes),
      ),
    );
  }

  protected function marshalAttributes(
    ServerRequestInterface $request,
    dict<string, string> $attributes,
  ): ServerRequestInterface {
    if ($attributes->count()) {
      foreach ($attributes as $key => $attribute) {
        $request = $request->withAttribute($key, $attribute);
      }
    }
    return $request;
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

  public function setApplicationConfig(array<mixed, mixed> $config): void {
    $this->dependency->registerConfig($config);
    $config = $this->getContainer()->get(Service::CONFIG);
    $this->registerDependencies($config);
    $this->registerMiddlewares($config);
    $this->dependency->register();
  }

  public function getContainer(): Container {
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
    return ImmVector {};
  }

  private function registerDependencies(mixed $config): void {
    if (is_array($config)) {
      if (array_key_exists(Service::MODULES, $config)) {
        if ($this->dependency instanceof \Nazg\Foundation\Dependency\Dependency) {
          $vModule = $config[Service::MODULES];
          if ($vModule instanceof ImmVector) {
            $this->dependency->appendModules($vModule->toVector());
          }
        }
      }
    }
  }

  private function registerMiddlewares(mixed $config): void {
    if (is_array($config)) {
      if (array_key_exists(Service::MIDDLEWARES, $config)) {
        if ($config[Service::MIDDLEWARES] instanceof ImmVector) {
          $this->im = $config[Service::MIDDLEWARES];
        }
      }
    }
  }

  public function setValidateAttribute(bool $flag): void {
    $this->flag = $flag;
  }

  protected function middlewareProcessor(
    ImmVector<\Nazg\Types\TMiddlewareClass> $middleware,
    Container $container,
  ): RequestHandlerInterface {
    $appMiddleware =
      $this->im
        ->concat($this->middleware())
        ->concat($middleware)->toArray();
    $stack = new MiddlewareStack(
      $appMiddleware,
      new PsrContainerResolver($container),
    );
    if ($this->flag) {
      $dispatcher = new Dispatcher($stack, $this->requestHandler ?: new FallbackHandler());
      $dispatcher->setContainer($container);
      return $dispatcher;
    }
    return new Heredity($stack, $this->requestHandler ?: new FallbackHandler());
  }

  protected function send(ResponseInterface $response): void {
    (new Emitter())->emit($response);
  }
}
