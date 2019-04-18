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
 namespace Nazg\Middleware;

use type HH\Lib\Experimental\IO\WriteHandle;
use type Nazg\Heredity\Heredity;
use type Nazg\Glue\Container;
use type Nazg\Http\Server\MiddlewareInterface;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use namespace Nazg\Validation;

class Dispatcher extends Heredity {

  const string InterceptorMethod = 'process';
  protected ?Container $container;

  <<__Override>>
  protected function processor(
    WriteHandle $writeHandle,
    MiddlewareInterface $middleware,
    ServerRequestInterface $request
  ): ResponseInterface {
    $this->validateInterceptor($middleware, $request);
    return $middleware->process($writeHandle, $request, $this);
  }

  protected function validateInterceptor(
    MiddlewareInterface $middleware,
    ServerRequestInterface $request,
  ): void {
    $container = $this->container;
    if ($container is nonnull) {
      $rm = new \ReflectionMethod($middleware, self::InterceptorMethod);
      $attribute = $rm->getAttributeClass(Validation\RequestValidation::class);
      if ($attribute is nonnull) {
        $container->get($attribute->validationClass)
        |> new Validation\ValidatorFactory($$, $request)
        |> $$->validator()->validate();
      }
    }
  }

  public function setContainer(Container $container): void {
    $this->container = $container;
  }
}
