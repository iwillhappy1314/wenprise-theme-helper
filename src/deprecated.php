<?php
/**
 * Created by PhpStorm.
 * User: amoslee
 * Date: 2019-03-29
 * Time: 17:09
 */

if ( ! function_exists('wprs_get_post_meta')) {
    /**
     * 获取文章元数据，设置默认值
     *
     * @param        $post_id
     * @param string $key
     * @param bool   $single
     * @param bool   $default
     *
     * @return bool
     *
     * @deprecated
     */
    function wprs_get_post_meta($post_id, $key = '', $single = false, $default = false)
    {

        $meta = get_post_meta($post_id, $key, $single);

        if ( ! $meta && $default) {
            return $default;
        }

        return $meta;
    }
}


if ( ! function_exists('wprs_get_user_meta')) {
    /**
     * 获取用户元数据，可以设置默认值
     *
     * @param        $user_id
     * @param string $key
     * @param bool   $single
     * @param bool   $default
     *
     * @return bool
     *
     * @deprecated
     */
    function wprs_get_user_meta($user_id, $key = '', $single = false, $default = false)
    {

        $meta = get_user_meta($user_id, $key, $single);

        if ( ! $meta && $default) {
            return $default;
        }

        return $meta;
    }
}


if ( ! function_exists('wprs_get_template_option')) {
    /**
     * @param string $dir
     * @param string $default_path
     *
     * @return array
     *
     * @deprecated
     */
    function wprs_get_template_option($dir = 'templates', $default_path = '')
    {
        return wprs_data_templates($dir, $default_path);
    }
}


if ( ! function_exists('wprs_get_vue_component_template')) {
    /**
     * 获取嵌入的 Vue 组件
     *
     * @param string  the file path of the file
     * @param string  the script id
     *
     * @return void
     *
     * @deprecated
     */
    function wprs_get_vue_component_template($file_path, $id)
    {
        if (file_exists($file_path)) {
            echo '<script type="text/x-template" id="' . $id . '">' . "\n";
            include_once $file_path;
            echo "\n" . '</script>' . "\n";
        }
    }
}


if ( ! function_exists('wprs_bulma_menu')) {
    /**
     * 显示 Bulma 菜单
     *
     * @param $theme_location string 菜单位置
     *
     * @deprecated
     */
    function wprs_bulma_menu($theme_location)
    {
        if (($theme_location) && ($locations = get_nav_menu_locations()) && isset($locations[ $theme_location ])) {
            $menu       = get_term($locations[ $theme_location ], 'nav_menu');
            $menu_items = wp_get_nav_menu_items($menu->term_id);

            $menu_list = '<div id="main-nav" class="navbar-menu">' . "\n";
            $menu_list .= '<div class="navbar-end">';

            /**
             * 所有菜单
             */
            foreach ($menu_items as $menu_item) {
                if ($menu_item->menu_item_parent === 0) {

                    $menu_children_array = [];

                    $menu_item_url = $menu_item->url;

                    // 处理绝对路径为首页的情况
                    if ($menu_item->url === '/') {
                        $menu_item_url = home_url('/');
                    }

                    $is_current = (wprs_get_current_url() === $menu_item_url) ? 'is-active is-1' : '';

                    /**
                     * 二级菜单数组
                     */
                    $is_child_current = false;
                    foreach ($menu_items as $submenu) {
                        if ($submenu->menu_item_parent === $menu_item->ID) {
                            $is_current = '';
                            if (wprs_get_current_url() === $submenu->url) {
                                $is_child_current = true;
                                $is_current       = 'is-active is-2';
                            }

                            $menu_children_array[] = '<a class="navbar-item ' . $is_current . '" href="' . $submenu->url . '">' . $submenu->title . '</a>' . "\n";
                        }
                    }

                    if (count($menu_children_array) > 0) {

                        /**
                         * 显示二级菜单
                         */
                        $is_current_parent = ($is_child_current === '') ? '' : 'is-active-parent';
                        $is_current        = (wprs_get_current_url() === $menu_item_url) ? 'is-active is-0' : '';

                        $menu_list .= '<div class="navbar-item has-dropdown is-hoverable ' . $is_current_parent . '">' . "\n";
                        $menu_list .= '<a href="' . $menu_item->url . '" class="navbar-link ' . $is_current . '">' . $menu_item->title . ' </a>' . "\n";

                        $menu_list .= '<div class="navbar-dropdown is-boxed">' . "\n";
                        $menu_list .= implode("\n", $menu_children_array);
                        $menu_list .= '</div>' . "\n";
                        $menu_list .= '</div>' . "\n";

                    } else {

                        /**
                         * 顶级菜单
                         */
                        $menu_list .= '<a class="navbar-item ' . $is_current . '" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' . "\n";

                    }

                }

            }

            $menu_list .= '</div>' . "\n";
            $menu_list .= '</div>' . "\n";

        } else {
            $menu_list = '<!-- no menu defined in location "' . $theme_location . '" -->';
        }
        echo $menu_list;
    }
}


if ( ! function_exists('wprs_get_archive_title')) {
    /**
     * @return mixed
     *
     * @deprecated
     */
    function wprs_get_archive_title()
    {
        return wprs_get_page_title();
    }
}


if ( ! function_exists('wprs_get_archive_description')) {
    /**
     * @return mixed
     *
     * @deprecated
     */
    function wprs_get_archive_description()
    {
        return wprs_get_page_description();
    }
}


if ( ! class_exists('WprsJsonManifest')) {
    /**
     * 获取前端资源路径
     *
     * @deprecated
     */
    class WprsJsonManifest
    {
        private $manifest;

        public function __construct($manifest_path)
        {

            if (file_exists($manifest_path)) {
                $this->manifest = json_decode(file_get_contents($manifest_path), true);
            } else {
                $this->manifest = [];
            }

        }

        public function get()
        {
            return $this->manifest;
        }

        /**
         * @param string $key
         * @param null   $default
         *
         * @return array|mixed|null
         */
        public function getPath($key = '', $default = null)
        {
            $collection = $this->manifest;

            if ($key === null) {
                return $collection;
            }

            if (isset($collection[ $key ])) {
                return $collection[ $key ];
            }

            foreach (explode('.', $key) as $segment) {
                if ( ! isset($collection[ $segment ])) {
                    return $default;
                }

                $collection = $collection[ $segment ];
            }

            return $collection;
        }

    }
}