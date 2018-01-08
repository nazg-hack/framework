<?hh 

use Nazg\Foundation\Service;
use Nazg\Routing\HttpMethod;

return [
  Service::ROUTES => [
    HttpMethod::GET => ImmMap {
      '/' => IndexAction::class,
    },
  ],
];
