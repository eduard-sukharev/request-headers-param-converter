<?php

namespace EdSukharev\App\Test\ParamConverter;

use EdSukharev\App\ParamConverter\RequestHeaders;
use PHPUnit_Framework_TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RequestHeadersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RequestHeaders
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new RequestHeaders();
    }

    /**
     * @test
     */
    public function apply_valid()
    {
        $config = new ParamConverter(['IsOptional' => false, 'name' => 'ContentType', 'class' => 'string']);
        $request = $this->getRequest([
            'HTTP_Content_Type' => 'application/json'
        ]);
        $this->converter->apply($request, $config);
        self::assertEquals('application/json', $request->attributes->get('ContentType'));
    }

    private function getRequest(array $headers)
    {
        return new Request([], [], [], [], [], $headers);
    }
}
