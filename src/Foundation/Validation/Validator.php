<?hh // strict

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
 * Copyright (c) 2017-2018 Yuuki Takezawa
 *
 */
namespace Nazg\Foundation\Validation;

use Psr\Http\Message\ServerRequestInterface;
use Facebook\TypeAssert;

enum Attribute : string as string {
  Named = 'RequestValidation';
}

abstract class Validator {

  protected ?ServerRequestInterface $request;

  protected Vector<string> $messages = Vector {};

  protected Vector<string> $validateMethods = Vector {};

  protected bool $shouldThrowException = false;

  // disabled type assert for request parameters
  protected bool $skipValidateStructure = true;

  public function validateRequest(ServerRequestInterface $request): void {
    $this->request = $request;
  }

  public function validate(): bool {
    if (!is_null($this->request)) {
      $this->assertStructure();
    }
    if ($this->errors()->count()) {
      if ($this->shouldThrowException) {
        throw new ValidationException($this);
      }
      return false;
    }
    return true;
  }

  <<__Memoize>>
  public function errors(): ImmVector<string> {
    return $this->assertValidateResult()->immutable();
  }

  protected function assertStructure(): void {
    if (!$this->skipValidateStructure) {
      // here
    }
  }

  abstract protected function assertValidateResult(): Vector<string>;
}
