<?hh // strict

namespace NazgTest\Cache;

use Nazg\Cache\CacheConfiguration;
use PHPUnit\Framework\TestCase;

class CacheConfigurationTest extends TestCase {

  public function testShouldReturnNull(): void {
    $cache = new CacheConfiguration();
    $this->assertNull($cache->getFileSystemDir());
    $this->assertNull($cache->getMemcached());
    $this->assertNull($cache->getRedis());
  }

  public function testShouldReturnFilesystemDir(): void {
    $cache = new CacheConfiguration(null, shape('cacheStoreDir' => __DIR__), null);
    $this->assertNotNull($cache->getFileSystemDir());
  }

  public function testShouldReturnMemcached(): void {
    $cache = new CacheConfiguration(shape('servers' => ImmVector {
      shape('host' => '127.0.0.1', 'port' => 11211)
    }), null, null);
    $this->assertNotNull($cache->getMemcached());
  }
}
