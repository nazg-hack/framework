use type Nazg\Cache\{AggregateCacheServiceProvider, Driver};
use type Nazg\Glue\{Container, Scope};
use type Nazg\Foundation\ApplicationConfig;
use type Facebook\HackTest\HackTest;
use function Facebook\FBExpect\expect;
use type Nazg\HCache\CacheManager;
use type Nazg\HCache\Driver\{FileSystemCache, MapCache};

final class AggregateCacheServiceProviderTest extends HackTest {

  public function testShouldReturnExpectInstance(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class);
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(CacheManager::class))
      ->toBeInstanceOf(CacheManager::class);
    expect($container->get(\Nazg\HCache\CacheProvider::class))
      ->toBeInstanceOf(\Nazg\HCache\CacheProvider::class);
  }

  public function testShouldReturnFileSystemCache(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class)
      ->in(Scope::SINGLETON);
    $config = $container->get(ApplicationConfig::class);
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(\Nazg\HCache\CacheProvider::class))
      ->toBeInstanceOf(FileSystemCache::class);
  }

  public function testShouldReturn(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(AggregateCacheTestConfig::class)
      ->in(Scope::SINGLETON);
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(\Nazg\HCache\CacheProvider::class))
      ->toBeInstanceOf(MapCache::class);
  }
}

final class AggregateCacheTestConfig extends ApplicationConfig {
  protected Driver $cacheDriver = Driver::Map;
}
