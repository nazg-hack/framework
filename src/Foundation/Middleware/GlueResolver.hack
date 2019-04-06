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
 namespace Nazg\Foundation\Middleware;

use namespace HH\Lib\Str;
use type Nazg\Http\Server\MiddlewareInterface;
use type Nazg\Heredity\Exception\MiddlewareResolvingException;
use type Nazg\Heredity\Resolvable;
use type Nazg\Glue\Container;

class GlueResolver implements Resolvable {

  public function __construct(
    protected Container $container
  ) {}

  public function resolve(
    classname<MiddlewareInterface> $middleware
  ): MiddlewareInterface {
    if ($this->container->has($middleware)) {
      return $this->container->get($middleware);
    }
    throw new MiddlewareResolvingException(
      Str\format('Identifier "%s" is not binding.', $middleware),
    );
  }
}
