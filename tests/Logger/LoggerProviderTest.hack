use type Nazg\Logger\LoggerProvider;
use type Nazg\Glue\Container;
use type Nazg\Foundation\ApplicationConfig;
use type HackLogging\Logger;
use type Facebook\HackTest\HackTest;
use namespace HH\Lib\Experimental\Filesystem;
use function Facebook\FBExpect\expect;

final class LoggerProviderTest extends HackTest {

  public function testShouldThrowFileOpenException(): void {
    $logger = new LoggerProvider();
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(ApplicationConfig::class);
    expect(() ==> $logger->get($container))
      ->toThrow(Filesystem\FileOpenException::class);
  }

  public function testShouldReturnLoggerInstance(): void {
    $logger = new LoggerProvider();
    $container = new Container();
    $container->bind(ApplicationConfig::class)
      ->to(LoggerProviderTestConfig::class);
    expect($logger->get($container))
      ->toBeInstanceOf(Logger::class);
  }
}

final class LoggerProviderTestConfig extends ApplicationConfig {
  <<__Override>>
  public function getLogConfig(): shape('logfile' => string, 'logname' => string) {
    return shape(
      'logname' => 'nazg-testing',
      'logfile' => sys_get_temp_dir().'/'.bin2hex(random_bytes(16))
    );
  }
}
