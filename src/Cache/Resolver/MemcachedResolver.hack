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
namespace Nazg\Cache\Resolver;

use type Memcached;
use type Nazg\Cache\MemcachedConfig;


class MemcachedResolver {

  public function __construct(
    protected MemcachedConfig $config
  ) {}

  public function provide():  Memcached {
    $config = $this->config;
    $m = new Memcached(Shapes::idx($config, 'persistentId'));
    $servers = Shapes::idx($config, 'servers');
    if($servers is nonnull) {
      foreach ($servers as $value) {
        $m->addServer($value['host'], $value['port'], Shapes::idx($value, 'weight', 0));
      }
    }
    return $m;
  }
}
