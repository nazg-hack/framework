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
namespace Nazg\Foundation\Middleware;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Facebook\HackRouter\BaseRouter;
use namespace HH\Lib\Experimental\IO;

class RouteDispatchMiddlewareProvider 
  implements ProviderInterface<RouteDispatchMiddleware> {

  public function get(
    Container $container
  ): RouteDispatchMiddleware {
    return new RouteDispatchMiddleware($container->get(BaseRouter::class));
  }
}
