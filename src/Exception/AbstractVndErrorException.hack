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

abstract class AbstractVndErrorException
  extends \Exception implements VndErrorExceptionInterface {

  private dict<arraykey, mixed> $attributes = dict[];

  private dict<LinkRelation, vec<LinkResource>> $linkRelations = dict[];

  private int $logRefCode = 500;

  private ?UriInterface $uri = null;

  public function setAttributes(
    dict<arraykey, mixed> $attributes
  ): void {
    $this->attributes = $attributes;
  }

  public function getAttributes(): dict<arraykey, mixed> {
    return $this->attributes;
  }

  public function setRelations(
    dict<LinkRelation, vec<LinkResource>> $linkRelations
  ): void {
    $this->linkRelations = $linkRelations;
  }

  public function setLogRef(
    int $logRefCode
  ): void {
    $this->logRefCode = $logRefCode;
  }

  public function getRelations(): dict<LinkRelation, vec<LinkResource>> {
    return $this->linkRelations;
  }

  public function getLogRef(): int {
    return $this->logRefCode;
  }

  public function setPath(UriInterface $uri): void {
    $this->uri = $uri;
  }

  public function getPath(): ?UriInterface {
    return $this->uri;
  }
}
