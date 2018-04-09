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
namespace Nazg\Cache;

use Redis;
use Memcached;

type MemcachedServer = shape(
  'host' => string,
  'port' => int,
  ?'weight' => int,
);
type FileSystemConfig = shape(
  'cacheStoreDir' => string
);

type MemcachedConfig = shape(
  'servers' => ImmVector<MemcachedServer>,
  ?'persistentId' => string,
);

type RedisConfig = shape(
  'host' => string,
  ?'port' => int,
  ?'password' => string,
  ?'prefix' => string,
  ?'readTimeout' => float,
  ?'persistent' => bool,
  ?'database' => int
);

class CacheConfiguration {

  public function __construct(
    protected ?MemcachedConfig $memcachedConfig = null,
    protected ?FileSystemConfig $filesystemConfig = null,
    protected ?RedisConfig $redisConfig = null,
  ) {}

  public function getMemcached(): ?Memcached {
    if(!is_null($this->memcachedConfig)) {
      $config = $this->memcachedConfig;
      $m = new Memcached(Shapes::idx($config, 'persistentId'));
      $servers = Shapes::idx($config, 'servers');
      if(!is_null($servers)) {
        $m->addServers($servers->toArray());
      }
      return $m;
    }
    return null;
  }

  public function getFileSystemDir(): ?string {
    if(!is_null($this->filesystemConfig)) {
      return Shapes::idx($this->filesystemConfig, 'cacheStoreDir');
    }
    return null;
  }

  public function getRedis(): ?Redis {
    if(!is_null($this->redisConfig)) {
      $config = $this->redisConfig;
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
      $redis = call_user_func_array($r, [$redis, $config]);
      $this->redisConnectOption($redis, $config);
      return $redis;
    }
    return null;
  }

  protected function redisConnectOption(Redis $redis, RedisConfig $config): void {
    $password = Shapes::idx($config, 'password', null);
    if ($password) {
      $redis->auth($password);
    }
    $database = Shapes::idx($config, 'database', null);
    if ($database) {
      $redis->select($database);
    }
    $prefix = Shapes::idx($config, 'prefix', null);
    if ($prefix) {
      $redis->setOption(Redis::OPT_PREFIX, $prefix);
    }
    $timeout = Shapes::idx($config, 'readTimeout', null);
    if ($timeout) {
      $redis->setOption(Redis::OPT_READ_TIMEOUT, $timeout);
    }
  }
}
