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
namespace Nazg\Foundation\Bootstrap;

use type Psr\Container\ContainerInterface;

type Bootstrap = classname<BootstrapRegisterInterface>;

class BootstrapRegister implements BootstrapRegisterInterface {

  protected ImmVector<Bootstrap>
    $ibr = ImmVector {\Nazg\Foundation\Exception\ExceptionRegister::class};

  public function __construct(protected ContainerInterface $container) {}

  public function register(): void {
    foreach ($this->ibr->getIterator() as $i) {
      if ($this->container->has($i)) {
        $instance = $this->container->get($i);
        if ($instance instanceof BootstrapRegisterInterface) {
          $instance->register();
        }
      }
    }
  }
}
