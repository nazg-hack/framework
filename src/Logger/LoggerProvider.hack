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
namespace Nazg\Logger;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;
use type Nazg\Foundation\ApplicationConfig;
use type HackLogging\Logger;
use type HackLogging\Handler\FilesystemHandler;
use namespace HH\Lib\File;

final class LoggerProvider implements ProviderInterface<Logger> {

  public function get(
    Container $container
  ): Logger {
    $config = $container->get(ApplicationConfig::class);
    $logConfig = $config->getLogConfig();
    return new Logger($logConfig['logname'], vec[
      new FilesystemHandler(File\open_write_only_nd($logConfig['logfile']))
    ]);
  }
}
