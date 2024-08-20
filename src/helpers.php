<?php

use Nette\Utils\Finder;

if ( ! function_exists('wprs_get_ip')) {
    /**
     * 获取用户的真实 IP
     *
     * @return mixed
     */
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


if ( ! function_exists('wprs_order_no')) {
    /**
     * 生成订单号
     *
     * @return string 订单号字符串
     * @package   helper
     *
     */
    function wprs_order_no()
    {
        return date('Ymd') . str_pad(wp_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    }
}


if ( ! function_exists('wprs_get_manifest')) {
    /**
     * 获取前端资源路径
     *
     * @param $manifest_path
     *
     * @return array
     */
    function wprs_get_manifest($manifest_path)
    {
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
        } else {
            $manifest = [];
        }

        return $manifest;
    }
}


if ( ! function_exists('wprs_env')) {
    /**
     * 获取环境变量
     */
    function wprs_env()
    {
        return defined('ENV') ? ENV : 'production';
    }
}


if ( ! function_exists('wprs_assets')) {
    /**
     * 获取前端资源
     *
     * @param $filename string 文件名
     *
     * @return string 文件路径
     */
    function wprs_assets($filename)
    {
        $dist_path = get_theme_file_path('/front/dist/');
        $dist_uri  = get_theme_file_uri('/front/dist/');

        if ( ! is_dir($dist_path)) {
            $dist_path = get_theme_file_path('/frontend/dist/');
            $dist_uri  = get_theme_file_uri('/frontend/dist/');
        }

        $directory = dirname($filename) . '/';
        $file      = basename($filename);
        static $manifest;

        if (empty($manifest)) {
            $manifest_path = $dist_path . 'assets.json';
            $manifest      = wprs_get_manifest($manifest_path);
        }

        if (array_key_exists($file, $manifest) && wprs_env() !== 'local') {
            return $dist_uri . $directory . $manifest[ $file ];
        }

        return $dist_uri . $directory . $file;
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
        $value = get_option($type . '_' . $name);

        if ( ! $value) {
            $value = get_option('_' . $type . '_' . $name);
        }

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

        return $tax_object->object_type[ 0 ];
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


/**
 * 获取文章顶级分类
 *
 * @param $post_id  int 文章 ID
 * @param $taxonomy string 分类方法
 *
 * @return string|bool
 */
if ( ! function_exists('wprs_get_post_root_term')) {
    function wprs_get_post_root_term($post_id, $taxonomy)
    {
        $root_term  = false;
        $post_terms = wp_get_post_terms($post_id, $taxonomy);

        if ( ! is_wp_error($post_terms)) {
            foreach ($post_terms as $term) {
                if ($term->parent === 0) {
                    $root_term = $term->term_id;
                } else {
                    $root_term = get_ancestors($term->term_id, $taxonomy, 'taxonomy')[ 0 ];
                }
            }
        }

        return $root_term;
    }
}


if ( ! function_exists('wprs_get_page_type')) {
    /**
     * 获取页面类型
     *
     * @return string
     */
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


if ( ! function_exists('wprs_get_page_settings')) {
    /**
     * 获取设置，具体页面设置覆盖主题全局设置
     * 优先级: 页面 > 父级页面 > 分类 > 存档 > 全局 > 函数默认
     *
     * @param $name
     * @param $default
     *
     * @return bool|string
     * @todo: 添加自定义工具支持
     *
     */
    function wprs_get_page_settings($name, $default = '')
    {

        $global_settings = get_option($name);

        if (is_page() || is_single() || is_singular()) {

            $post     = get_queried_object();
            $settings = get_post_meta($post->ID, $name, true);

            if ( ! $settings && $post->post_parent) {
                $settings = get_post_meta($post->post_parent, $name, true);
            }

        } elseif (is_category() || is_tag() || is_tax()) {

            $settings = get_post_meta(get_queried_object_id(), $name, true);

            if ( ! $settings) {
                $settings = wprs_get_archive_option(wprs_get_term_post_type(), $name);
            }

        } elseif (is_post_type_archive()) {

            $post_type = get_queried_object()->name;
            $settings  = wprs_get_archive_option($post_type, $name);

        } else {

            $settings = $global_settings;

        }

        if ( ! $settings) {
            $settings = $global_settings;
        }

        if ( ! $settings) {
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
            $is_https   = isset($_SERVER[ 'HTTPS' ]) && 'on' === $_SERVER[ 'HTTPS' ];
            $protocol   = 'http' . ($is_https ? 's' : '');
            $host       = isset($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ 'SERVER_ADDR' ];
            $port       = $_SERVER[ 'SERVER_PORT' ];
            $path_query = $_SERVER[ 'REQUEST_URI' ];

            $url = sprintf('%s://%s%s%s',
                $protocol,
                $host,
                $is_https ? (443 != $port ? ':' . $port : '') : (80 != $port ? ':' . $port : ''),
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
        $pieces = parse_url($url);
        $domain = $pieces[ 'host' ] ?? '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs[ 'domain' ];
        }

        return false;
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

        return explode('.', $domain)[ 0 ];
    }
}


if ( ! function_exists('wprs_get_base_number')) {
    /**
     * 求两个数的最大公因式
     *
     * @param $a
     * @param $b
     *
     * @return float|int|mixed
     */
    function wprs_get_base_number($a, $b)
    {

        $a = abs($a);
        $b = abs($b);

        if ($a < $b) {
            [$b, $a] = [$a, $b];
        }

        if ($b === 0) {
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


if ( ! function_exists('wprs_ratio_simplify')) {
    /**
     * 简化分数
     *
     * @param $num
     * @param $den
     *
     * @return string
     */
    function wprs_ratio_simplify($num, $den)
    {
        $g = wprs_get_base_number($num, $den);

        if ($g === 0) {
            return 'is-' . $num . 'by' . $den;
        }

        return 'is-' . $num / $g . 'by' . $den / $g;

    }
}


if ( ! function_exists('wprs_float2rat')) {
    /**
     * 小数转化为分数
     *
     * @param       $n
     * @param float $tolerance
     *
     * @return string
     */
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


if ( ! function_exists('wprs_category_get_primary')) {
    /**
     * 获取主分类
     *
     * @param null $post_id
     *
     * @return array|null|\WP_Error|\WP_Term
     */
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


if ( ! function_exists('wprs_update_post_status')) {
    /**
     * 更新文章状态
     *
     * @param $post_id
     * @param $status
     */
    function wprs_update_post_status($post_id, $status)
    {
        global $wpdb;

        $wpdb->update($wpdb->posts, ['post_status' => $status], ['ID' => $post_id]);
    }
}


if ( ! function_exists('wprs_string_mask')) {
    /**
     * 隐藏字符串中的部分字符
     *
     * @param     $str
     * @param int $start
     * @param int $length
     *
     * @return mixed
     */
    function wprs_string_mask($str, $start = 0, $length = 4)
    {
        $mask = preg_replace("/\S/", "*", $str);
        if (is_null($length)) {
            $mask = substr($mask, $start);
            $str  = substr_replace($str, $mask, $start);
        } else {
            $mask = substr($mask, $start, $length);
            $str  = substr_replace($str, $mask, $start, $length);
        }

        return $str;
    }
}


if ( ! function_exists('wprs_str_random')) {
    /**
     * 生成随机的字母+数字字符串
     *
     * @param int $length
     *
     * @return string
     * @throws \Exception
     */
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


if ( ! function_exists('wprs_trim_words')) {
    /**
     * 裁剪文本
     *
     * @param      $input
     * @param      $length
     * @param bool $ellipses
     * @param bool $strip_html
     *
     * @return bool|string
     */
    function wprs_trim_words($input, $length, $ellipses = true, $strip_html = true)
    {
        //strip tags, if desired
        if ($strip_html) {
            $input = wp_strip_all_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space   = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }
}


if ( ! function_exists('add_filters')) {
    /**
     * 应用多个 Filter 到回调
     *
     * @param mixed   $filters
     * @param mixed   $callback
     * @param integer $priority
     * @param integer $arguments
     *
     * @return true
     */
    function add_filters($filters, $callback, $priority = 10, $arguments = 1)
    {
        foreach ($filters as $index => $filter) {
            add_filter(
                $filter,
                $callback,
                (int)is_array($priority) ? $priority[ $index ] : $priority,
                (int)is_array($arguments) ? $arguments[ $index ] : $arguments
            );
        }

        return true;
    }
}


if ( ! function_exists('add_actions')) {
    /**
     * 应用多个 Action 到回调
     *
     * @param mixed   $actions
     * @param mixed   $callback
     * @param integer $priority
     * @param integer $arguments
     *
     * @return true
     */
    function add_actions($actions, $callback, $priority = 10, $arguments = 1)
    {
        return add_filters($actions, $callback, $priority, $arguments);
    }
}


if ( ! function_exists('wprs_step_class')) {
    /**
     * 获取步骤类名
     *
     * @param $step_name       string
     * @param $steps           array
     * @param $step_order      int
     *
     * @return string
     */
    function wprs_step_class($step_name, $steps, $step_order)
    {
        $step_key = array_search($step_name, $steps, true);

        if ($step_key === $step_order) {
            return 'c-step--active';
        }

        if ($step_key < $step_order) {
            return 'c-step--complete';
        }

        return 'c-step--disable';

    }
}


if ( ! function_exists('wprs_user_get_roles')) {
    /**
     * 根据用户 ID 获取该用户的角色
     *
     * @param $user_id
     *
     * @return array 该用户的角色，一般只有一个元素
     */
    function wprs_user_get_roles($user_id = 0)
    {
        if ($user_id === 0) {
            $user_id = get_current_user_id();
        }

        $user = get_userdata($user_id);

        return empty($user) ? [] : $user->roles;
    }
}


if ( ! function_exists('wprs_user_get_prev_role')) {
    /**
     * 获取下一流程的用户角色，CRM 系统中，用来过滤上一流程角色审核通过的客户
     *
     * @param $roles        array 角色名称数组
     * @param $user_id      int 用户 ID
     *
     * @return string|bool 返回角色名称，如果是第一级，返回 True
     */
    function wprs_user_get_prev_role($roles, $user_id = 0)
    {
        $user_role          = wprs_user_get_roles($user_id)[ 0 ];
        $current_role_level = array_search($user_role, $roles, true);

        $prev_role = $roles[ $current_role_level - 1 ];

        if ( ! $current_role_level || $current_role_level === 0) {
            return false;
        }

        return $prev_role;
    }

}


if ( ! function_exists('wprs_user_get_next_role')) {
    /**
     * 获取下一流程的用户角色，CRM 系统中，用来通知下一流程进行审核操作
     *
     * @param $roles        array 角色名称数组
     * @param $user_id      int 用户 ID
     *
     * @return string|bool 返回角色名称，如果是最后一级返回 false
     */
    function wprs_user_get_next_role($roles, $user_id = 0)
    {
        $user_role          = wprs_user_get_roles($user_id)[ 0 ];
        $current_role_level = array_search($user_role, $roles, true);

        $next_role = $roles[ $current_role_level + 1 ];

        if ( ! $current_role_level || $current_role_level === count($roles)) {
            return false;
        }

        return $next_role;
    }
}


if ( ! function_exists('wprs_class')) {
    /**
     * 转换数组为 Class
     *
     * @param string|array $class
     * @param string|array $remove
     */
    function wprs_class($class = '', $remove = '')
    {
        if ( ! empty($remove)) {
            $class = array_diff((array)$class, (array)$remove);
        }

        echo 'class="' . join(' ', $class) . '"';
    }
}


if ( ! function_exists('wprs_get_templates_in_path')) {
    /**
     * 获取路径中的指定文件
     *
     * @param $path
     * @param $headers
     *
     * @return array
     */
    function wprs_get_templates_in_path($path, $headers = [])
    {
        $templates = [];

        if (is_dir($path)) {
            $files = glob($path . '/*.php');

            foreach ($files as $key => $file) {

                $filename  = $file->getFilename();
                $file_info = get_file_data($key, $headers);

                // 获取模板名称
                if (isset($file_info[ 'name' ]) && $file_info[ 'name' ] !== '') {
                    $templates[ $filename ] = $file_info;
                }
            }
        }

        return $templates;
    }
}


if ( ! function_exists('wprs_content_dir')) {
    /**
     * 获取 wp-content 中的自定义子目录，如果不存在，新建目录
     *
     * @param string $dir wp-content 子目录名称
     *
     * @return string
     */
    function wprs_content_dir($dir = '')
    {

        $dir = ltrim($dir, '/');
        $dir = rtrim($dir, '/');

        $directory = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $dir;

        if ( ! is_dir($dir)) {
            wp_mkdir_p($dir);
        }

        return $directory;
    }
}


/**
 * 渲染 Blade 模版
 *
 * @param string $template 模版路径，支持点路径
 * @param array  $data     模版中需要的数据
 *
 * @return mixed
 */
if ( ! function_exists('wprs_render_view')) {
    function wprs_render_view($template, $data = [])
    {
        if (class_exists('\Jenssegers\Blade\Blade')) {
            $view_dir  = get_theme_file_path('views');
            $cache_dir = wprs_content_dir('blade-cache');

            $blade = new \Jenssegers\Blade\Blade($view_dir, $cache_dir);

            return $blade->make($template, $data)
                         ->render();
        }

        wp_die('Please install jenssegers/blade library');
    }
}


if ( ! function_exists('wprs_value')) {
    /**
     * 获取指定值的默认值
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function wprs_value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}


if ( ! function_exists('wprs_data_get')) {
    /**
     * 使用点注释获取数据
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function wprs_data_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[ $key ])) {
            return $array[ $key ];
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return wprs_value($default);
            }

            $array = $array[ $segment ];
        }

        return $array;
    }
}


if ( ! function_exists('wprs_input_get')) {
    /**
     * @param $key
     * @param $default
     *
     * @return array|mixed
     */
    function wprs_input_get($key, $default = null)
    {
        return wprs_data_get($_REQUEST, $key, $default);
    }
}