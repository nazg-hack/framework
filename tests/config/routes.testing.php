<?hh

return [
  \Nazg\Foundation\Service::ROUTES => ImmMap {
    \Nazg\Http\HttpMethod::GET => ImmMap {
      '/' => ImmVector {
        \NazgTest\Action\IndexAction::class
      },
      '/testing/{id}' => ImmVector {
        \NazgTest\Middleware\FakeAttributeMiddleware::class,
        \NazgTest\Action\ParameterAction::class,
      },
      '/validate/{id}' => ImmVector {
        \NazgTest\Action\ValidateAction::class
      },
    },
  },
];
