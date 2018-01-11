<?hh 

use Nazg\Foundation\Service;
use Nazg\Http\HttpMethod;

return [
  Service::ROUTES => ImmMap {
    HttpMethod::GET => ImmMap {
      '/' => IndexAction::class,
      '/testing/{id}' => ParameterAction::class,
    },
  },
];
