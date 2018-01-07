<?hh 

use Ytake\Adr\Foundation\Service;
use Ytake\Adr\Routing\HttpMethod;

return [
  Service::ROUTES => [
    HttpMethod::GET => ImmMap {
      '/' => IndexAction::class,
    },
  ],
];
