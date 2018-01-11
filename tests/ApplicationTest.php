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
  
  public function testG(): void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $dependency = new \Nazg\Foundation\Dependency\Dependency();
    $app = new OverrideApplication($dependency);
    $app->setApplicationConfig($aggregator->getMergedConfig());
    $app->run(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/testing/12?message=testing&server=hhvm',
        'QUERY_STRING' => 'message=testing&server=hhvm',
        'REQUEST_METHOD' => 'GET'
      ],
      [
        'message' => 'testing',
        'server' => 'hhvm',
      ])
    );
  }
}

class OverrideApplication extends Application {
  protected function send(ResponseInterface $response): void {
    $decode = json_decode($response->getBody()->getContents());
    Assert::assertObjectHasAttribute('id', $decode);
    Assert::assertObjectHasAttribute('message', $decode);
    Assert::assertObjectHasAttribute('server', $decode);
  }
}
