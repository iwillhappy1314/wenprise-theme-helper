<?php

if ( ! function_exists('add_action')) {
    return;
}

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
