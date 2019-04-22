<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * Sample test case.
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

    }


    /**
     * A single example test.
     */
    public function test_wprs_is_wechat()
    {
        $this->assertFalse(wprs_is_wechat());
    }


    public function test_wprs_is_ajax()
    {
        $this->assertFalse(wprs_is_ajax());
    }


    public function test_wprs_is_request()
    {
        $this->assertFalse(wprs_is_request('ajax'));
    }


    public function test_wprs_is_en()
    {
        $this->assertTrue(wprs_is_en());
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
