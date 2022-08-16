<?php
/**
 * 模版标签
 */

if ( ! function_exists('add_action')) {
    return;
}

if ( ! function_exists('wprs_bulma_pagination')) {
    /**
     * Bulma CSS 数字分页
     *
     * @param string $query
     * @param string $pages
     * @param int    $range
     */
    function wprs_bulma_pagination($query = '', $pages = '', $range = 5)
    {
        $show_items = ($range * 2) + 1;

        global $paged;
        if (empty($paged)) {
            $paged = 1;
        }

        if ( ! $query) {
            global $wp_query;
            $wprs_query = $wp_query;
        } else {
            $wprs_query = $query;
        }

        if ($pages === '') {
            $pages = $wprs_query->max_num_pages;
            if ( ! $pages) {
                $pages = 1;
            }
        }

        if (1 !== $pages) {
            echo '<nav class="pagination" role="navigation" aria-label="pagination">';
            if ($paged > 2 && $paged > $range + 1 && $show_items < $pages) {
                echo '<a class="pagination-previous" aria-label="Previous" href="' . get_pagenum_link(1) . '"><span aria-hidden="true">«</span></a>';
            }
            if ($paged > 1 && $show_items < $pages) {
                echo '<a class="pagination-previous" aria-label="Previous" href="' . get_pagenum_link($paged - 1) . '"><span aria-hidden="true"><</span></a>';
            }

            echo '<ul class="pagination-list">';

            for ($i = 1; $i <= $pages; $i++) {
                if (1 !== $pages && ( ! ($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $show_items)) {
                    if ($paged == $i) {
                        echo '<li><a class="pagination-link is-current" href="#">' . $i . '</a></li>';
                    } else {
                        echo '<li><a class="pagination-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                    }
                }
            }

            echo '</ul>';

            if ($paged < $pages) {
                echo '<a class="pagination-next" aria-label="Next" href="' . get_pagenum_link($paged + 1) . '"><span aria-hidden="true">></span></a>';
            }
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $show_items < $pages) {
                echo '<a class="pagination-next" aria-label="Next" href="' . get_pagenum_link($pages) . '"><span aria-hidden="true">»</span></a>';
            }
            echo '</nav>';
        }
    }
}


if ( ! function_exists('wprs_pagination')) {
    /**
     * 数字分页
     *
     * @param string $query
     * @param string $pages
     * @param int    $range
     */
    function wprs_pagination($query = '', $pages = '', $range = 5)
    {
        $show_items = ($range * 2) + 1;

        global $paged;
        if (empty($paged)) {
            $paged = 1;
        }

        if ( ! $query) {
            global $wp_query;
            $wprs_query = $wp_query;
        } else {
            $wprs_query = $query;
        }

        if ($pages === '') {
            $pages = $wprs_query->max_num_pages;
            if ( ! $pages) {
                $pages = 1;
            }
        }

        if (1 !== $pages) {
            echo '<ul class="pagination">';
            if ($paged > 2 && $paged > $range + 1 && $show_items < $pages) {
                echo '<li><a aria-label="Previous" href="' . get_pagenum_link(1) . '"><span aria-hidden="true">«</span></a></li>';
            }
            if ($paged > 1 && $show_items < $pages) {
                echo '<li><a aria-label="Previous" href="' . get_pagenum_link($paged - 1) . '"><span aria-hidden="true"><</span></a></li>';
            }

            for ($i = 1; $i <= $pages; $i++) {
                if (1 !== $pages && ( ! ($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $show_items)) {
                    if ($paged === $i) {
                        echo '<li class="active"><a href="#">' . $i . '</a></li>';
                    } else {
                        echo '<li><a href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                    }
                }
            }

            if ($paged < $pages) {
                echo '<li><a class="nextpostslink" aria-label="Next" href="' . get_pagenum_link($paged + 1) . '"><span aria-hidden="true">></span></a></li>';
            }
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $show_items < $pages) {
                echo '<li><a class="lastpostslink" aria-label="Next" href="' . get_pagenum_link($pages) . '"><span aria-hidden="true">»</span></a></li>';
            }
            echo '</ul>';
        }
    }
}


if ( ! function_exists('wprs_get_page_title')) {
    /**
     * 获取存档或文章标题作为页面标题使用
     *
     * @return mixed
     */
    function wprs_get_page_title()
    {
        if (is_category() || is_tag()) {

            $title = get_term_meta(get_queried_object_id(), 'title', true);

            if ( ! $title) {
                $title = sprintf(__('%s'), single_cat_title('', false));
            }

        } elseif (is_author()) {

            $title = sprintf(__('%s'), '<span class="vcard">' . get_the_author() . '</span>');

        } elseif (is_year()) {

            $title = sprintf(__('%s'), get_the_date(_x('Y', 'wprs', 'yearly archives date format')));

        } elseif (is_month()) {

            $title = sprintf(__('%s'), get_the_date(_x('F Y', 'wprs', 'monthly archives date format')));

        } elseif (is_day()) {

            $title = sprintf(__('%s'), get_the_date(_x('F j, Y', 'wprs', 'daily archives date format')));

        } elseif (is_tax('post_format')) {

            if (is_tax('post_format', 'post-format-aside')) {
                $title = _x('Asides', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-gallery')) {
                $title = _x('Galleries', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-image')) {
                $title = _x('Images', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-video')) {
                $title = _x('Videos', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-quote')) {
                $title = _x('Quotes', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-link')) {
                $title = _x('Links', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-status')) {
                $title = _x('Statuses', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-audio')) {
                $title = _x('Audio', 'wprs', 'post format archive title');
            } elseif (is_tax('post_format', 'post-format-chat')) {
                $title = _x('Chats', 'wprs', 'post format archive title');
            }

        } elseif (is_post_type_archive()) {

            $title = sprintf(__('%s'), post_type_archive_title('', false));

            $post_type    = get_queried_object()->name;
            $custom_title = wprs_get_archive_option($post_type, 'title');

            if ( ! empty($custom_title)) {
                $title = $custom_title;
            }

        } elseif (is_page() || is_single() || is_singular()) {

            $title = sprintf(__('%s'), get_the_title());

        } elseif (is_tax()) {

            $title = get_term_meta(get_queried_object_id(), 'title', true);

            if ( ! $title) {
                $title = sprintf(__('%1$s'), single_term_title('', false));
            }

        } else {

            $title = __('Archives', 'wprs');

        }

        /**
         * Filter the archive title.
         *
         * @param string $title Archive title to be displayed.
         *
         * @since 4.1.0
         *
         */
        return apply_filters('wprs_get_page_title', $title);
    }
}


if ( ! function_exists('wprs_get_page_description')) {
    /**
     * 获取存档描述
     *
     * @return mixed|string
     */
    function wprs_get_page_description()
    {

        $description = '';

        if (is_post_type_archive()) {
            $description = wprs_get_archive_option(get_queried_object()->name, 'description');
        } elseif (is_tax() || is_category() || is_tag()) {
            $description = get_the_archive_description();
        } else {
            if (has_excerpt()) {
                get_the_excerpt();
            }
        }

        return $description;
    }
}


if ( ! function_exists('wprs_image_size_attr')) {
    /**
     * 获取图像尺寸属性
     *
     * @param string|array $size
     *
     * @return array
     */
    function wprs_image_size_attr($size = 'is-400by300')
    {

        $size_class = '';
        $size_array = [400, 300];

        if (is_array($size)) {
            if (array_sum($size) !== 0) {
                $size_array = $size;

                if (isset($size[ 2 ]) && ($size[ 2 ] === 1)) {
                    $size_class = 'is-square ';
                } else {
                    $size_class = 'is-' . str_replace('/', 'by', wprs_float2rat($size[ 0 ] / $size[ 1 ]));
                }

                if (count($size) >= 2) {
                    unset($size_array[ 2 ]);
                }
            }
        } else {
            $size_array = explode('by', str_replace('is-', '', $size));
            $size_class = wprs_ratio_simplify($size_array[ 0 ], $size_array[ 1 ]);
        }

        // 原始图像尺寸，方便查找图像尺寸定义
        $size_class_base = 'is-' . join('by', $size_array);

        $size_array[] = 1;

        return [
            'size_class_base' => $size_class_base,
            'size_class'      => $size_class,
            'size_array'      => $size_array,
        ];
    }
}


if ( ! function_exists('wprs_post_thumbnail')) {
    /**
     * 显示文章缩略图，如果有相册，显示相册  wprs_get_attachment_image();
     *
     * @param        $post
     * @param string $size
     * @param bool   $icon
     * @param array  $attr
     *
     * @return string
     */
    function wprs_post_thumbnail($post, $size = 'thumbnail', $icon = false, $attr = [])
    {
        $html = '';

        if (is_int($post)) {
            $post = get_post($post);
        }

        $thumb_id = get_post_thumbnail_id($post);
        $gallery  = get_post_gallery(get_the_ID(), false);

        if ($gallery) {
            $gallery = $gallery[ 'ids' ];
        } else {
            $gallery = get_post_meta(get_the_ID(), 'gallery', true);
        }

        if ($gallery) {

            $gallary_ids = explode(',', $gallery);

            $html .= '<figure class="js-popup f-view f-overlay">';
            $html .= '<div class="js-slider" data-slick={"slidesToShow": 4, "slidesToScroll": 4}>';

            if (has_post_thumbnail()) {
                $html .= '<a class="js-gallery image" href="' . wp_get_attachment_image_url($thumb_id, 'full') . '">';
                $html .= wp_get_attachment_image($thumb_id, $size, $icon, $attr);
                $html .= '</a>';
            }

            foreach ($gallary_ids as $gallery_id) {
                $html .= '<a class="js-gallery image" href="' . wp_get_attachment_image_url($gallery_id, 'full') . '">';
                $html .= wp_get_attachment_image($gallery_id, $size, $icon, $attr);
                $html .= '</a>';
            }

            $html .= '</div>';

            $html .= wprs_thumbnail_mask($post);

            $html .= '</figure>';

        } elseif (has_post_thumbnail($post)) {

            $html .= '<figure class="js-popup f-view f-overlay">';
            $html .= '<div class="image">';
            $html .= wp_get_attachment_image($thumb_id, $size, $icon, $attr);
            $html .= '</div>';
            $html .= wprs_thumbnail_mask($post);
            $html .= '</figure>';

        }

        return $html;
    }
}


if ( ! function_exists('wprs_thumbnail_image')) {
    /**
     * 获取附件图像
     *
     * @param              $id
     * @param string|array $size
     * @param bool         $icon
     * @param array        $attr
     *
     * @return string
     */
    function wprs_thumbnail_image($id, $size = 'thumbnail', $icon = false, $attr = [])
    {
        $html = '<figure class="image">';
        $html .= wp_get_attachment_image($id, $size, $icon, $attr);
        $html .= '</figure>';

        return $html;
    }
}


if ( ! function_exists('wprs_thumbnail_mask')) {
    /**
     * 显示缩略图遮罩
     *
     * @param $post
     *
     * @return string
     */
    function wprs_thumbnail_mask($post)
    {

        if (is_int($post)) {
            $post = get_post($post);
        }

        $thumb_id   = get_post_thumbnail_id();
        $thumb_full = wp_get_attachment_image_url($thumb_id, 'full');

        $is_show_image_popup = wprs_get_page_settings('show_popup', 'show');

        $html = '<div class="is-overlay u-mask flex-center">';

        if ($is_show_image_popup === 'show') {
            $html .= '<a class="js-gallery" href="' . esc_url($thumb_full) . '"><i class="wpion-expand-arrows-alt2"></i></a>';
        }

        if ( ! is_singular($post)) {
            $html .= '<a class="js-post" href="' . get_the_permalink($post) . '"><i class="wpion-link1"></i></a>';
        }

        $html .= '</div>';

        return $html;
    }
}


if ( ! function_exists('wprs_breadcrumbs')) {
    /**
     * 显示面包屑导航
     */
    function wprs_breadcrumbs()
    {
        if (class_exists('Carbon_Breadcrumb_Trail')) {

            $breadcrumbs = new \Carbon_Breadcrumb_Trail([
                'glue'            => '',
                'link_before'     => '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">',
                'link_after'      => '</li>',
                'wrapper_before'  => '<ol class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">',
                'wrapper_after'   => '</ol>',
                'title_before'    => '<span itemprop="name">',
                'title_after'     => '</span>',
                'last_item_link'  => false,
                'home_item_title' => __('Home', 'wprs'),
            ]);

            $breadcrumbs->setup();

            echo $breadcrumbs->render(true); // WP_KSES strips itemprop, itemscope, etc, so bypassing!!!

        } elseif (function_exists('yoast_breadcrumb')) {

            yoast_breadcrumb('<div id="breadcrumbs">', '</div>');

        } else {

            echo 'Please install tyxla/carbon-breadcrumbs plugin or enable Yoast SEO Breadcrumb feature.';

        }

    }
}


/**
 * 添加 Yoast SEO 主分类支持
 */
add_action('carbon_breadcrumbs_after_setup_trail', function ($trail)
{
    global $post;
    if ( ! is_singular('post')) {
        return;
    }

    $cats = get_the_category($post->ID);
    if (empty($cats) || empty($cats[ 0 ])) {
        return;
    }
    $cats = wp_list_sort($cats, [
        'term_id' => 'ASC',
    ]);

    /**
     * Call the filter,
     * triggering YoastSEO primary category modification
     */
    $category_object = apply_filters('post_link_category', $cats[ 0 ], $cats, $post);

    $term_id = $category_object->term_id;

    /**
     * Taxonomy breadcrumb is inserted at 700
     * Removing it, and adding new one at the same priority
     */
    $trail->remove_item_by_priority(700);

    $terms = Carbon_Breadcrumb_Locator::factory('term', 'category');
    $items = $terms->get_items(700, $term_id);

    if ($items) {
        $trail->add_item($items);
    }
});

/**
 * 为面包屑导航添加微格式
 */
add_filter('carbon_breadcrumbs_item_attributes', function ($attributes, $item)
{
    if ( ! is_array($attributes)) {
        $attributes = [];
    }
    $attributes[ 'itemscope' ] = null;
    $attributes[ 'itemtype' ]  = 'http://schema.org/WebPage';
    $attributes[ 'itemprop' ]  = 'item';

    return $attributes;
}, 10, 2);


add_filter('carbon_breadcrumbs_item_output', function ($item_output, $item, $trail, $trail_renderer, $index)
{
    // Add Position
    $n           = strrpos($item_output, '</li>');
    $item_output = substr($item_output, 0, $n) . '<meta itemprop="position" content="' . $index . '" />' . substr($item_output, $n);

    return $item_output;
}, 10, 5);



/**
 * 显示文章类型、分类方法过滤链接
 *
 * @param string      $taxonomy
 * @param string|null $size
 *
 * @return string
 */
function wprs_the_taxonomy_filter($taxonomy, $post_type=null){
    
    $terms = get_terms([
        'taxonomy'   => 'career_cat',
        'hide_empty' => false,
    ]);
    
    ?>

    <ul class='wprs-tax-filter'>
        
        <?php if($post_type): ?>
            <li class="<?= is_post_type_archive($post_type) ? 'active' : ''; ?>">
                <a href="<?= get_post_type_archive_link($post_type); ?>">All</a>
            </li>
        <?php endif; ?>

        <?php foreach ($terms as $term): ?>
            <li class="<?= get_queried_object_id() === $term->term_id ? 'active' : ''; ?>">
                <a href="<?= get_term_link($term); ?>"><?= $term->name; ?></a>
            </li>
        <?php endforeach; ?>
        
    </ul>

<?php }
