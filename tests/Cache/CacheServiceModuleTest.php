<?hh // strict

namespace NazgTest\Cache;

use type Ytake\HHContainer\FactoryContainer;
use type Nazg\Cache\Driver;
use type Nazg\Cache\CacheConfiguration;
use type Nazg\Cache\CacheServiceModule;
use type Nazg\HCache\CacheManager;
use type Nazg\HCache\CacheProvider;
use     type Nazg\HCache\Driver\{
    MapCache,
    FileSystemCache,
    ApcCache,
    MemcachedCache,
    VoidCache
};
use type PHPUnit\Framework\TestCase;

class CacheServiceModuleTest extends TestCase {

  public function testShouldReturnCacheDrivers(): void {
    $container = new FactoryContainer();
    $serviceModule = new TestCacheServiceModule();
    $serviceModule->provide($container);
    $manager = $container->get(CacheManager::class);
    $this->assertInstanceOf(CacheManager::class, $manager);
    $this->assertInstanceOf(
      FileSystemCache::class,
      $container->get(CacheProvider::class)
    );
    $container = new FactoryContainer();
    $serviceModule = new TestCacheServiceModule();
    $serviceModule->setDriver(Driver::Void);
    $serviceModule->provide($container);
    $this->assertInstanceOf(
      VoidCache::class,
      $container->get(CacheProvider::class)
    );
    $container = new FactoryContainer();
    $serviceModule = new TestCacheServiceModule();
    $serviceModule->setDriver(Driver::Apc);
    $serviceModule->provide($container);
    $this->assertInstanceOf(
      ApcCache::class,
      $container->get(CacheProvider::class)
    );
    $container = new FactoryContainer();
    $serviceModule = new TestCacheServiceModule();
    $serviceModule->setDriver(Driver::Map);
    $serviceModule->provide($container);
    $this->assertInstanceOf(
      MapCache::class,
      $container->get(CacheProvider::class)
    );
    $container = new FactoryContainer();
    $serviceModule = new TestCacheServiceModule();
    $serviceModule->setDriver(Driver::Memcached);
    $serviceModule->provide($container);
    $this->assertInstanceOf(
      MemcachedCache::class,
      $container->get(CacheProvider::class)
    );
  }
}

class TestCacheServiceModule extends CacheServiceModule {

  public function setDriver(Driver $driver): void {
    $this->defaultDriver = $driver;
  }

  <<__Override>>
  protected function cacheConfigure(FactoryContainer $_container): CacheConfiguration {
    return new CacheConfiguration(
      shape('servers' =>
        ImmVector {
          shape('host' => '127.0.0.1', 'port' => 11211)
        }
      ),
      shape('cacheStoreDir' => __DIR__),
      shape(
        'host' => '127.0.0.1',
        'port' => 6379,
        'prefix' => 'testing'
      )
    );
  }
}
