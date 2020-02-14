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
 * Copyright (c) 2017-2020 Yuuki Takezawa
 *
 */
namespace Nazg\Middleware;

use type Facebook\HackRouter\HttpMethod;
use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;

class SimpleCorsMiddlewareProvider implements ProviderInterface<SimpleCorsMiddleware> {

  public function get(
    Container $container
  ): SimpleCorsMiddleware {
    return new SimpleCorsMiddleware(shape(
        'methods' => Vector {
          HttpMethod::GET,
          HttpMethod::HEAD,
          HttpMethod::POST,
        },
      )
    );
  }
}
