<?php

use Nette\Utils\Arrays;
use Nette\Utils\Finder;


/**
 * @param string $dir
 * @param string $default_path
 *
 * @return array
 *
 * @deprecated
 */
function wprs_get_template_option( $dir = "wizhi", $default_path = '' )
{
	return wprs_data_templates( $dir, $default_path );
}

/**
 * 获取存档页面模板
 *
 * @param string $dir          模板文件所在的目录名称
 * @param string $default_path 模板文件所在默认目录名称，可为空
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_templates' ) ) {
	function wprs_data_templates( $dir = "wizhi", $default_path = '' )
	{

		$template_in_plugin = $default_path . $dir;
		$template_in_theme  = get_theme_file_path( $dir );

		$templates_in_plugin = [];
		$templates_in_theme  = [];


		// 插件中的模板
		if ( is_dir( $template_in_plugin ) ) {

			// 查找目录中的文件
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

		ksort($templates);

		return $templates;
	}
}


/**
 * 获取文章类型数组
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_post_types' ) ) {
	function wprs_data_post_types()
	{

		$args_type = [
			'public'   => true,
			'_builtin' => false,
		];

		$post_types = get_post_types( $args_type, 'objects' );

		$output = [
			0      => sprintf( '— %s —', __( 'Select Content Type', 'wprs' ) ),
			'post' => __( 'Post', 'wprs' ),
			'page' => __( 'Page', 'wprs' ),
		];

		foreach ( $post_types as $post_type ) {
			$output[ $post_type->name ] = $post_type->label;
		}

		return $output;
	}
}


/**
 * 获取分类方法数组
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_taxonomies' ) ) {
	function wprs_data_taxonomies()
	{

		$output = [
			0          => sprintf( '— %s —', __( 'Select Taxonomy', 'wprs' ) ),
			'category' => __( 'Category', 'wprs' ),
			'post_tag' => __( 'Tags', 'wprs' ),
		];

		$args = [
			'public'   => true,
			'_builtin' => false,
		];

		$taxonomies = get_taxonomies( $args );

		foreach ( $taxonomies as $taxonomy ) {
			$tax = get_taxonomy( $taxonomy );
			if ( ( ! $tax->show_tagcloud || empty( $tax->labels->name ) ) ) {
				continue;
			}
			$output[ esc_attr( $taxonomy ) ] = esc_attr( $tax->labels->name );
		}

		return $output;
	}
}


/**
 * 获取分类法项目数组
 *
 * @param string $taxonomy
 * @param int    $parent
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_terms' ) ) {
	function wprs_data_terms( $taxonomy = 'post_tag', $parent = 0 )
	{
		$terms = get_terms( $taxonomy, [
			'parent'     => $parent,
			'hide_empty' => false,
		] );

		$output = [
			0 => sprintf( '— %s —', __( 'Select Category', 'wprs' ) ),
		];

		if ( is_wp_error( $terms ) ) {
			return $output;
		}

		foreach ( $terms as $term ) {

			$output[ $term->term_id ] = $term->name;
			$term_children            = get_term_children( $term->term_id, $taxonomy );

			if ( is_wp_error( $term_children ) ) {
				continue;
			}

			foreach ( $term_children as $term_child_id ) {

				$term_child = get_term_by( 'id', $term_child_id, $taxonomy );

				if ( is_wp_error( $term_child ) ) {
					continue;
				}

				$output[ $term_child->term_id ] = $term_child->name;
			}

		}

		return $output;
	}
}


/**
 * 获取文章数组
 *
 * @param string $type
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_posts' ) ) {
	function wprs_data_posts( $type = "post" )
	{
		$args = [
			'post_type'      => $type,
			'posts_per_page' => '-1',
		];
		$loop = new \WP_Query( $args );

		$output = [
			0 => sprintf( '— %s —', __( 'Select Content', 'wprs' ) ),
		];

		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				$output[ get_the_ID() ] = get_the_title();
			endwhile;
		}

		wp_reset_postdata();

		return $output;
	}
}


/**
 * 获取图片尺寸数组
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_image_sizes' ) ) {
	function wprs_data_image_sizes()
	{
		$image_sizes_orig   = get_intermediate_image_sizes();
		$image_sizes_orig[] = 'full';
		$image_sizes        = [];

		foreach ( $image_sizes_orig as $size ) {
			$image_sizes[ $size ] = $size;
		}

		return $image_sizes;
	}
}


/**
 * 获取主题
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_themes' ) ) {
	function wprs_data_themes()
	{
		$themes = wp_get_themes();

		$options = [
			0 => 'Responsive',
		];

		foreach ( $themes as $theme ) {
			$options[ $theme->template ] = $theme->Name;
		}

		return $options;
	}
}


/**
 * 获取颜色选项
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_colors' ) ) {
	function wprs_data_colors()
	{
		$output              = [];
		$output[]            = __( 'Default', 'wprs' );
		$output[ 'success' ] = __( 'Success（Green）', 'wprs' );
		$output[ 'info' ]    = __( 'Info（Blue）', 'wprs' );
		$output[ 'warning' ] = __( 'Warning（Orange）', 'wprs' );
		$output[ 'danger' ]  = __( 'Danger（Red）', 'wprs' );

		return $output;
	}
}


/**
 * 获取尺寸选项
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_sizes' ) ) {
	function wprs_data_sizes()
	{
		$sizes = [
			'0'    => __( 'Zero', 'wprs' ),
			'auto' => __( 'Auto', 'wprs' ),
			'xxs'  => __( 'xxSmall', 'wprs' ),
			'xs'   => __( 'xSmall', 'wprs' ),
			'sm'   => __( 'Small', 'wprs' ),
			'md'   => __( 'Medium', 'wprs' ),
			'lg'   => __( 'Large', 'wprs' ),
			'xl'   => __( 'xLarge', 'wprs' ),
		];

		return $sizes;
	}
}


/**
 * 根据角色获取用户选项数组
 *
 * @param $role
 *
 * @return array
 */
if ( ! function_exists( 'wprs_data_user' ) ) {
	function wprs_data_user( $role )
	{
		$users = get_users( [
			'role' => $role,
		] );

		$user_data = [];
		foreach ( $users as $user ) {
			$user_data[ $user->ID ] = $user->display_name;
		}

		return $user_data;
	}
}