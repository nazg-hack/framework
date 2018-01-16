<?hh

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Nazg\Foundation\Application;
use Ytake\HHConfigAggreagator\ArrayProvider;
use Ytake\HHConfigAggreagator\ConfigAggreagator;
use Ytake\HHConfigAggreagator\PhpFileProvider;
use Zend\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase {
  
  public function testShouldReturnApplicationInstance(): void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $app = new Application(new \Nazg\Foundation\Dependency\Dependency());
    $app->setApplicationConfig($aggregator->getMergedConfig());
    $this->assertInstanceOf(Application::class, $app);
  }
  
  /**
   * @expectedException \Nazg\Foundation\Validation\ValidationException
   */
  public function testShouldBeValidationFaild():void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $app = new Application(new \Nazg\Foundation\Dependency\Dependency());
    $app->setApplicationConfig($aggregator->getMergedConfig());
    $app->run(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/validate/12',
        'REQUEST_METHOD' => 'GET'
      ],
      [
        'parameter1' => 'testing',
        'parameter2' => 'hhvm',
      ])
    );
  }
}
