use type Nazg\Exception\VndErrorFactory;
use type Nazg\Exception\AbstractVndErrorException;
use type Nazg\Exception\NotFoundHttpException;
use type Ytake\Hhypermedia\Serializer;
use type Facebook\HackTest\HackTest;
use function Facebook\FBExpect\expect;
use namespace HH\Lib\{Dict, C};

final class VndErrorFactoryTest extends HackTest {

  public function testShouldReturnSerializerInstance(): void {
    $e = new MockStandardVndErrorException('vnd error testing');
    $factory = new VndErrorFactory($e);
    $v = $factory->invoke(dict[
      'exception' => get_class($e),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => Dict\map($e->getTrace(), ($v) ==> {
        if(is_array($v)) {
          if(C\contains_key($v, 'args')) {
            return Dict\filter_with_key($v, ($k, $_) ==> $k !== 'args');
          }
        }
        return $v;
      }),
    ]);
    expect($v)->toBeInstanceOf(Serializer::class);
  }

  public function testShouldReturnSerializerInstanceWithNotImplementsException(): void {
    $e = new NotFoundHttpException('vnd error testing');
    $factory = new VndErrorFactory($e);
    $v = $factory->invoke(dict[
      'exception' => get_class($e),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => Dict\map($e->getTrace(), ($v) ==> {
        if(is_array($v)) {
          if(C\contains_key($v, 'args')) {
            return Dict\filter_with_key($v, ($k, $_) ==> $k !== 'args');
          }
        }
        return $v;
      }),
    ]);
    expect($v)->toBeInstanceOf(Serializer::class);
  }
}

final class MockStandardVndErrorException extends AbstractVndErrorException {

}
