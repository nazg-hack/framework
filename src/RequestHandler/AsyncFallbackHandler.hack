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
namespace Nazg\RequestHandler;

use type HH\Lib\IO\WriteHandle;
use type Facebook\Experimental\Http\Message\ResponseInterface;
use type Facebook\Experimental\Http\Message\ServerRequestInterface;
use type Nazg\Http\Server\AsyncRequestHandlerInterface;
use type Ytake\Hungrr\StatusCode;
use type Ytake\Hungrr\Response\JsonResponse;

class AsyncFallbackHandler implements AsyncRequestHandlerInterface {

  public async function handleAsync(
    WriteHandle $wirteHandle,
    ServerRequestInterface $_
  ): Awaitable<ResponseInterface> {
    return new JsonResponse(
      $wirteHandle,
      StatusCode::NOT_FOUND
    );
  }
}
