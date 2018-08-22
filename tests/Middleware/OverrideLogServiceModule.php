<?hh

namespace NazgTest\Middleware;

use type Psr\Log\LoggerInterface;
use namespace Monolog\Monolog;

class OverrideLogServiceModule extends \Nazg\Log\LogServiceModule {

  const string LOG_FILE = __DIR__ . '/../logs/tests.log';

  <<__Override>>
  protected function defaultLogger(): LoggerInterface {
    $monolog = new \Monolog\Logger("Nazg.Log");
    $monolog->pushHandler(
      new \Monolog\Handler\StreamHandler(self::LOG_FILE, \Monolog\Logger::WARNING)
    );
    return $monolog;
  }
}
