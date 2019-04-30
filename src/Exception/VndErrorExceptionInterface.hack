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
namespace Nazg\Exception;

use type Ytake\Hhypermedia\Error\LinkRelation;
use type Ytake\Hhypermedia\LinkResource;
use type Facebook\Experimental\Http\Message\UriInterface;

interface VndErrorExceptionInterface {

  /**
   * Link attributes follow the same semantics as defined in the HAL specification
   *
   * @see https://github.com/blongden/vnd.error#link-attributes
   */
  public function setRelations(
    dict<LinkRelation, vec<LinkResource>> $linkRelations
  ): void;

  /**
   * For expressing a identifier to refer to the specific error on the server side
   * for logging purposes (i.e. a request number).
   *
   * @see https://github.com/blongden/vnd.error#logref
   */
  public function setLogRef(
    int $logRefCode
  ): void;

  public function getRelations(): dict<LinkRelation, vec<LinkResource>>;

  public function getLogRef(): ?int;

  public function setPath(UriInterface $uri): void;

  public function getPath(): ?UriInterface;
}
