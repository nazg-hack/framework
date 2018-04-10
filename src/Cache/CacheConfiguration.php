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
use Nazg\Cache\Resolver\{
  MemcachedResolver,
  RedisResolver
};

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
      $resolver = new MemcachedResolver($this->memcachedConfig);
      return $resolver->provide();
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
      $resolver = new RedisResolver($this->redisConfig);
      return $resolver->provide();
    }
    return null;
  }
}
