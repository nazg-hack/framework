<?hh

return [
  \Nazg\Foundation\Service::ROUTES => ImmMap {
    \Nazg\Http\HttpMethod::GET => ImmMap {
      '/' => shape(
        'middleware' => ImmVector {
          \NazgTest\Action\IndexAction::class
        }
      ),
      '/testing/{id}' => shape(
        'middleware' => ImmVector {
          \NazgTest\Middleware\FakeAttributeMiddleware::class,
          \NazgTest\Action\ParameterAction::class,
        }
      ),
      '/validate/{id}' => shape(
        'middleware' => ImmVector {
          \NazgTest\Action\ValidateAction::class
        }
      )
    },
  },
];
