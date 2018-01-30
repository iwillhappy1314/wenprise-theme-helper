<?php

use Nette\Utils\Arrays;
use Nette\Utils\Finder;

/**
 * 获取存档页面模板
 *
 * @param string $dir          模板文件所在的目录名称
 * @param string $default_path 模板文件所在默认目录名称，可为空
 *
 * @return array
 */
function wprs_get_template_option( $dir = "wizhi", $default_path = '' ) {

	$template_in_plugin = $default_path . $dir;
	$template_in_theme  = get_theme_file_path( $dir );

	$templates_in_plugin = [];
	$templates_in_theme  = [];


	// 插件中的模板
	if ( is_dir( $template_in_plugin ) ) {
		$finder = Finder::findFiles( '*.php' )
		                ->in( $template_in_plugin );

		foreach ( $finder as $key => $file ) {

			$filename        = $file->getFilename();
			$file_name_array = explode( '-', $filename );
			$name            = Arrays::get( $file_name_array, 1, '' );

			$headers = [
				'Name' => __( 'Loop Template Name', 'wprs' ),
			];

			$file_info = get_file_data( $key, $headers );

			// 获取模板名称
			if ( $file_info[ 'Name' ] ) {
				$option_name = $file_info[ 'Name' ];
			} else {
				$option_name = ucfirst( $name );
			}

			$templates_in_theme[ explode( '.', $name )[ 0 ] ] = $option_name;

		}
	}


	// 主题中的模板
	if ( is_dir( $template_in_theme ) ) {

		$finder = Finder::findFiles( '*.php' )
		                ->in( $template_in_theme );

		foreach ( $finder as $key => $file ) {

			$filename        = $file->getFilename();
			$file_name_array = explode( '-', $filename );
			$name            = Arrays::get( $file_name_array, 1, '' );

			$headers = [
				'Name' => __( 'Loop Template Name', 'wprs' ),
			];

			$file_info = get_file_data( $key, $headers );

			// 获取模板名称
			if ( $file_info[ 'Name' ] ) {
				$option_name = $file_info[ 'Name' ];
			} else {
				$option_name = ucfirst( $name );
			}

			$templates_in_theme[ explode( '.', $name )[ 0 ] ] = $option_name;

		}
	}


	// 合并插件和主题中的模板，优先使用主题中模板
	$templates = wp_parse_args( $templates_in_theme, $templates_in_plugin );

	return $templates;
}