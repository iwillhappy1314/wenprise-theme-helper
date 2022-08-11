<?php

if (!function_exists('wprs_data_templates')) {
    /**
     * 获取存档页面模板
     *
     * @param string $dir          模板文件所在的目录名称
     * @param string $default_path 模板文件所在默认目录名称，可为空
     * @param string $name         注释名称
     *
     * @return array
     */
    function wprs_data_templates($dir = 'templates', $default_path = '', $name = 'Loop Template Name')
    {

        $headers = [
            'name' => $name,
        ];

        $default_template_dir = $default_path . $dir;
        $parent_template_dir  = get_parent_theme_file_path($dir);
        $theme_template_dir   = get_theme_file_path($dir);

        $templates_empty       = ['' => ['name' => __('Select a template', 'wprs')]];
        $templates_in_default  = [];
        $templates_in_theme    = [];
        $templates_in_template = [];

        // 插件中的模板
        if (is_dir($default_template_dir)) {
            $templates_in_default = wprs_get_templates_in_path($default_template_dir, $headers);
        }

        // 主题中的模板
        if (is_dir($theme_template_dir)) {
            $templates_in_template = wprs_get_templates_in_path($parent_template_dir, $headers);
            $templates_in_theme    = wprs_get_templates_in_path($theme_template_dir, $headers);
        }

        // 合并插件和主题中的模板，优先使用主题中模板
        $templates = array_merge($templates_empty, $templates_in_default, $templates_in_template, $templates_in_theme);

        $result = [];
        foreach ($templates as $key => $name) {
            $result[$key] = $name['name'];
        }

        ksort($result);

        return $result;
    }
}


if ( ! function_exists('wprs_data_post_types')) {
    /**
     * 获取文章类型数组
     *
     * @param $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_post_types($show_empty = true)
    {

        $args_type = [
            'public'   => true,
            '_builtin' => false,
        ];

        $post_types = get_post_types($args_type, 'objects');

        $output = [
            'post' => __('Post', 'wprs'),
            'page' => __('Page', 'wprs'),
        ];

        foreach ($post_types as $post_type) {
            $output[ $post_type->name ] = $post_type->label;
        }

        $empty = [
            '' => sprintf('%s', __('Select a content type', 'wprs')),
        ];

        if ($show_empty) {
            $output = $empty + $output;
        }

        return $output;
    }
}


if ( ! function_exists('wprs_data_taxonomies')) {

    /**
     * 获取分类方法数组
     *
     * @param $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_taxonomies($show_empty = true)
    {

        $empty = [
            '' => sprintf('%s', __('Select a taxonomy', 'wprs')),
        ];

        $output = [
            'category' => __('Category', 'wprs'),
            'post_tag' => __('Tags', 'wprs'),
        ];

        $args = [
            'public'   => true,
            '_builtin' => false,
        ];

        $taxonomies = get_taxonomies($args);

        foreach ($taxonomies as $taxonomy) {
            $tax = get_taxonomy($taxonomy);
            if (( ! $tax->show_tagcloud || empty($tax->labels->name))) {
                continue;
            }
            $output[ esc_attr($taxonomy) ] = esc_attr($tax->labels->name);
        }

        if ($show_empty) {
            $output = $empty + $output;
        }

        return $output;
    }
}


if ( ! function_exists('wprs_data_terms')) {
    /**
     * 获取分类法项目数组
     *
     * @param string $taxonomy
     * @param int    $parent
     * @param        $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_terms($taxonomy = 'post_tag', $parent = 0, $show_empty = true)
    {
        $terms = get_terms($taxonomy, [
            'parent'     => $parent,
            'hide_empty' => false,
        ]);

        $output = [];

        if (is_wp_error($terms)) {
            return $output;
        }

        foreach ($terms as $term) {

            $output[ $term->term_id ] = $term->name;
            $term_children            = get_term_children($term->term_id, $taxonomy);

            if (is_wp_error($term_children)) {
                continue;
            }

            foreach ($term_children as $term_child_id) {

                $term_child = get_term_by('id', $term_child_id, $taxonomy);

                if (is_wp_error($term_child)) {
                    continue;
                }

                $output[ $term_child->term_id ] = $term_child->name;
            }

        }

        $empty = [
            '' => sprintf('%s', __('Select a term', 'wprs')),
        ];

        if ($show_empty) {
            $output = $empty + $output;
        }

        return $output;
    }
}


if ( ! function_exists('wprs_data_posts')) {
    /**
     * 获取文章数组
     *
     * @param string $type
     * @param        $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_posts($type = "post", $show_empty = true)
    {
        $args = [
            'post_type'      => $type,
            'posts_per_page' => '-1',
        ];

        $posts = get_posts($args);

        $output = wp_list_pluck($posts, 'post_title', 'ID');

        $empty = [
            '' => sprintf('%s', __('Select Content', 'wprs')),
        ];

        if ($show_empty) {
            $output = $empty + $output;
        }

        return $output;
    }
}


if ( ! function_exists('wprs_data_image_sizes')) {
    /**
     * 获取图片尺寸数组
     *
     * @return array
     */
    function wprs_data_image_sizes()
    {
        $image_sizes_orig   = get_intermediate_image_sizes();
        $image_sizes_orig[] = 'full';
        $image_sizes        = [];

        foreach ($image_sizes_orig as $size) {
            $image_sizes[ $size ] = $size;
        }

        return $image_sizes;
    }
}


