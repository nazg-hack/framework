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

use type Nazg\Foundation\ApplicationConfig;
use namespace Facebook\CLILib\CLIOptions;
use function apc_delete;

final class ContainerCacheClear extends CliApplication {

  <<__Override>>
  public async function mainAsync(): Awaitable<int> {
    $app = $this->getApplication();
    $config = $app->getContainer()->get(ApplicationConfig::class);
    apc_delete($config->getContainerCacheKeyname());
    $stdout = $this->getStdout();
    await $stdout->writeAsync("Deleted Container Cache");
    await $stdout->flushAsync();
    return 0;
  }

  <<__Override>>
  protected function getSupportedOptions(): vec<CLIOptions\CLIOption> {
    return vec[];
  }
}
