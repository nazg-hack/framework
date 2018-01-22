<?hh

namespace NazgTest;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Nazg\Foundation\Application;
use Ytake\HHConfigAggreagator\ArrayProvider;
use Ytake\HHConfigAggreagator\ConfigAggreagator;
use Ytake\HHConfigAggreagator\PhpFileProvider;
use Zend\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use function Facebook\FBExpect\expect;

class ApplicationTest extends TestCase {
  
  public function testShouldReturnApplicationInstance(): void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $app = new Application(new \Nazg\Foundation\Dependency\Dependency());
    $app->setApplicationConfig($aggregator->getMergedConfig());
    expect($app)->toBeInstanceOf(Application::class);
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

  public function testShouldBeOverrideResponse():void {
    $aggregator = new ConfigAggreagator([
      new PhpFileProvider(__DIR__ . '/config/*.{hh,php}'),
      new ArrayProvider(['config_cache_enabled' => false])
    ]);
    $app = new OverrideApplication(new \Nazg\Foundation\Dependency\Dependency());
    $app->setApplicationConfig($aggregator->getMergedConfig());
    $app->run(
      ServerRequestFactory::fromGlobals([
        'REQUEST_URI' => '/testing/12',
        'REQUEST_METHOD' => 'GET'
      ],
      [
        'parameter1' => 'testing',
        'parameter2' => 'hhvm',
      ])
    );
    $stream = $app->getResponse()?->getBody();
    if($stream instanceof \Psr\Http\Message\StreamInterface) {
      $expect = '{"id":"changed_value","parameter1":"testing","parameter2":"hhvm"}';
      expect($stream->getContents())->toBeSame($expect);
    }
  }
}

final class OverrideApplication extends Application {
  private ?ResponseInterface $response;
  <<__Override>>
  protected function send(ResponseInterface $response): void {
    $this->response = $response;
  }
  public function getResponse(): ?ResponseInterface {
    return $this->response;
  }
}
