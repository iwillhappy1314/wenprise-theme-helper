<?php
/**
 * Class ConditionalTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * 测试条件函数
 */
class ConditionalTest extends WP_UnitTestCase
{

    function setUp()
    {
        // Call the setup method on the parent or the factory objects won't be loaded!
        parent::setUp();

        $this->user_id = $this->factory->user->create([
            'user_login' => 'test_user',
            'role'       => 'editor',
        ]);

        $this->category = $this->factory->term->create([
            'taxonomy' => 'category',
            'term'     => 'Parent Category ',
        ]);

        $this->category_child = $this->factory->term->create([
            'taxonomy' => 'category',
            'term'     => 'Child Category ',
            [
                'parent' => $this->category,
            ],
        ]);


        $this->post = $this->factory->post->create_and_get([
            'post_type'     => 'post',
            'post_category' => [$this->category],
        ]);

        $this->post_in_child_cat = $this->factory->post->create_and_get([
            'post_type'     => 'post',
            'post_category' => [$this->category_child],
        ]);

        $this->page_parent = $this->factory->post->create_and_get([
            'post_type' => 'page',
        ]);

        $this->page_child = $this->factory->post->create_and_get([
            'post_type'   => 'page',
            'post_parent' => $this->page_parent->ID,
        ]);

    }


    /**
     * A single example test.
     */
    public function test_wprs_is_wechat()
    {
        $this->assertFalse(wprs_is_wechat());

        $_SERVER[ 'HTTP_USER_AGENT' ] = 'MicroMessenger';
        $this->assertTrue(wprs_is_wechat());

        $_SERVER[ 'HTTP_USER_AGENT' ] = 'MicroMessenger 10.1';
        $this->assertTrue(wprs_is_wechat());
    }


    public function test_wprs_is_subpage()
    {
        $this->assertFalse(wprs_is_subpage());

        // 添加数据测试
        $this->assertTrue(wprs_is_subpage($this->page_child, $this->page_parent->ID));
    }


    public function test_wprs_is_object_in_terms()
    {
        // 文章在父级分类中
        $this->assertTrue(wprs_is_object_in_terms($this->post->ID, [$this->category], 'category'));

        // 文章在子分类中、测试父级分类
        $this->assertTrue(wprs_is_object_in_terms($this->post_in_child_cat->ID, [$this->category], 'category'));

        // 文章在子分类中、测试子分类
        $this->assertTrue(wprs_is_object_in_terms($this->post_in_child_cat->ID, [$this->category_child], 'category'));

        // 测试其他分类方法
        $this->assertFalse(wprs_is_object_in_terms($this->post->ID, [$this->category], 'post_tag'));
    }


    public function test_wprs_is_ajax()
    {
        $this->assertFalse(wprs_is_ajax());

        $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] = 'xmlhttprequest';
        $this->assertTrue(wprs_is_ajax());
    }


    public function test_wprs_is_request()
    {
        $this->assertFalse(wprs_is_request('ajax'));
        $this->assertFalse(wprs_is_request('admin'));
        $this->assertTrue(wprs_is_request('frontend'));

        set_current_screen('post');
        $this->assertTrue(wprs_is_request('admin'));

        // 设置 Ajax 操作
        define('DOING_AJAX', true);
        $this->assertTrue(wprs_is_ajax());
        $this->assertTrue(wprs_is_request('ajax'));
        $this->assertTrue(wprs_is_request('frontend'));

        // 设置 Rest 操作
        define('REST_REQUEST', true);
        $this->assertTrue(wprs_is_request('rest'));

        // 设置 Cron 操作
        define('DOING_CRON', true);
        $this->assertTrue(wprs_is_request('cron'));
        $this->assertFalse(wprs_is_request('frontend'));

    }


    public function test_wprs_is_en()
    {
        $this->assertTrue(wprs_is_en());

        add_filter('locale', function ($locale)
        {
            return 'zh_CN';
        });

        $this->assertFalse(wprs_is_en());
    }


    public function test_wprs_is_table_installed()
    {
        $this->assertTrue(wprs_is_table_installed('posts'));
        $this->assertFalse(wprs_is_table_installed('likes'));
    }


    public function test_wprs_is_user_has_role()
    {
        $this->assertTrue(wprs_is_user_has_role('administrator', 1));
        $this->assertFalse(wprs_is_user_has_role('editor', 1));
        $this->assertTrue(wprs_is_user_has_role('editor', $this->user_id));
        $this->assertFalse(wprs_is_user_has_role('administrator', 2));
    }


    public function test_wprs_is_plugin_active()
    {
        $this->assertFalse(wprs_is_plugin_active('woocommerce'));
    }


    public function test_wprs_is_plugin_active_for_network()
    {
        $this->assertFalse(wprs_is_plugin_active_for_network('woocommerce'));
    }

}
