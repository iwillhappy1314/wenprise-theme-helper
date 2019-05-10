<?php
/**
 * Class HelperTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * 测试辅助函数
 */
class HelperTest extends WP_UnitTestCase
{

    function setUp()
    {
        // Call the setup method on the parent or the factory objects won't be loaded!
        parent::setUp();

        $this->user_id = $this->factory->user->create([
            'user_login' => 'test_user',
            'role'       => 'editor',
        ]);

    }


    public function test_wprs_get_ip()
    {

        $_SERVER[ 'HTTP_CLIENT_IP' ] = '1.1.1.1';

        $this->assertEquals('1.1.1.1', wprs_get_ip());

        $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] = '000';
        $this->assertEquals('1.1.1.1', wprs_get_ip());

        $_SERVER[ 'HTTP_CLIENT_IP' ]       = '111';
        $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] = '2.2.2.2';
        $this->assertEquals('2.2.2.2', wprs_get_ip());

        $_SERVER[ 'HTTP_CLIENT_IP' ]       = '111';
        $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] = '222';
        $_SERVER[ 'REMOTE_ADDR' ]          = '3.3.3.3';

        $this->assertEquals('3.3.3.3', wprs_get_ip());
    }


    public function test_wprs_env()
    {
        // Replace this with some actual testing code.
        $this->assertEquals('production', wprs_env());

        define('ENV', 'local');
        $this->assertEquals('local', wprs_env());
    }


    public function test_wprs_user_get_roles()
    {
        // Replace this with some actual testing code.
        $this->assertEquals(['administrator'], wprs_user_get_roles(1));
        $this->assertEquals(['editor'], wprs_user_get_roles($this->user_id));
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


    public function test_wprs_get_domain()
    {

        $this->assertEquals('google.com', wprs_get_domain('www.google.com'));
        $this->assertEquals('google.com', wprs_get_domain('google.com'));
        $this->assertEquals('google.com', wprs_get_domain('google.com/about'));
        $this->assertEquals('google.com', wprs_get_domain('www.google.com/about'));
        $this->assertEquals('google.com', wprs_get_domain('www.google.com/about?p=1'));
        $this->assertEquals('google.com.cn', wprs_get_domain('www.google.com.cn/about?p=1'));
        $this->assertNotEquals('bing.com', wprs_get_domain('www.google.com'));

    }


    public function test_wprs_update_post_status()
    {
        wprs_update_post_status($this->post_id, 'complete');

        // $this->assertEquals('complete', get_post_status($this->post_id));
    }


    public function test_wprs_content_dir()
    {
        $this->assertEquals(WP_CONTENT_DIR . DIRECTORY_SEPARATOR, wprs_content_dir());
        $this->assertEquals(WP_CONTENT_DIR . DIRECTORY_SEPARATOR, wprs_content_dir('/'));
        $this->assertEquals(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'cache', wprs_content_dir('cache'));
        $this->assertEquals(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'cache', wprs_content_dir('cache/'));
        $this->assertEquals(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'cache/css', wprs_content_dir('cache/css'));
    }
}
