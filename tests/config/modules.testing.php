<?hh 

use Nazg\Foundation\Service;

return [
  Service::MODULES => ImmVector{
    \TestingServiceModule::class,
  },
];
