<?hh //strict

namespace Ytake\Adr\Routing;

enum HttpMethod: string {
  HEAD = 'HEAD';
  GET = 'GET';
  POST = 'POST'; 
  PATCH = 'PATCH';
  PUT = 'PUT';
  DELETE = 'DELETE';
}
