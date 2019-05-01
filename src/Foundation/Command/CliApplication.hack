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
namespace Nazg\Foundation\Command;

use type Nazg\Foundation\Application;
use type Facebook\CLILib\CLIBase;

abstract class CliApplication extends CLIBase {

  private static ?Application $app;

  final public static function setApplication(
    Application $app
  ): void {
    self::$app = $app;
  }

  public function getApplication(): Application {
    invariant(self::$app is Application, 'Could not resolve Application instance');
    return self::$app;
  }
}
