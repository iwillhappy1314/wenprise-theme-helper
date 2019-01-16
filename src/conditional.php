<?php


if ( ! function_exists('wprs_is_wechat')) {
    /**
     * 判断是否在微信中打开
     */
    function wprs_is_wechat()
    {
        if ( ! empty($_SERVER[ 'HTTP_USER_AGENT' ]) && strpos($_SERVER[ 'HTTP_USER_AGENT' ], 'MicroMessenger') !== false) {
            return true;
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_subpage')) {
    /**
     * 判断当前页面是否为子页面
     *
     * @param array $parent
     *
     * @return bool
     */
    function wprs_is_subpage(array $parent)
    {
        global $post;

        $parentPage = get_post($post->post_parent);

        if (is_page() && $post->post_parent && $parentPage->post_name === $parent[ 0 ]) {
            return $post->post_parent;
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_object_in_terms')) {
    /**
     * @param        int       $post 文章 ID
     * @param        int|array $cats 分类方法 ID
     * @param string string $taxonomy 分类方法
     *
     * @return bool
     */
    function wprs_is_object_in_terms($post, $cats, $taxonomy = 'category')
    {
        foreach ((array)$cats as $cat) {
            $terms = get_term_children((int)$cat, $taxonomy);

            if ($terms && is_object_in_term($post, $taxonomy, $terms)) {
                return true;
            }
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_ajax')) {
    /**
     * 判断是否为 Ajax 请求
     *
     * @return bool
     */
    function wprs_is_ajax()
    {
        if ( ! empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_request')) {
    /**
     * 判断请求类型
     *
     * @param $type string admin|ajax|rest|cron|frontend
     *
     * @return bool
     */
    function wprs_is_request($type)
    {
        switch ($type) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined('DOING_AJAX');

            case 'rest' :
                return defined('REST_REQUEST');

            case 'cron' :
                return defined('DOING_CRON');

            case 'frontend' :
                return ( ! is_admin() || defined('DOING_AJAX')) && ! defined('DOING_CRON');
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_en')) {
    /**
     * 判断当前语言是否为英文
     *
     * @return bool
     */
    function wprs_is_en()
    {

        $lang = get_bloginfo('language');

        if ($lang == 'en-US') {
            return true;
        }

        return false;
    }
}


if ( ! function_exists('wprs_is_table_installed')) {
    /**
     * 判断是否安装了数据表
     *
     * @param int $table_name , 不带前缀的数据表名称
     *
     * @return bool
     */
    function wprs_is_table_installed($table_name)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            return true;
        }

        return false;
    }
}


/**
 * 判断用户是有某个角色
 *
 * @param $role    string 角色名称
 * @param $user_id int 用户 ID
 *
 * @return bool
 */
if ( ! function_exists('wprs_is_user_has_role')) {
    function wprs_is_user_has_role($role, $user_id = 0)
    {
        $user_roles = wprs_user_get_roles($user_id);

        return in_array($role, $user_roles);
    }
}


if ( ! function_exists('wprs_is_plugin_active')) {
    /**
     * 检查插件是否已激活
     *
     * @param $plugin
     *
     * @return bool
     */
    function wprs_is_plugin_active($plugin)
    {
        return in_array($plugin, (array)get_option('active_plugins', [])) || wprs_is_plugin_active_for_network($plugin);
    }
}


if ( ! function_exists('wprs_is_plugin_active_for_network')) {
    /**
     * 检查网络插件是否已激活
     *
     * @param $plugin
     *
     * @return bool
     */
    function wprs_is_plugin_active_for_network($plugin)
    {
        if ( ! is_multisite()) {
            return false;
        }

        $plugins = get_site_option('active_sitewide_plugins');
        if (isset($plugins[ $plugin ])) {
            return true;
        }

        return false;
    }
}