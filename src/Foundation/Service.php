<?hh // strict 

namespace Nazg\Foundation;

enum Service: string as string {
  CONFIG = 'app.config';
  MODULES = 'module';
  ROUTES = 'route';
  MIDDLEWARES = 'middleware';
}
