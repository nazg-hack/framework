<?hh // strict

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
 * Copyright (c) 2017-2018 Yuuki Takezawa
 *
 */
namespace Nazg\Middleware;

use type Nazg\Http\HttpMethod;
use type Psr\Http\Message\ResponseInterface;
use type Psr\Http\Message\ServerRequestInterface;
use type Psr\Http\Server\MiddlewareInterface;
use type Psr\Http\Server\RequestHandlerInterface;

type CorsSetting = shape(
  ?'origin' => string,
  ?'header' => string,
  'methods' => Vector<HttpMethod>,
);

enum AccessControl : string as string {
  AllowOrigin = 'Access-Control-Allow-Origin';
  AllowHeaders = 'Access-Control-Allow-Headers';
  AllowMethods = 'Access-Control-Allow-Methods';
}

class SimpleCorsMiddleware implements MiddlewareInterface {

  protected string $allowOrigin = '*';
  protected string
    $allowHeaders = 'X-Requested-With, Content-Type, Accept, Origin, Authorization';
  protected Vector<HttpMethod>
    $allowMethods = Vector {
      HttpMethod::GET,
      HttpMethod::HEAD,
      HttpMethod::POST,
    };

  public function __construct(protected CorsSetting $cors) {}

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    $response = $handler->handle($request);
    $origin = ($this->cors['origin']) ?? $this->allowOrigin;
    $header = ($this->cors['header']) ?? $this->allowHeaders;
    $methods =
      ($this->cors['methods']->isEmpty())
        ? $this->allowMethods
        : $this->cors['methods'];
    return
      $response->withHeader(AccessControl::AllowOrigin, $origin)
        ->withHeader(AccessControl::AllowHeaders, $header)
        ->withHeader(
          AccessControl::AllowMethods,
          $this->implodeMethods($methods),
        );
  }

  protected function implodeMethods(Vector<HttpMethod> $methods): string {
    return \implode(",", $methods);
  }
}
