Request Headers ParamConverter for SensioFrameworkExtraBundle
==========================

This package contains simple yet convenient ParamConverter for injecting HTTP headers into controller actions.

Read about SensioFrameworkExtraBundle on its [official homepage](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html).

## Usage

Define `RequestHeader` as a service (in `config.yml`):
```yml
services:
    EdSukharev\App\ParamConverter\RequestHeaders:
        tags:
            - { name: 'request.param_converter', converter: 'request_header_converter', priority: '-60' }
```
And in your Controller:
```php
namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController
{
    /**
     * @Route("/", methods={"GET"})
     * @ParamConverter("XAuthenticatedUserId", class="int", isOptional=true, converter="request_header_converter")
     * @ParamConverter("ContentType", class="string", isOptional=false, converter="request_header_converter")
     * @ParamConverter("XRequireAuth", class="string", isOptional=false, converter="request_header_converter")
     */
    public function getUserInfo($xAuthenticatedUserId, $contentType, $xRequireAuth) {
        return new JsonResponse([
            'XAuthenticatedUserId' => $xAuthenticatedUserId,
            'ContentType' => $contentType,
            'XRequireAuth' => $xRequireAuth,
        ]);
    }
}
```

Then, provided following request:
```
curl localhost -H 'x-require-auth: false' -H 'content-type: application/json'
```
the response will be:
```json
{
  "XAuthenticatedUserId": null,
  "ContentType": "application/json",
  "XRequireAuth": false
}
```

## Annotation parameters

ParamConverter annotation receives following parameters as arguments:
    - **name** — name of the header to look for, also used to generate controller action argument name
    - **class** — defines type of the argument. By default, all headers received as strings.
    - **isOptional** — when header is missing, and this is set to false, then `\EdSukharev\App\ParamConverter\MissingHeaderException` will be thrown, otherwise argument value will be set to null. 
    - **converter** — has to be `request_header_converter` for this request parameter to be handled by this library.
