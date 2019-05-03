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

use type Ytake\Hhypermedia\Serializer;
use type Ytake\Hhypermedia\Error\ErrorLink;
use type Ytake\Hhypermedia\Error\MessageResource;
use type Ytake\Hhypermedia\ResourceObject;
use type Ytake\Hhypermedia\Serializer\VndErrorSerializer;
use type Ytake\Hhypermedia\ErrorAttributes;
use type Ytake\Hhypermedia\Visitor\JsonSerializationVisitor;

final class VndErrorFactory {

  public function __construct(
    private \Exception $throw
  ) {}

  public function invoke(
    dict<arraykey, mixed> $optionalAttributes = dict[],
    ErrorAttributes $errorAttributes = shape()
  ): Serializer {
    $resourceObject = new ResourceObject();
    $throw = $this->throw;
    if($throw is AbstractVndErrorException) {
      foreach($throw->getRelations() as $name => $relation) {
        $resourceObject = $resourceObject->withLink(new ErrorLink($name, $relation));
      }
    }
    return new Serializer(
      new VndErrorSerializer(),
      new MessageResource(
        $throw->getMessage(),
        $resourceObject,
        $errorAttributes,
        $optionalAttributes
      ),
      new JsonSerializationVisitor(
        \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES| \JSON_PRESERVE_ZERO_FRACTION
      )
    );
  }
}
