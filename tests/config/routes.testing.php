<?hh 

use Nazg\Foundation\Service;
use Nazg\Http\HttpMethod;

return [
  Service::ROUTES => ImmMap {
    HttpMethod::GET => ImmMap {
      '/' => ImmVector {IndexAction::class},
      '/testing/{id}' => ImmVector {
        FakeAttributeMiddleware::class,
        ParameterAction::class,
      },
      '/validate/{id}' => ImmVector {ValidateAction::class},
    },
  },
];
