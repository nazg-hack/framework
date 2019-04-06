namespace Nazg\Foundation;

use type Facebook\Experimental\Http\Message\HTTPMethod;

/**
 * Framework Configure Class
 *
 */
class ApplicationConfig {

  public function getRoutes(): dict<HTTPMethod, ImmMap<string, \Nazg\Routing\TResponder>> {
    return dict[];
  }
}
