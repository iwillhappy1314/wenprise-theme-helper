<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * Sample test case.
 */
class DataOptionTest extends WP_UnitTestCase
{

    function setUp()
    {
        // Call the setup method on the parent or the factory objects won't be loaded!
        parent::setUp();

        register_post_type('book');
        register_taxonomy('genre', 'book');

        $this->post_id = $this->factory->post->create([
            'post_type'   => 'page',
            'post_status' => 'draft',
            'post_title'  => 'This is a test page.',
            'meta_input'  => [
                'one' => 'foo-bar',
                'two' => ['foo', 'bar'],
            ],
        ]);

        $this->post_ids = $this->factory->post->create_many(3);

        $this->user_id = $this->factory->user->create([
            'user_login' => 'test_user',
            'role'       => 'editor',
        ]);

        $this->user_ids = $this->factory->user->create_many(4);

        $this->term_id = $this->factory->term->create([
            'name'     => 'Size',
            'taxonomy' => 'genre',
            'slug'     => 'Size',
        ]);

        $this->term_ids = $this->factory->term->create_many(3);

    }


    public function test_wprs_data_post_types()
    {
        $expect = [
            'post' => 'Post',
            'page' => 'Page',
        ];

        $expect2 = ['' => 'Select a content type'] + $expect;

        $this->assertSame($expect, wprs_data_post_types(false));
        $this->assertSame($expect2, wprs_data_post_types());
    }


    public function test_wprs_data_taxonomies()
    {
        $expect = [
            'category' => __('Category', 'wprs'),
            'post_tag' => __('Tags', 'wprs'),
            'genre'    => 'Tags',
        ];

        $expect2 = ['' => sprintf('%s', __('Select a taxonomy', 'wprs')),] + $expect;

        $this->assertSame($expect, wprs_data_taxonomies(false));
        $this->assertSame($expect2, wprs_data_taxonomies());
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
}
