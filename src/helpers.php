<?php

/**
 * 获取嵌入的 Vue 组件
 *
 * @param  string  the file path of the file
 * @param  string  the script id
 *
 * @return void
 */
function wprs_get_vue_component_template($file_path, $id)
{
    if (file_exists($file_path)) {
        echo '<script type="text/x-template" id="' . $id . '">' . "\n";
        include_once $file_path;
        echo "\n" . '</script>' . "\n";
    }
}


/**
 * 判断是否在微信中打开
 */
if ( ! function_exists('wprs_is_wechat')) {
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


/**
 * 获取文章元数据，设置默认值
 *
 * @param        $post_id
 * @param string $key
 * @param bool   $single
 * @param bool   $default
 *
 * @return bool
 */
if ( ! function_exists('wprs_get_post_meta')) {
    function wprs_get_post_meta($post_id, $key = '', $single = false, $default = false)
    {

        $meta = get_post_meta($post_id, $key, $single);

        if ( ! $meta && $default) {
            return $default;
        }

        return $meta;
    }
}


/**
 * 获取用户元数据，可以设置默认值
 *
 * @param        $user_id
 * @param string $key
 * @param bool   $single
 * @param bool   $default
 *
 * @return bool
 */
if ( ! function_exists('wprs_get_user_meta')) {
    function wprs_get_user_meta($user_id, $key = '', $single = false, $default = false)
    {

        $meta = get_user_meta($user_id, $key, $single);

        if ( ! $meta && $default) {
            return $default;
        }

        return $meta;
    }
}


/**
 * 判断是否为 Ajax 请求
 *
 * @return bool
 */
if ( ! function_exists('wprs_is_ajax')) {
    function wprs_is_ajax()
    {
        if ( ! empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }
}


/**
 * 判断请求类型
 *
 * @param $type string admin|ajax|rest|cron|frontend
 *
 * @return bool
 */
if ( ! function_exists('wprs_is_request')) {
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


/**
 * 判断当前语言是否为英文
 *
 * @return bool
 */
if ( ! function_exists('wprs_is_en')) {
    function wprs_is_en()
    {

        $lang = get_bloginfo('language');

        if ($lang == 'en-US') {
            return true;
        }

        return false;
    }
}

/**
 * 获取用户的真实 IP
 *
 * @return mixed
 */
if ( ! function_exists('wprs_get_ip')) {
    function wprs_get_ip()
    {
        $client  = @$_SERVER[ 'HTTP_CLIENT_IP' ];
        $forward = @$_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        $remote  = $_SERVER[ 'REMOTE_ADDR' ];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}


if ( ! function_exists("wprs_order_no")) {
    /**
     * 生成订单号
     *
     * @package   helper
     *
     * @return string 订单号字符串
     */
    function wprs_order_no()
    {
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}


/**
 * 获取前端资源路径
 */
if ( ! class_exists('WprsJsonManifest')) {
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

            if (is_null($key)) {
                return $collection;
            }

            if (isset($collection[ $key ])) {
                return $collection[ $key ];
            }

            foreach (explode('.', $key) as $segment) {
                if ( ! isset($collection[ $segment ])) {
                    return $default;
                } else {
                    $collection = $collection[ $segment ];
                }
            }

            return $collection;
        }

    }
}


/**
 * 获取前端资源
 *
 * @param $filename string 文件名
 *
 * @return string 文件路径
 */
if ( ! function_exists('wprs_assets')) {
    function wprs_asset($filename)
    {
        $dist_path = get_theme_file_uri('/front/dist/');
        $directory = dirname($filename) . '/';
        $file      = basename($filename);
        static $manifest;

        if (empty($manifest)) {
            $manifest_path = get_theme_file_path('/front/dist/' . 'assets.json');
            $manifest      = new WprsJsonManifest($manifest_path);
        }

        if (array_key_exists($file, $manifest->get())) {
            return $dist_path . $directory . $manifest->get()[ $file ];
        } else {
            return $dist_path . $directory . $file;
        }
    }
}


if ( ! function_exists('wprs_get_archive_option')) {
    /**
     * 获取分类法存档设置
     *
     * @param        $type    string 分类法名称
     * @param        $name    string 设置名称
     * @param string $default 默认值
     *
     * @return mixed|string
     */
    function wprs_get_archive_option($type, $name, $default = '')
    {
        $value = carbon_get_theme_option($type . '_' . $name);

        if ( ! $value) {
            $value = $default;
        }

        return $value;
    }
}


if ( ! function_exists('wprs_get_taxonomy_type')) {
    /**
     * 获取个分类法关联的第一个文章类型，在分类法存档页使用
     */
    function wprs_get_taxonomy_type()
    {
        $taxonomy   = get_query_var('taxonomy');
        $tax_object = get_taxonomy($taxonomy);
        $type       = $tax_object->object_type[ 0 ];

        return $type;
    }
}


if ( ! function_exists('wprs_get_term_post_type')) {
    /**
     * 获取当前分类法项目所属的文章类型，在分类法项目存档页中使用
     *
     * @param bool $term
     *
     * @return mixed
     */
    function wprs_get_term_post_type($term = false)
    {

        if ( ! $term) {
            $term = get_queried_object();
        }

        $term = get_term($term);

        $taxonomy = get_taxonomy($term->taxonomy);

        return $taxonomy->object_type[ 0 ];
    }
}


if ( ! function_exists('wprs_get_page_type')) {
    function wprs_get_page_type()
    {

        if (is_category()) {

            $page_type = 'category';

        } elseif (is_tag()) {

            $page_type = 'tag';

        } elseif (is_author()) {

            $page_type = 'author';

        } elseif (is_year()) {

            $page_type = 'year';

        } elseif (is_month()) {

            $page_type = 'month';

        } elseif (is_day()) {

            $page_type = 'day';

        } elseif (is_tax('post_format')) {

            $page_type = 'format';

        } elseif (is_post_type_archive()) {

            $page_type = 'type';

        } elseif (is_page()) {

            $page_type = 'page';

        } elseif (is_single()) {

            $page_type = 'single';

        } elseif (is_singular()) {

            $page_type = 'singular';

        } elseif (is_tax()) {

            $page_type = 'tax';

        } else {

            $page_type = 'index';

        }

        return $page_type;
    }
}


/**
 * 获取设置，具体页面设置覆盖主题全局设置
 * 优先级: 页面 > 父级页面 > 分类 > 存档 > 全局 > 函数默认
 *
 * @todo: 添加自定义工具支持
 *
 * @param $name
 * @param $default
 *
 * @return bool|string
 */
if ( ! function_exists('wprs_get_page_settings')) {
    function wprs_get_page_settings($name, $default = '')
    {

        $global_settings = carbon_get_theme_option($name);

        if (is_page() || is_single() || is_singular()) {

            $post     = get_queried_object();
            $settings = carbon_get_post_meta($post->ID, $name);

            if ( ! $settings && $post->post_parent) {
                $settings = carbon_get_post_meta($post->post_parent, $name);
            }

        } elseif (is_category() || is_tag() || is_tax()) {

            $settings = carbon_get_term_meta(get_queried_object_id(), $name);

            if ( ! $settings) {
                $settings = wprs_get_archive_option(wprs_get_term_post_type(), $name);
            }

        } elseif (is_post_type_archive()) {

            $post_type = get_queried_object()->name;
            $settings  = wprs_get_archive_option($post_type, $name);

        } else {

            $settings = $global_settings;

        }

        if ('' === $settings) {
            $settings = $global_settings;
        }

        if ('' === $settings) {
            $settings = $default;
        }

        return $settings;
    }
}


if ( ! function_exists('wprs_get_current_url')) {
    /**
     * 获取当前 URL
     *
     * @return bool|string
     */
    function wprs_get_current_url()
    {
        $url = false;

        if (isset($_SERVER[ 'SERVER_ADDR' ])) {
            $is_https   = isset($_SERVER[ 'HTTPS' ]) && 'on' == $_SERVER[ 'HTTPS' ];
            $protocol   = 'http' . ($is_https ? 's' : '');
            $host       = isset($_SERVER[ 'HTTP_HOST' ])
                ? $_SERVER[ 'HTTP_HOST' ]
                : $_SERVER[ 'SERVER_ADDR' ];
            $port       = $_SERVER[ 'SERVER_PORT' ];
            $path_query = $_SERVER[ 'REQUEST_URI' ];

            $url = sprintf('%s://%s%s%s',
                $protocol,
                $host,
                $is_https
                    ? (443 != $port ? ':' . $port : '')
                    : (80 != $port ? ':' . $port : ''),
                $path_query
            );
        }

        return $url;
    }
}


if ( ! function_exists('wprs_get_domain')) {
    /**
     * 获取网址的域名
     *
     * @param $url
     *
     * @return bool|mixed|string
     */
    function wprs_get_domain($url)
    {
        $host = @parse_url($url, PHP_URL_HOST);
        // If the URL can't be parsed, use the original URL
        // Change to "return false" if you don't want that
        if ( ! $host) {
            $host = $url;
        }
        // The "www." prefix isn't really needed if you're just using
        // this to display the domain to the user
        if (substr($host, 0, 4) == "www.") {
            $host = substr($host, 4);
        }
        // You might also want to limit the length if screen space is limited
        if (strlen($host) > 50) {
            $host = substr($host, 0, 47) . '...';
        }

        return $host;
    }
}


if ( ! function_exists('wprs_get_social_icon')) {
    /**
     * 获取社交图标名称，可以自动添加 font-awesome 图标
     *
     * @param $url
     *
     * @return mixed
     */
    function wprs_get_social_icon($url)
    {

        $domain = wprs_get_domain($url);
        $icon   = explode('.', $domain)[ 0 ];

        return $icon;

    }
}


/**
 * 求两个数的最大公因式
 *
 * @param $a
 * @param $b
 *
 * @return float|int|mixed
 */
if ( ! function_exists('wprs_get_base_number')) {
    function wprs_get_base_number($a, $b)
    {

        $a = abs($a);
        $b = abs($b);

        if ($a < $b) {
            list($b, $a) = [$a, $b];
        }

        if ($b == 0) {
            return $a;
        }

        $r = $a % $b;

        while ($r > 0) {
            $a = $b;
            $b = $r;
            $r = $a % $b;
        }

        return $b;
    }
}


/**
 * 简化分数
 *
 * @param $num
 * @param $den
 *
 * @return string
 */
if ( ! function_exists('wprs_ratio_simplify')) {
    function wprs_ratio_simplify($num, $den)
    {
        $g = wprs_get_base_number($num, $den);

        if ($g == 0) {
            return 'is-' . $num . 'by' . $den;
        } else {
            return 'is-' . $num / $g . 'by' . $den / $g;
        }

    }
}


/**
 * 小数转化为分数
 *
 * @param       $n
 * @param float $tolerance
 *
 * @return string
 */
if ( ! function_exists('wprs_float2rat')) {
    function wprs_float2rat($n, $tolerance = 1.e-6)
    {
        $h1 = 1;
        $h2 = 0;
        $k1 = 0;
        $k2 = 1;
        $b  = 1 / $n;
        do {
            $b   = 1 / $b;
            $a   = floor($b);
            $aux = $h1;
            $h1  = $a * $h1 + $h2;
            $h2  = $aux;
            $aux = $k1;
            $k1  = $a * $k1 + $k2;
            $k2  = $aux;
            $b   = $b - $a;
        } while (abs($n - $h1 / $k1) > $n * $tolerance);

        return "$h1/$k1";
    }
}


/**
 * 获取主分类
 *
 * @param null $post_id
 *
 * @return array|null|\WP_Error|\WP_Term
 */
if ( ! function_exists('wprs_category_get_primary')) {
    function wprs_category_get_primary($post_id = null)
    {

        if ( ! $post_id) {
            $post_id = get_the_ID();
        }

        $primary_cat_id = get_post_meta($post_id, '_yoast_wpseo_primary_category', true);

        if ( ! $primary_cat_id) {
            $categories     = wp_get_post_terms($post_id, 'category');
            $primary_cat_id = wp_list_pluck($categories, 'term_id')[ 0 ];
        }

        return get_term($primary_cat_id);
    }
}


/**
 * 更新文章状态
 *
 * @param $post_id
 * @param $status
 */
if ( ! function_exists('wprs_update_post_status')) {
    function wprs_update_post_status($post_id, $status)
    {
        global $wpdb;

        $wpdb->update($wpdb->posts, ['post_status' => $status], ['ID' => $post_id]);
    }
}


/**
 * 生成随机的字母+数字字符串
 *
 * @param int $length
 *
 * @return string
 */
if ( ! function_exists('wprs_str_random')) {
    function wprs_str_random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}


/**
 * 判断是否安装了数据表
 *
 * @param int $table_name , 不带前缀的数据表名称
 *
 * @return bool
 */
if ( ! function_exists('wprs_is_table_installed')) {
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

if ( ! function_exists('wprs_get_queried_object_name')) {
    function wprs_get_queried_object_name()
    {

    }
}
