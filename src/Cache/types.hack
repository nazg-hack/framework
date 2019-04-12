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

type MemcachedServer = shape(
  'host' => string,
  'port' => int,
  ?'weight' => int,
);
type FileSystemConfig = shape(
  'cacheStoreDir' => string
);
type MemcachedConfig = shape(
  'servers' => ImmVector<MemcachedServer>,
  ?'persistentId' => string,
);
type RedisConfig = shape(
  'host' => string,
  ?'port' => int,
  ?'password' => string,
  ?'prefix' => string,
  ?'readTimeout' => float,
  ?'persistent' => bool,
  ?'database' => int
);