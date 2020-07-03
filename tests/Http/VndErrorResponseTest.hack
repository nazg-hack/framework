use type Nazg\Http\VndErrorResponse;
use type Ytake\Hungrr\StatusCode;
use type Facebook\HackTest\HackTest;
use namespace HH\Lib\IO;
use function Facebook\FBExpect\expect;

final class VndErrorResponseTest extends HackTest {

  public async function testShouldBe(): Awaitable<void> {
    list($read, $write) = IO\pipe();
    $r = new VndErrorResponse($write);
    await $write->writeAsync(\json_encode(dict[]));
    $write->close();
    expect($r->getStatusCode())->toBeSame(500);
    expect($r->getProtocolVersion())->toBeSame('1.1');
    expect($r->getReasonPhrase())->toBeSame('Internal Server Error');
    expect($r->getHeaders())->toBeSame(dict[
      'content-type' => vec['application/vnd.error+json'],
    ]);
    $re = await $read->readAsync();
    expect($re)->toBeSame('{}');
  }

  public async function testShouldReturnJsonBody(): Awaitable<void> {
    list($read, $write) = IO\pipe();
    await $write->writeAsync(\json_encode(dict[
      'testing' => dict[
        'HHVM' => 'Hack',
      ]
    ]));
    $write->close();
    $r = new VndErrorResponse($write, StatusCode::FORBIDDEN);
    expect($r->getStatusCode())->toBeSame(403);
    expect($r->getProtocolVersion())->toBeSame('1.1');
    expect($r->getReasonPhrase())->toBeSame('Forbidden');
    expect($r->getHeaders())->toBeSame(dict[
      'content-type' => vec['application/vnd.error+json'],
    ]);
    $r->getBody();
    $re = await $read->readAsync();
    expect($re)->toBeSame('{"testing":{"HHVM":"Hack"}}');
  }
}
