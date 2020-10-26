<?php

if ( ! function_exists('add_action')) {
    return;
}

/**
 * 处理图像裁剪请求
 *
 * @param bool         $downsize 是否压缩图像尺寸，默认为 True
 * @param int          $id       图像附件 ID
 * @param array|string $size     图像尺寸，数组或字符串，默认为 'medium'.
 * @param bool         $crop     是否裁剪照片
 *
 * @return bool|mixed
 *
 * @since 1.0.0
 *
 * @deprecated
 */
add_action('image_downsize', function ($downsize = true, $id, $size, $crop = false)
{
    // 获取附件的元数据
    $meta = wp_get_attachment_metadata($id);

    // 获取所有已创建的图像尺寸 (add_image_size()).
    $sizes = wprs_get_image_sizes();

    // 如果尺寸不是数组，回退到 WordPress 默认功能
    if ( ! is_array($size)) {
        return false;
    }

    // 如果只有宽度，设置高度为宽度
    if (count($size) === 1) {
        $size = [$size[ 0 ], $size[ 0 ]];
    }

    // 如果尺寸数组有 3 个元素
    if (count($size) >= 3) {
        // 覆盖 crop 选项
        if (isset($size[ 2 ])) {
            $crop = $size[ 2 ];

            // 移除 crop 选项
            unset($size[ 2 ]);
        } else {
            $crop = $size[ 'crop' ];
        }
    }

    // 从尺寸数组中取出宽度、高度
    [$width, $height] = $size;

    // 构建宽度 x 高度尺寸名称
    $size = $width . 'x' . $height;

    // 添加 cropped 到尺寸
    if ($crop) {
        $size .= '-cropped';
    }

    // 遍历上面的尺寸数组 (add_image_size()).
    foreach ($sizes as $size_name => $size_atts) {
        // 如果是命名尺寸，使用命名尺寸
        if ($width === $size_atts[ 'width' ] && $height === $size_atts[ 'height' ]) {
            $size = $size_name;
        }
    }

    // 如果元数据包含此尺寸，回退到 WordPress 默认功能
    if (isset($meta[ 'sizes' ]) && array_key_exists($size, $meta[ 'sizes' ])) {
        return false;
    }

    // 如果此尺寸不存在，生成图像
    $intermediate = image_make_intermediate_size(get_attached_file($id), $width, $height, $crop);

    // 如果创建图像失败，回退到 WordPress 默认功能
    if ( ! is_array($intermediate)) {
        return false;
    }

    // 保存返回的尺寸数据到附件元数据中
    $meta[ 'sizes' ][ $size ] = $intermediate;
    wp_update_attachment_metadata($id, $meta);

    // Further constrain the image if 'content_width' is narrower (media.php).
    [$width, $height] = image_constrain_size_for_editor($intermediate[ 'width' ], $intermediate[ 'height' ], $size);

    // 获取原始附件数据
    $file_url  = wp_get_attachment_url($id);
    $file_base = wp_basename($file_url);
    $src       = str_replace($file_base, $intermediate[ 'file' ], $file_url);

    return apply_filters('dynamic_image_resizer_output',
        // Return the expected array - 'true' is to declare this image is modified
        // (http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src).
        [$src, $width, $height, true],
        $downsize, $id, $size, $crop
    );
}, 10, 3);


if ( ! function_exists('wprs_get_image_sizes')) {
    /**
     * 获取当前所有定义的尺寸名称
     *
     * @param string $size 如果提供了此参数，只返回此尺寸
     *
     * @return bool|mixed        Image sizes
     *
     * @since  1.0.0
     *
     */
    function wprs_get_image_sizes($size = '')
    {
        global $_wp_additional_image_sizes;
        $sizes                        = [];
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info.
        foreach ($get_intermediate_image_sizes as $_size) {
            if (in_array($_size, ['thumbnail', 'medium', 'large'])) {
                $sizes[ $_size ][ 'width' ]  = get_option($_size . '_size_w');
                $sizes[ $_size ][ 'height' ] = get_option($_size . '_size_h');
                $sizes[ $_size ][ 'crop' ]   = (bool)get_option($_size . '_crop');
            } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                $sizes[ $_size ] = [
                    'width'  => $_wp_additional_image_sizes[ $_size ][ 'width' ],
                    'height' => $_wp_additional_image_sizes[ $_size ][ 'height' ],
                    'crop'   => $_wp_additional_image_sizes[ $_size ][ 'crop' ],
                ];
            }
        }

        // 如果提供了 $size 参数，只返回此尺寸
        if ($size) {
            if (isset($sizes[ $size ])) {
                return $sizes[ $size ];
            } else {
                return false;
            }
        }

        return $sizes;
    }
}


if ( ! function_exists('wprs_render_qrcode')) {
    /**
     * 生成二维码
     *
     * @param $string
     *
     * @return string
     */
    function wprs_render_qrcode($string)
    {
        if (class_exists('\BaconQrCode\Renderer\Image\Png')) {
            $renderer = new \BaconQrCode\Renderer\Image\Png();
            $renderer->setHeight(256);
            $renderer->setWidth(256);
            $renderer->setMargin(0);

            $qrCode = new \BaconQrCode\Writer($renderer);

            return 'data:image/png;base64, ' . base64_encode($qrCode->writeString($string));
        } else {
            wp_die('please install bacon/bacon-qr-code library');

        }
    }
}