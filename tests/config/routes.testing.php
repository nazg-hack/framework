<?hh 

use Nazg\Foundation\Service;
use Nazg\Routing\HttpMethod;

return [
  Service::ROUTES => ImmMap {
    HttpMethod::GET => ImmMap {
      '/' => IndexAction::class,
      '/testing/{id}' => ParameterAction::class,
    },
  },
];
