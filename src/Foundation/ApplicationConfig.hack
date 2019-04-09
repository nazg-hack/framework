namespace Nazg\Foundation;

use type Facebook\Experimental\Http\Message\HTTPMethod;

/**
 * Framework Configure Class
 *
 */
<<__ConsistentConstruct>>
class ApplicationConfig {

  private dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes = dict[];

  public function setRoutes(
    dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> $routes
  ): void {
    $this->routes = $routes;
  }

  public function getRoutes(): dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> {
    return $this->routes;
  }
}
