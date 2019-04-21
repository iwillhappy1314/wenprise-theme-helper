<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * Sample test case.
 */
class HelperTest extends WP_UnitTestCase
{

    /**
     * A single example test.
     */
    public function test_wprs_user_get_roles()
    {
        // Replace this with some actual testing code.
        $this->assertEquals(['administrator'], wprs_user_get_roles(1));
    }


    public function test_wprs_step_class()
    {

        $steps = [
            'register',
            'login',
            'logout',
        ];

        // Replace this with some actual testing code.
        $this->assertEquals('c-step--active', wprs_step_class('register', $steps, 0));
        $this->assertEquals('c-step--complete', wprs_step_class('register', $steps, 1));
        $this->assertEquals('c-step--disable', wprs_step_class('logout', $steps, 1));
    }


    public function test_wprs_trim_words()
    {
        $string_en = 'this is a test for english strings';
        $string_zh = '这是一个测试字符串，测试中文字符截断';

        // Replace this with some actual testing code.
        $this->assertEquals('this is a test...', wprs_trim_words($string_en, '18', '...'));
        // $this->assertEquals('这是一个...', wprs_trim_words($string_zh, '8', '...'));
    }


    public function test_wprs_string_masks()
    {
        $string = '18812348888';

        // Replace this with some actual testing code.
        $this->assertEquals('188****8888', wprs_string_mask($string, 3, 4));
    }


    public function test_wprs_get_domain(){

        $this->assertEquals('google.com', wprs_get_domain('www.google.com'));
        $this->assertEquals('google.com', wprs_get_domain('google.com'));
        // $this->assertEquals('google.com', wprs_get_domain('google.com/about'));
        // $this->assertEquals('google.com', wprs_get_domain('www.google.com/about'));
        // $this->assertEquals('google.com', wprs_get_domain('www.google.com/about?p=1'));
        $this->assertNotEquals('bing.com', wprs_get_domain('www.google.com'));

    }
}
