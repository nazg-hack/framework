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

use Memcached;
use Redis;
use Nazg\Cache\CacheConfiguration;
use Nazg\HCache\CacheManager;
use Nazg\HCache\CacheProvider;
use Nazg\HCache\Driver\{
    MapCache,
    FileSystemCache,
    ApcCache,
    MemcachedCache,
    RedisCache,
    VoidCache
};
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

abstract class CacheServiceModule extends ServiceModule {

  protected Driver $defaultDriver = Driver::File;

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      CacheManager::class,
      $container ==> new CacheManager(),
      \Ytake\HHContainer\Scope::SINGLETON,
    );

    $container->set(
      CacheProvider::class,
      $container ==> {
        $manager = $container->get(CacheManager::class);
        if($manager instanceof CacheManager) {
          return $this->detectCacheProvider(
            $manager->createCache($this->defaultDriver),
            $this->cacheConfigure($container)
          );
        }
        throw new \RuntimeException("Failed to resolve " . CacheProvider::class);
      },
      \Ytake\HHContainer\Scope::SINGLETON,
    );
  }

  abstract protected function cacheConfigure(
    FactoryContainer $container
  ): CacheConfiguration;

  protected function detectCacheProvider(
    ?CacheProvider $provider, 
    CacheConfiguration $cacheConfigure
  ): CacheProvider {
    invariant($provider instanceof CacheProvider, "provider type error");
    if($this->defaultDriver === Driver::File) {
      if($provider instanceof FileSystemCache) {
        $dir = $cacheConfigure->getFileSystemDir();
        if(!\is_null($dir)) {
          $provider->setDirectory($dir);
        }
      }
    }
    if($this->defaultDriver === Driver::Memcached) {
      if($provider instanceof MemcachedCache) {
        $m = $cacheConfigure->getMemcached();
        if(!\is_null($m)) {
          $provider->setMemcached($m);
        }
      }
    }
    if($this->defaultDriver === Driver::Redis) {
      if($provider instanceof RedisCache) {
        $r = $cacheConfigure->getRedis();
        if(!\is_null($r)) {
          $provider->setRedis($r);
        }
      }
    }
    return $provider;
  }
}
