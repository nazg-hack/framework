<?hh // strict

namespace Nazg\Http;

enum HttpMethod: string {
  HEAD = 'HEAD';
  GET = 'GET';
  POST = 'POST'; 
  PATCH = 'PATCH';
  PUT = 'PUT';
  DELETE = 'DELETE';
}
