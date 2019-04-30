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
namespace Nazg\Validation;

use type Facebook\TypeAssert\IncorrectTypeException;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;

abstract class Validator {

  protected ?ServerRequestInterface $request;

  protected vec<string> $messages = vec[];

  protected vec<string> $validateMethods = vec[];

  protected bool $shouldThrowException = false;

  // disabled type assert for request parameters
  protected bool $skipValidateStructure = true;

  public function validateRequest(
    ServerRequestInterface $request
  ): void {
    $this->request = $request;
  }

  public function validate(): bool {
    if ($this->request is nonnull) {
      try {
        $this->assertStructure();
      } catch(IncorrectTypeException $e) {
        throw new ValidationException($this);
      }
    }
    if ($this->errors()) {
      if ($this->shouldThrowException) {
        throw new ValidationException($this);
      }
      return false;
    }
    return true;
  }

  <<__Memoize>>
  public function errors(): vec<string> {
    return $this->assertValidateResult();
  }

  protected function assertStructure(): void {
    if (!$this->skipValidateStructure) {
      // here
    }
  }

  abstract protected function assertValidateResult(): vec<string>;
}
