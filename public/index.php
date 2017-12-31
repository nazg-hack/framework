<?hh

use Ytake\Adr\Foundation\Application;
use Ytake\HHConfigAggreagator\ArrayProvider;
use Ytake\HHConfigAggreagator\ConfigAggreagator;
use Ytake\HHConfigAggreagator\PhpFileProvider;
use Zend\Diactoros\ServerRequestFactory;

require __DIR__ . '/../vendor/hh_autoload.php';

call_user_func(function() {
  $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/../config/{{,*.}global,{,*.}local}.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ],
    __DIR__.'/resources/cached.config.cache.hh'
  );
  $app = new Application(
    new \Ytake\Adr\Foundation\Dependency\Dependency(
      new \Ytake\HHContainer\FactoryContainer()
    )
  );
  $app->setApplicationConfig($aggregator->getMergedConfig());
  $app->run(ServerRequestFactory::fromGlobals());
});
