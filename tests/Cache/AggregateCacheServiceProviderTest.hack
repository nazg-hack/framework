use type Nazg\Cache\{AggregateCacheServiceProvider, Driver};
use type Nazg\Glue\{Container, ProviderInterface, Scope};
use type Nazg\Foundation\ApplicationConfig;
use type Facebook\HackTest\HackTest;
use function Facebook\FBExpect\expect;
use type Nazg\HCache\CacheManager;
use type Nazg\HCache\CacheProvider as HCacheProvider;
use Nazg\HCache\Driver\{FileSystemCache, MapCache};

final class AggregateCacheServiceProviderTest extends HackTest {

  public function testShouldReturnExpectInstance(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class);
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(CacheManager::class))
      ->toBeInstanceOf(CacheManager::class);
    expect($container->get(HCacheProvider::class))
      ->toBeInstanceOf(HCacheProvider::class);
  }

  public function testShouldReturnFileSystemCache(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class)
      ->in(Scope::SINGLETON);
    $config = $container->get(ApplicationConfig::class);
    
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(HCacheProvider::class))
      ->toBeInstanceOf(FileSystemCache::class);
  }

  public function testShouldReturn(): void {
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class)
      ->in(Scope::SINGLETON);
    $config = $container->get(ApplicationConfig::class);
    $config->setCacheDriver(Driver::Map);
    $cache = new AggregateCacheServiceProvider($container);
    $cache->apply();
    expect($container->get(HCacheProvider::class))
      ->toBeInstanceOf(MapCache::class);
  }
}
