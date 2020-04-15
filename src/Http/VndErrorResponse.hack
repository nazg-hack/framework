/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2017-2019 Yuuki Takezawa
 *
 */
namespace Nazg\Http;

use type Ytake\Hungrr\Response\JsonResponse;
use type Ytake\Hungrr\StatusCode;
use type Ytake\Hungrr\Response\InjectContentTypeTrait;
use namespace HH\Lib\IO;

final class VndErrorResponse extends JsonResponse {

  use InjectContentTypeTrait;

  public function __construct(
    private IO\WriteHandle $body,
    StatusCode $status = StatusCode::INTERNAL_SERVER_ERROR,
    dict<string, vec<string>> $headers = dict[],
    protected int $encodingOptions = self::DEFAULT_JSON_FLAGS
  ) {
    parent::__construct(
      $body,
      $status,
      /* HH_FIXME[3004] */
      $this->injectContentType('application/vnd.error+json', $headers),
    );
  }
}
