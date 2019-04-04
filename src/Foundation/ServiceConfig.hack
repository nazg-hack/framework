namespace Nazg\Foundation;

use type Facebook\Experimental\Http\Message\HTTPMethod;

class ServiceConfig {

  public function getRoutes(): dict<HTTPMethod, dict<string, \Nazg\Routing\TResponder>> {
    return dict[];
  }
}
