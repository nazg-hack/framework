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
namespace Nazg\Cache\Resolver;

use type Redis;
use type Nazg\Cache\RedisConfig;

class RedisResolver {

  const type T = Redis;

  public function __construct(
    protected RedisConfig $config
  ) {}

  public function provide(): this::T {
    $config = $this->config;
    $redis = new Redis();
    $r = (Redis $redis, RedisConfig $config) ==> {
      if(!Shapes::idx($config, 'persistent', false)) {
        $redis->connect(
          $config['host'],
          Shapes::idx($config, 'port', 6379),
          Shapes::idx($config, 'readTimeout', 0)
        );
      }
      $redis->pconnect(
        $config['host'],
        Shapes::idx($config, 'port', 6379),
        Shapes::idx($config, 'readTimeout', 0)
      );
      return $redis;
    };
    $redis = \call_user_func_array($r, [$redis, $config]);
    $this->redisConnectOption($redis, $config);
    return $redis;
  }

  protected function redisConnectOption(Redis $redis, RedisConfig $config): void {
    $password = Shapes::idx($config, 'password', null);
    if (!\is_null($password)) {
      $redis->auth($password);
    }
    $database = Shapes::idx($config, 'database', null);
    if (!\is_null($database)) {
      $redis->select($database);
    }
    $prefix = Shapes::idx($config, 'prefix', null);
    if (!\is_null($prefix)) {
      $redis->setOption(Redis::OPT_PREFIX, $prefix);
    }
    $timeout = Shapes::idx($config, 'readTimeout', null);
    if (!\is_null($timeout)) {
      $redis->setOption(Redis::OPT_READ_TIMEOUT, $timeout);
    }
  }
}
