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
namespace Nazg\Cache;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Nazg\HCache\CacheManager;
use type Nazg\HCache\CacheProvider as HCacheProvider;
use type Nazg\HCache\Driver\{FileSystemCache, MemcachedCache};
use type Nazg\Foundation\ApplicationConfig;

class CacheProvider implements ProviderInterface<HCacheProvider> {

  protected Driver $defaultDriver = Driver::File;

  public function get(Container $container): HCacheProvider {
    $manager = $container->get(CacheManager::class);
    $config = $container->get(ApplicationConfig::class);
    $this->defaultDriver = $config->getCacheDriver();
    return $this->detectCacheProvider(
      $manager->createCache($this->defaultDriver),
      $this->cacheConfig($config)
    );
  }
  
  protected function cacheConfig(ApplicationConfig $config): CacheConfig {
    return new CacheConfig(
      $config->getMemcachedCacheConfig(),
      $config->getFilesystemCacheConfig()
    );
  }

  protected function detectCacheProvider(
    HCacheProvider $provider,
    CacheConfig $cacheConfigure
  ): HCacheProvider {
    if($this->defaultDriver === Driver::File) {
      if($provider is FileSystemCache) {
        $dir = $cacheConfigure->getFileSystemDir();
        if($dir is nonnull) {
          $provider->setDirectory($dir);
        }
        return $provider;
      }
    }
    if($this->defaultDriver === Driver::Memcached) {
      if($provider is MemcachedCache) {
        $m = $cacheConfigure->getMemcached();
        if($m is nonnull) {
          $provider->setMemcached($m);
        }
        return $provider;
      }
    }
    throw new Exception\CacheDriverNotFoundException();
  }
}
