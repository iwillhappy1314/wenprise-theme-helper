<?php

if(!function_exists('add_action')){
    return;
}

/**
 * 加载多语言文件
 */
$locale = apply_filters( 'theme_locale', is_admin() ? get_user_locale() : get_locale(), 'wprs' );
load_textdomain( 'wprs', __DIR__ . '/languages/wprs-' . $locale . '.mo' );