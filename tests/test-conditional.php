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

    /**
     * A single example test.
     */
    public function test_wprs_is_wechat()
    {
        $this->assertEquals(false, wprs_is_wechat());
    }


    public function test_wprs_is_ajax()
    {
        $this->assertEquals(false, wprs_is_ajax());
    }


    public function test_wprs_is_request()
    {
        $this->assertEquals(false, wprs_is_request('ajax'));
    }


    public function test_wprs_is_en()
    {
        $this->assertEquals(true, wprs_is_en());
    }


    public function test_wprs_is_table_installed()
    {
        $this->assertEquals(true, wprs_is_table_installed('posts'));
        $this->assertEquals(false, wprs_is_table_installed('likes'));
    }



    public function test_wprs_is_user_has_role()
    {
        $this->assertEquals(true, wprs_is_user_has_role('administrator', 1));
        $this->assertEquals(false, wprs_is_user_has_role('editor', 1));
        $this->assertEquals(false, wprs_is_user_has_role('administrator', 2));
    }


    public function test_wprs_is_plugin_active()
    {
        $this->assertEquals(false, wprs_is_plugin_active('woocommerce'));
    }


    public function test_wprs_is_plugin_active_for_network()
    {
        $this->assertEquals(false, wprs_is_plugin_active_for_network('woocommerce'));
    }


}