if ( ! function_exists('wprs_data_themes')) {
    /**
     * 获取主题
     *
     * @param        $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_themes($show_empty = true)
    {
        $themes = wp_get_themes();

        $options = wp_list_pluck($themes, 'Name', 'template');

        $empty = [
            '' => sprintf('%s', __('Responsive', 'wprs')),
        ];

        if ($show_empty) {
            $options = $empty + $options;
        }

        return $options;
    }
}


if ( ! function_exists('wprs_data_colors')) {
    /**
     * 获取颜色选项
     *
     * @return array
     */
    function wprs_data_colors()
    {
        $output              = [];
        $output[]            = __('Default', 'wprs');
        $output[ 'success' ] = __('Success', 'wprs');
        $output[ 'info' ]    = __('Info', 'wprs');
        $output[ 'warning' ] = __('Warning', 'wprs');
        $output[ 'danger' ]  = __('Danger', 'wprs');

        return $output;
    }
}


if ( ! function_exists('wprs_data_sizes')) {
    /**
     * 获取尺寸选项
     *
     * @return array
     */
    function wprs_data_sizes()
    {
        return [
            ''     => __('Zero', 'wprs'),
            'auto' => __('Auto', 'wprs'),
            'xxs'  => __('xxSmall', 'wprs'),
            'xs'   => __('xSmall', 'wprs'),
            'sm'   => __('Small', 'wprs'),
            'md'   => __('Medium', 'wprs'),
            'lg'   => __('Large', 'wprs'),
            'xl'   => __('xLarge', 'wprs'),
        ];
    }
}


if ( ! function_exists('wprs_data_user')) {
    /**
     * 根据角色获取用户选项数组
     *
     * @param        $role
     * @param        $show_empty bool 是否显示空值
     *
     * @return array
     */
    function wprs_data_user($role, $show_empty = true)
    {
        $users = get_users([
            'role' => $role,
        ]);

        $options = wp_list_pluck($users, 'display_name', 'ID');

        $empty = [
            '' => sprintf('%s', __('Select a user', 'wprs')),
        ];

        if ($show_empty) {
            $options = $empty + $options;
        }

        return $options;
    }
}


if ( ! function_exists('wprs_data_roles')) {
    /**
     * 获取用户角色
     *
     * @return array
     */
    function wprs_data_roles()
    {
        return wp_roles()->role_names;
    }
}
