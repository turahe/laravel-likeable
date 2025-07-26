<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Enums\LikeType;

class EnumTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_values()
    {
        $this->assertEquals('like', LikeType::LIKE->value);
        $this->assertEquals('dislike', LikeType::DISLIKE->value);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_names()
    {
        $this->assertEquals('LIKE', LikeType::LIKE->name);
        $this->assertEquals('DISLIKE', LikeType::DISLIKE->name);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_cases()
    {
        $cases = LikeType::cases();
        
        $this->assertCount(2, $cases);
        $this->assertContains(LikeType::LIKE, $cases);
        $this->assertContains(LikeType::DISLIKE, $cases);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_comparison()
    {
        $this->assertTrue(LikeType::LIKE === LikeType::LIKE);
        $this->assertTrue(LikeType::DISLIKE === LikeType::DISLIKE);
        $this->assertFalse(LikeType::LIKE === LikeType::DISLIKE);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_string_conversion()
    {
        $this->assertEquals('like', LikeType::LIKE->value);
        $this->assertEquals('dislike', LikeType::DISLIKE->value);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_from_value()
    {
        $this->assertEquals(LikeType::LIKE, LikeType::from('like'));
        $this->assertEquals(LikeType::DISLIKE, LikeType::from('dislike'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_try_from()
    {
        $this->assertEquals(LikeType::LIKE, LikeType::tryFrom('like'));
        $this->assertEquals(LikeType::DISLIKE, LikeType::tryFrom('dislike'));
        $this->assertNull(LikeType::tryFrom('invalid'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_enum_json_serialization()
    {
        $this->assertEquals('"like"', json_encode(LikeType::LIKE));
        $this->assertEquals('"dislike"', json_encode(LikeType::DISLIKE));
    }
} 