<?php
/**
 * 测试数据选项
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * DataOptionTest
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


    /**
     * 测试文章类型选项
     */
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


    /**
     * 测试分类法选项
     */
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


    /**
     * 测试用户选项数组
     */
    public function test_wprs_data_user()
    {
        foreach ($this->user_ids as $user_id) {
            $expect[ $user_id ] = get_user_by('ID', $user_id)->display_name;
        }

        $expect2 = ['' => sprintf('%s', __('Select a user', 'wprs')),] + $expect;

        $this->assertSame($expect, wprs_data_user('subscriber', false));
        $this->assertSame($expect2, wprs_data_user('subscriber'));
    }

}
