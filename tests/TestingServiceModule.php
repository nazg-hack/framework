<?hh // strict

namespace NazgTest;

use Ytake\HHContainer\FactoryContainer;
use Ytake\HHContainer\ServiceModule;

class TestingServiceModule extends ServiceModule {
  public function provide(FactoryContainer $container): void {
    $container->set(
      \NazgTest\Action\IndexAction::class, 
      $container ==> new \NazgTest\Action\IndexAction()
    );
    $container->set(
      \NazgTest\Action\ParameterAction::class, 
      $container ==> new \NazgTest\Action\ParameterAction()
    );
    $container->set(
      \NazgTest\Action\ValidateAction::class, 
      $container ==> new \NazgTest\Action\ValidateAction()
    );
    $container->set(
      \NazgTest\Middleware\FakeAttributeMiddleware::class, 
      $container ==> new \NazgTest\Middleware\FakeAttributeMiddleware()
    );
    $container->set(
      \NazgTest\Validation\MockValidateActionFaild::class, 
      $container ==> new \NazgTest\Validation\MockValidateActionFaild()
    );
  }
}
