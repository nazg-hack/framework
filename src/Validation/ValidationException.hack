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

use type Exception;
use type Ytake\Hungrr\StatusCode;
use type Nazg\Exception\AbstractVndErrorException;

class ValidationException extends AbstractVndErrorException {

  private int $logRefCode = StatusCode::BAD_REQUEST;

  public function __construct(
    protected Validator $validator,
    int $code = StatusCode::BAD_REQUEST,
    protected ?Exception $previous = null,
  ) {
    parent::__construct('The given data was invalid.', $code, $previous);
    $this->validator = $validator;
  }

  public function errors(): vec<string> {
    return $this->validator->errors();
  }
}
