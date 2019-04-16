namespace Nazg\Foundation;

use type Facebook\Experimental\Http\Message\HTTPMethod;
use namespace Nazg\Cache;
use function sys_get_temp_dir;
use function bin2hex;
use function random_bytes;

/**
 * Framework Configure Class
 *
 */
<<__ConsistentConstruct>>
class ApplicationConfig implements ConfigInterface {

  use Cache\CacheConfigTrait;

  private dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes = dict[];

  private shape('logfile' => string, 'logname' => string) $logfile = shape(
    'logfile' => '',
    'logname' => 'nazg'
  );

  public function setRoutes(
    dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes
  ): void {
    $this->routes = $routes;
  }

  public function getRoutes(): dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> {
    return $this->routes;
  }

  public function setLogConfig(shape('logfile' => string, 'logname' => string) $logConfig): void {
    $this->logfile = $logConfig;
  }

  public function getLogConfig(): shape('logfile' => string, 'logname' => string) {
    return $this->logfile;
  }
}
