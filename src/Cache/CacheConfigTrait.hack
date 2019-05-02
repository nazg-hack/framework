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
namespace Nazg\Cache;

use type Nazg\Foundation\ConfigInterface;

trait CacheConfigTrait {
  require implements ConfigInterface;

  protected Driver $cacheDriver = Driver::File;

  private FileSystemConfig $FileSystemConfig = shape(
    'cacheStoreDir' => '/'
  );

  private MemcachedServer $memcachedServer = shape(
    'host' => '127.0.0.1', 'port' => 11211
  );

  private MemcachedConfig $memcachedConfig = shape(
    'servers' => vec[],
  );

  public function setMemcachedCacheConfig(
    MemcachedConfig $memcachedConfig
  ): void {
    $this->memcachedConfig = $memcachedConfig;
  }

  public function setFilesystemCacheConfig(
    FileSystemConfig $FileSystemConfig
  ): void {
    $this->FileSystemConfig = $FileSystemConfig;
  }

  public function setCacheDriver(Driver $driver): void {
    $this->cacheDriver = $driver;
  }

  public function getCacheDriver(): Driver {
    return $this->cacheDriver;
  }

  public function getMemcachedCacheConfig(): MemcachedConfig {
    return $this->memcachedConfig;
  }

  public function getFilesystemCacheConfig(): FileSystemConfig {
    return $this->FileSystemConfig;
  }
}
