<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Exceptions\LikerNotDefinedException;
use Turahe\Likeable\Exceptions\LikeTypeInvalidException;
use Turahe\Likeable\Exceptions\ModelInvalidException;

class ExceptionTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function test_liker_not_defined_exception()
    {
        $exception = new LikerNotDefinedException('User ID is required');

        $this->assertInstanceOf(LikerNotDefinedException::class, $exception);
        $this->assertEquals('User ID is required', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_invalid_exception()
    {
        $exception = new LikeTypeInvalidException('Invalid like type provided');

        $this->assertInstanceOf(LikeTypeInvalidException::class, $exception);
        $this->assertEquals('Invalid like type provided', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_model_invalid_exception()
    {
        $exception = new ModelInvalidException('Invalid model provided');

        $this->assertInstanceOf(ModelInvalidException::class, $exception);
        $this->assertEquals('Invalid model provided', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_exception_inheritance()
    {
        $this->assertInstanceOf(\Exception::class, new LikerNotDefinedException());
        $this->assertInstanceOf(\Exception::class, new LikeTypeInvalidException());
        $this->assertInstanceOf(\Exception::class, new ModelInvalidException());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_exception_with_custom_code()
    {
        $exception = new LikerNotDefinedException('Custom message', 500);

        $this->assertEquals('Custom message', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }
}
