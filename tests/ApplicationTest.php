<?hh 

use PHPUnit\Framework\TestCase;
use Ytake\Adr\Foundation\Application;
use Ytake\HHConfigAggreagator\ArrayProvider;
use Ytake\HHConfigAggreagator\ConfigAggreagator;
use Ytake\HHConfigAggreagator\PhpFileProvider;
use Zend\Diactoros\ServerRequestFactory;

class ApplicationTest extends TestCase {
  
  public function testShouldReturnApplicationInstance(): void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $app = new Application(new \Ytake\Adr\Foundation\Dependency\Dependency());
    $app->setApplicationConfig($aggregator->getMergedConfig());
    $this->assertInstanceOf(Application::class, $app);
  }
}
