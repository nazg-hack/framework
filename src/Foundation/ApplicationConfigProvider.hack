namespace Nazg\Foundation;

use type Nazg\Glue\Container;
use type Nazg\Glue\ProviderInterface;

class ApplicationConfigProvider implements ProviderInterface<ApplicationConfig> {

  public function __construct(
    protected ApplicationConfig $config
  ) {}

  public function get(
    Container $_
  ): ApplicationConfig {
    return $this->config;
  }
}
