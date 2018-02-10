<?php

/**
 * 基于 Latte 的字符串模版
 *
 * @param $template string 模版字符串
 * @param $params   array 模版数据
 * @param $string   boolean 是否渲染为字符串而不是直接输出
 *
 * @deprecated
 */
if ( ! function_exists( 'wprs_render' ) ) {
	function wprs_render( $template, $params, $string = false ) {
		$latte = new Latte\Engine;
		$latte->setTempDirectory( WP_CONTENT_DIR . '/cache/' );

		$latte->setLoader( new Latte\Loaders\StringLoader( [
			'template' => $template,
		] ) );

		if ( $string ) {
			return $latte->renderToString( 'template', $params );
		} else {
			$latte->render( 'template', $params );
		}

		return true;
	}
}


/**
 * 判断是否在微信中打开
 */
if ( ! function_exists( 'wprs_is_wechat' ) ) {
	function wprs_is_wechat() {
		if ( ! empty( $_SERVER[ 'HTTP_USER_AGENT' ] ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'MicroMessenger' ) !== false ) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'wprs_is_subpage' ) ) {
	/**
	 * 判断当前页面是否为子页面
	 *
	 * @param array $parent
	 *
	 * @return bool
	 */
	function wprs_is_subpage( array $parent ) {
		global $post;

		$parentPage = get_post( $post->post_parent );

		if ( is_page() && $post->post_parent && $parentPage->post_name === $parent[ 0 ] ) {
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
if ( ! function_exists( 'wprs_get_post_meta' ) ) {
	function wprs_get_post_meta( $post_id, $key = '', $single = false, $default = false ) {

		$meta = get_post_meta( $post_id, $key, $single );

		if ( ! $meta && $default ) {
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
if ( ! function_exists( 'wprs_get_user_meta' ) ) {
	function wprs_get_user_meta( $user_id, $key = '', $single = false, $default = false ) {

		$meta = get_user_meta( $user_id, $key, $single );

		if ( ! $meta && $default ) {
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
if ( ! function_exists( 'wprs_is_ajax' ) ) {
	function wprs_is_ajax() {
		if ( ! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) {
			return true;
		}

		return false;
	}
}

/**
 * 判断当前语言是否为英文
 *
 * @return bool
 */
if ( ! function_exists( 'wprs_is_en' ) ) {
	function wprs_is_en() {

		$lang = get_bloginfo( 'language' );

		if ( $lang == 'en-US' ) {
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
if ( ! function_exists( 'wprs_get_ip' ) ) {
	function wprs_get_ip() {
		$client  = @$_SERVER[ 'HTTP_CLIENT_IP' ];
		$forward = @$_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		$remote  = $_SERVER[ 'REMOTE_ADDR' ];

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}
}


if ( ! function_exists( "order_no" ) ) {
	/**
	 * 生成订单号
	 *
	 * @package   helper
	 *
	 * @return string 订单号字符串
	 */
	function order_no() {
		return date( 'Ymd' ) . str_pad( mt_rand( 1, 99999 ), 5, '0', STR_PAD_LEFT );
	}
}


/**
 * 获取前端资源路径
 */
if ( ! class_exists( 'WprsJsonManifest' ) ) {
	class WprsJsonManifest {
		private $manifest;

		public function __construct( $manifest_path ) {

			if ( file_exists( $manifest_path ) ) {
				$this->manifest = json_decode( file_get_contents( $manifest_path ), true );
			} else {
				$this->manifest = [];
			}

		}

		public function get() {
			return $this->manifest;
		}

		/**
		 * @param string $key
		 * @param null   $default
		 *
		 * @return array|mixed|null
		 */
		public function getPath( $key = '', $default = null ) {
			$collection = $this->manifest;

			if ( is_null( $key ) ) {
				return $collection;
			}

			if ( isset( $collection[ $key ] ) ) {
				return $collection[ $key ];
			}

			foreach ( explode( '.', $key ) as $segment ) {
				if ( ! isset( $collection[ $segment ] ) ) {
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
if ( ! function_exists( 'wprs_assets' ) ) {
	function wprs_asset( $filename ) {
		$dist_path = get_theme_file_uri( '/front/dist/' );
		$directory = dirname( $filename ) . '/';
		$file      = basename( $filename );
		static $manifest;

		if ( empty( $manifest ) ) {
			$manifest_path = get_theme_file_path( '/front/dist/' . 'assets.json' );
			$manifest      = new WprsJsonManifest( $manifest_path );
		}

		if ( array_key_exists( $file, $manifest->get() ) ) {
			return $dist_path . $directory . $manifest->get()[ $file ];
		} else {
			return $dist_path . $directory . $file;
		}
	}
}


if ( ! function_exists( 'wprs_get_archive_option' ) ) {
	/**
	 * 获取分类法存档设置
	 *
	 * @param        $type    string 分类法名称
	 * @param        $name    string 设置名称
	 * @param string $default 默认值
	 *
	 * @return mixed|string
	 */
	function wprs_get_archive_option( $type, $name, $default = '' ) {
		$value = carbon_get_theme_option( $type . '_' . $name );

		if ( ! $value ) {
			$value = $default;
		}

		return $value;
	}
}


if ( ! function_exists( 'wprs_get_taxonomy_type' ) ) {
	/**
	 * 获取个分类法关联的第一个文章类型，在分类法存档页使用
	 */
	function wprs_get_taxonomy_type() {
		$taxonomy   = get_query_var( 'taxonomy' );
		$tax_object = get_taxonomy( $taxonomy );
		$type       = $tax_object->object_type[ 0 ];

		return $type;
	}
}


if ( ! function_exists( 'wprs_get_term_post_type' ) ) {
	/**
	 * 获取当前分类法项目所属的文章类型，在分类法项目存档页中使用
	 *
	 * @param bool $term
	 *
	 * @return mixed
	 */
	function wprs_get_term_post_type( $term = false ) {

		if ( ! $term ) {
			$term = get_queried_object();
		}

		$term = get_term( $term );

		$taxonomy = get_taxonomy( $term->taxonomy );

		return $taxonomy->object_type[ 0 ];
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
if ( ! function_exists( 'wprs_get_page_settings' ) ) {
	function wprs_get_page_settings( $name, $default = '' ) {

		$global_settings = carbon_get_theme_option( $name );

		if ( is_page() || is_single() || is_singular() ) {

			$post     = get_queried_object();
			$settings = carbon_get_post_meta( $post->ID, $name );

			if ( ! $settings && $post->post_parent ) {
				$settings = carbon_get_post_meta( $post->post_parent, $name );
			}

		} elseif ( is_category() || is_tag() || is_tax() ) {

			$settings = carbon_get_term_meta( get_queried_object_id(), $name );

			if ( ! $settings ) {
				$settings = wprs_get_archive_option( wprs_get_term_post_type(), $name );
			}

		} elseif ( is_post_type_archive() ) {

			$post_type = get_queried_object()->name;
			$settings  = wprs_get_archive_option( $post_type, $name );

		} else {

			$settings = $global_settings;

		}

		if ( '' === $settings ) {
			$settings = $global_settings;
		}

		if ( '' === $settings ) {
			$settings = $default;
		}

		return $settings;
	}
}


if ( ! function_exists( 'wprs_get_current_url' ) ) {
	/**
	 * 获取当前 URL
	 *
	 * @return bool|string
	 */
	function wprs_get_current_url() {
		$url = false;

		if ( isset( $_SERVER[ 'SERVER_ADDR' ] ) ) {
			$is_https   = isset( $_SERVER[ 'HTTPS' ] ) && 'on' == $_SERVER[ 'HTTPS' ];
			$protocol   = 'http' . ( $is_https ? 's' : '' );
			$host       = isset( $_SERVER[ 'HTTP_HOST' ] )
				? $_SERVER[ 'HTTP_HOST' ]
				: $_SERVER[ 'SERVER_ADDR' ];
			$port       = $_SERVER[ 'SERVER_PORT' ];
			$path_query = $_SERVER[ 'REQUEST_URI' ];

			$url = sprintf( '%s://%s%s%s',
				$protocol,
				$host,
				$is_https
					? ( 443 != $port ? ':' . $port : '' )
					: ( 80 != $port ? ':' . $port : '' ),
				$path_query
			);
		}

		return $url;
	}
}


if ( ! function_exists( 'wprs_get_domain' ) ) {
	/**
	 * 获取网址的域名
	 *
	 * @param $url
	 *
	 * @return bool|mixed|string
	 */
	function wprs_get_domain( $url ) {
		$host = @parse_url( $url, PHP_URL_HOST );
		// If the URL can't be parsed, use the original URL
		// Change to "return false" if you don't want that
		if ( ! $host ) {
			$host = $url;
		}
		// The "www." prefix isn't really needed if you're just using
		// this to display the domain to the user
		if ( substr( $host, 0, 4 ) == "www." ) {
			$host = substr( $host, 4 );
		}
		// You might also want to limit the length if screen space is limited
		if ( strlen( $host ) > 50 ) {
			$host = substr( $host, 0, 47 ) . '...';
		}

		return $host;
	}
}


if ( ! function_exists( 'wprs_get_social_icon' ) ) {
	/**
	 * 获取社交图标名称，可以自动添加 font-awesome 图标
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	function wprs_get_social_icon( $url ) {

		$domain = wprs_get_domain( $url );
		$icon   = explode( '.', $domain )[ 0 ];

		return $icon;

	}
}
