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
    /**
     * @var Request
     */
    private $request;

    public function setUp()
    {
        $this->converter = new RequestHeaders();
        $this->request = new Request([], [], [], [], [], [
            'HTTP_Content_Type' => 'application/json',
            'HTTP_X_AUTHENTICATED_USER_ID' => '12345',
            'HTTP_X_REQUIRE_AUTH' => 'false',
        ]);
    }

    /**
     * @test
     * @dataProvider applyValidProvider
     */
    public function apply_valid($config, $parameterName, $expectedResult)
    {
        $config = new ParamConverter($config);

        $this->converter->apply($this->request, $config);
        self::assertEquals($expectedResult, $this->request->attributes->get($parameterName));
    }

    public function applyValidProvider()
    {
        return [
            'optional string value, exists' => [
                'config' => ['IsOptional' => true, 'name' => 'ContentType', 'class' => 'string'],
                'parameterName' => 'contentType',
                'expectedResult' => 'application/json',
            ],
            'optional int value, exists' => [
                'config' => ['IsOptional' => true, 'name' => 'X-Authenticated-User-Id', 'class' => 'int'],
                'parameterName' => 'xAuthenticatedUserId',
                'expectedResult' => 12345,
            ],
            'optional bool value, exists' => [
                'config' => ['IsOptional' => true, 'name' => 'X-Require-Auth', 'class' => 'bool'],
                'parameterName' => 'xRequireAuth',
                'expectedResult' => false,
            ],
            'optional string value, missing' => [
                'config' => ['IsOptional' => true, 'name' => 'NonExistentHeader', 'class' => 'string'],
                'parameterName' => 'nonExistentHeader',
                'expectedResult' => null,
            ],
            'optional int value, missing' => [
                'config' => ['IsOptional' => true, 'name' => 'Non-Existent-Header', 'class' => 'int'],
                'parameterName' => 'nonExistentHeader',
                'expectedResult' => null,
            ],
            'optional bool value, missing' => [
                'config' => ['IsOptional' => true, 'name' => 'NonExistentHeader', 'class' => 'bool'],
                'parameterName' => 'nonExistentHeader',
                'expectedResult' => false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider applyInvalidProvider
     */
    public function apply_invalid($config)
    {
        self::setExpectedException('EdSukharev\App\ParamConverter\MissingHeaderException');
        $config = new ParamConverter($config);

        $this->converter->apply($this->request, $config);
    }

    public function applyInvalidProvider()
    {
        return [
            'required string value, missing' => [
                'config' => ['IsOptional' => false, 'name' => 'NonExistentHeader', 'class' => 'string'],
            ],
            'required int value, missing' => [
                'config' => ['IsOptional' => false, 'name' => 'Non-Existent-Header', 'class' => 'int'],
            ],
            'required bool value, missing' => [
                'config' => ['IsOptional' => false, 'name' => 'NonExistentHeader', 'class' => 'bool'],
            ],
        ];
    }
}
