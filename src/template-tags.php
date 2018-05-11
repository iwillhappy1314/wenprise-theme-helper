<?php
/**
 * 模版标签
 */

/**
 * Bulma CSS 数字分页
 *
 * @param string $query
 * @param string $pages
 * @param int    $range
 */
if ( ! function_exists( 'wprs_bulma_pagination' ) ) {
	function wprs_bulma_pagination( $query = '', $pages = '', $range = 5 ) {
		$show_items = ( $range * 2 ) + 1;

		global $paged;
		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( ! $query ) {
			global $wp_query;
			$wprs_query = $wp_query;
		} else {
			$wprs_query = $query;
		}

		if ( $pages == '' ) {
			$pages = $wprs_query->max_num_pages;
			if ( ! $pages ) {
				$pages = 1;
			}
		}

		if ( 1 != $pages ) {
			echo '<nav class="pagination" role="navigation" aria-label="pagination">';
			if ( $paged > 2 && $paged > $range + 1 && $show_items < $pages ) {
				echo '<a class="pagination-previous" aria-label="Previous" href="' . get_pagenum_link( 1 ) . '"><span aria-hidden="true">«</span></a>';
			}
			if ( $paged > 1 && $show_items < $pages ) {
				echo '<a class="pagination-previous" aria-label="Previous" href="' . get_pagenum_link( $paged - 1 ) . '"><span aria-hidden="true"><</span></a>';
			}

			echo '<ul class="pagination-list">';

			for ( $i = 1; $i <= $pages; $i ++ ) {
				if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $show_items ) ) {
					if ( $paged == $i ) {
						echo '<li><a class="pagination-link is-current" href="#">' . $i . '</a></li>';
					} else {
						echo '<li><a class="pagination-link" href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
					}
				}
			}

			echo '</ul>';

			if ( $paged < $pages ) {
				echo '<a class="pagination-next" aria-label="Next" href="' . get_pagenum_link( $paged + 1 ) . '"><span aria-hidden="true">></span></a>';
			}
			if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $show_items < $pages ) {
				echo '<a class="pagination-next" aria-label="Next" href="' . get_pagenum_link( $pages ) . '"><span aria-hidden="true">»</span></a>';
			}
			echo '</nav>';
		}
	}
}

/**
 * 数字分页
 *
 * @param string $query
 * @param string $pages
 * @param int    $range
 */
if ( ! function_exists( 'wprs_pagination' ) ) {
	function wprs_pagination( $query = '', $pages = '', $range = 5 ) {
		$show_items = ( $range * 2 ) + 1;

		global $paged;
		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( ! $query ) {
			global $wp_query;
			$wprs_query = $wp_query;
		} else {
			$wprs_query = $query;
		}

		if ( $pages == '' ) {
			$pages = $wprs_query->max_num_pages;
			if ( ! $pages ) {
				$pages = 1;
			}
		}

		if ( 1 != $pages ) {
			echo '<ul class="pagination">';
			if ( $paged > 2 && $paged > $range + 1 && $show_items < $pages ) {
				echo '<li><a aria-label="Previous" href="' . get_pagenum_link( 1 ) . '"><span aria-hidden="true">«</span></a></li>';
			}
			if ( $paged > 1 && $show_items < $pages ) {
				echo '<li><a aria-label="Previous" href="' . get_pagenum_link( $paged - 1 ) . '"><span aria-hidden="true"><</span></a></li>';
			}

			for ( $i = 1; $i <= $pages; $i ++ ) {
				if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $show_items ) ) {
					if ( $paged == $i ) {
						echo '<li class="active"><a href="#">' . $i . '</a></li>';
					} else {
						echo '<li><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
					}
				}
			}

			if ( $paged < $pages ) {
				echo '<li><a class="nextpostslink" aria-label="Next" href="' . get_pagenum_link( $paged + 1 ) . '"><span aria-hidden="true">></span></a></li>';
			}
			if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $show_items < $pages ) {
				echo '<li><a class="lastpostslink" aria-label="Next" href="' . get_pagenum_link( $pages ) . '"><span aria-hidden="true">»</span></a></li>';
			}
			echo '</ul>';
		}
	}
}


if ( ! function_exists( 'wprs_get_archive_title' ) ) {
	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	function wprs_get_archive_title() {
		return wprs_get_page_title();
	}
}

if ( ! function_exists( 'wprs_get_archive_description' ) ) {
	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	function wprs_get_archive_description() {
		return wprs_get_page_description();
	}
}


/**
 *  获取存档或文章标题作为页面标题使用
 */
if ( ! function_exists( 'wprs_get_page_title' ) ) {
	/**
	 * 获取存档或文章标题作为页面标题使用
	 *
	 * @return mixed
	 */
	function wprs_get_page_title() {
		if ( is_category() || is_tag() ) {

			$title = carbon_get_term_meta( get_queried_object_id(), 'title' );

			if ( ! $title ) {
				$title = sprintf( __( '%s' ), single_cat_title( '', false ) );
			}

		} elseif ( is_author() ) {

			$title = sprintf( __( '%s' ), '<span class="vcard">' . get_the_author() . '</span>' );

		} elseif ( is_year() ) {

			$title = sprintf( __( '%s' ), get_the_date( _x( 'Y', 'wprs', 'yearly archives date format' ) ) );

		} elseif ( is_month() ) {

			$title = sprintf( __( '%s' ), get_the_date( _x( 'F Y', 'wprs', 'monthly archives date format' ) ) );

		} elseif ( is_day() ) {

			$title = sprintf( __( '%s' ), get_the_date( _x( 'F j, Y', 'wprs', 'daily archives date format' ) ) );

		} elseif ( is_tax( 'post_format' ) ) {

			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'wprs', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'wprs', 'post format archive title' );
			}
		} elseif ( is_post_type_archive() ) {

			$title = sprintf( __( '%s' ), post_type_archive_title( '', false ) );

			$post_type    = get_queried_object()->name;
			$custom_title = wprs_get_archive_option( $post_type, 'title' );

			if ( ! empty( $custom_title ) ) {
				$title = $custom_title;
			}

		} elseif ( is_page() || is_single() || is_singular() ) {

			$title = sprintf( __( '%s' ), get_the_title() );

		} elseif ( is_tax() ) {

			$title = carbon_get_term_meta( get_queried_object_id(), 'title' );

			if ( ! $title ) {
				$title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
			}

		} else {

			$title = __( 'Archives', 'wprs' );

		}

		/**
		 * Filter the archive title.
		 *
		 * @since 4.1.0
		 *
		 * @param string $title Archive title to be displayed.
		 */
		return apply_filters( 'wprs_get_page_title', $title );
	}
}


if ( ! function_exists( 'wprs_get_page_description' ) ) {
	/**
	 * 获取存档描述
	 *
	 * @return mixed|string
	 */
	function wprs_get_page_description() {

		$description = '';

		if ( is_post_type_archive() ) {
			$description = wprs_get_archive_option( get_queried_object()->name, 'description' );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$description = get_the_archive_description();
		} else {
			get_the_excerpt();
		}

		return $description;
	}
}


/**
 * 获取图像尺寸属性
 *
 * @param string $size
 *
 * @return array
 */
function wprs_image_size_attr( $size = 'is-400by300' ) {
	if ( is_array( $size ) ) {
		$size_array = $size;
		$size_class = ( $size[ 2 ] == 1 ) ? 'is-square ' : 'is-' . str_replace( '/', 'by', wprs_float2rat( $size[ 0 ] / $size[ 1 ] ) );
		unset( $size_array[ 2 ] );
	} else {
		$size_array = explode( 'by', str_replace( 'is-', '', $size ) );
		$size_class = wprs_ratio_simplify( $size_array[ 0 ], $size_array[ 1 ] );
	}

	// 原始图像尺寸，方便查找图像尺寸定义
	$size_class_base = 'is-' . join( 'by', $size_array );

	$size_array[] = 1;

	$attr = [
		'size_class_base' => $size_class_base,
		'size_class'      => $size_class,
		'size_array'      => $size_array,
	];

	return $attr;
}


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
if ( ! function_exists( 'wprs_post_thumbnail' ) ) {
	function wprs_post_thumbnail( $post, $size = 'is-400by300', $icon = false, $attr = [] ) {
		$html      = '';
		$img_class = [ 'class' => 'img-responsive' ];
		$attr      = wp_parse_args( $attr, $img_class );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		$image_attr = wprs_image_size_attr( $size );

		$thumb_id = get_post_thumbnail_id( $post );

		if ( get_post_gallery( $post ) ) {

			$gallery = get_post_gallery( get_the_ID(), false );

			$html .= '<figure class="f-popup f-view f-overlay">';
			$html .= '<div class="f-slider" data-slick={"slidesToShow": 4, "slidesToScroll": 4}>';

			if ( has_post_thumbnail() ) {
				$html .= '<div class="image ' . $image_attr[ 'size_class_base' ] . ' ' . $image_attr[ 'size_class' ] . '">';
				$html .= wp_get_attachment_image( $thumb_id, $image_attr[ 'size_array' ], $icon, $attr );
				$html .= '</div>';
			}

			foreach ( explode( ',', $gallery[ 'ids' ] ) as $image ) {
				$html .= '<div class="image ' . $image_attr[ 'size_class_base' ] . ' ' . $image_attr[ 'size_class' ] . '">';
				$html .= wp_get_attachment_image( $image, $image_attr[ 'size_array' ], $icon, $attr );
				$html .= '</div>';
			}

			$html .= '</div>';

			$html .= wprs_thumbnail_mask( $post );

			$html .= '</figure>';

		} elseif ( has_post_thumbnail( $post ) ) {

			$html .= '<figure class="f-popup f-view f-overlay">';
			$html .= '<div class="image ' . $image_attr[ 'size_class_base' ] . ' ' . $image_attr[ 'size_class' ] . '">';
			$html .= wp_get_attachment_image( $thumb_id, $image_attr[ 'size_array' ], $icon, $attr );
			$html .= '</div>';
			$html .= wprs_thumbnail_mask( $post );
			$html .= '</figure>';

		} else {

			$html .= '';

		}

		return $html;
	}
}


/**
 * 获取附件图像
 *
 * @param        $id
 * @param string $size
 * @param bool   $icon
 * @param array  $attr
 *
 * @return string
 */
function wprs_thumbnail_image( $id, $size = 'is-400by300', $icon = false, $attr = [] ) {
	// 如果是数组，转换为 is-1by2的形式，否则，求简化分数
	$image_attr = wprs_image_size_attr( $size );

	$html = '<figure class="image ' . $image_attr[ 'size_class_base' ] . ' ' . $image_attr[ 'size_class' ] . '">';
	$html .= wp_get_attachment_image( $id, $image_attr[ 'size_array' ], $icon, $attr );
	$html .= '</figure>';

	return $html;
}


/**
 * 显示缩略图遮罩
 *
 * @param $post
 *
 * @return string
 */
if ( ! function_exists( 'wprs_thumbnail_mask' ) ) {
	function wprs_thumbnail_mask( $post ) {

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		$attachment_id = get_post_thumbnail_id();
		$thumb_src     = wp_get_attachment_image_src( $attachment_id, true );

		$is_show_image_popup = wprs_get_page_settings( 'show_popup', 'show' );

		$html = '<div class="is-overlay u-mask flex-center">';

		if ( $is_show_image_popup === 'show' ) {
			if ( get_post_gallery( $post ) ) {
				$html .= '<a class="f-gallery" href="#"><i class="iconfont icon-arrowexpand"></i></a>';
			} else {
				$html .= '<a class="f-gallery" href="' . esc_url( $thumb_src[ 0 ] ) . '"><i class="iconfont icon-arrowexpand"></i></a>';
			}
		}

		if ( ! is_singular( $post ) ) {
			$html .= '<a class="f-post" href="' . get_the_permalink( $post ) . '"><i class="iconfont icon-link"></i></a>';
		}

		$html .= '</div>';

		return $html;
	}
}


if ( ! function_exists( 'wprs_bulma_menu' ) ) {
	/**
	 * 显示 Bulma 菜单
	 *
	 * @param $theme_location string 菜单位置
	 */
	function wprs_bulma_menu( $theme_location ) {
		if ( ( $theme_location ) && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $theme_location ] ) ) {
			$menu       = get_term( $locations[ $theme_location ], 'nav_menu' );
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			$menu_list = '<div id="main-nav" class="navbar-menu">' . "\n";
			$menu_list .= '<div class="navbar-end">';

			/**
			 * 所有菜单
			 */
			foreach ( $menu_items as $menu_item ) {
				if ( $menu_item->menu_item_parent == 0 ) {

					$menu_children_array = [];

					$menu_item_url = $menu_item->url;

					// 处理绝对路径为首页的情况
					if ( $menu_item->url == '/' ) {
						$menu_item_url = home_url( '/' );
					}

					$is_current = ( wprs_get_current_url() == $menu_item_url ) ? 'is-active is-1' : '';

					/**
					 * 二级菜单数组
					 */
					$is_child_current = false;
					foreach ( $menu_items as $submenu ) {
						if ( $submenu->menu_item_parent == $menu_item->ID ) {
							$is_current = '';
							if ( wprs_get_current_url() == $submenu->url ) {
								$is_child_current = true;
								$is_current       = 'is-active is-2';
							}

							$menu_children_array[] = '<a class="navbar-item ' . $is_current . '" href="' . $submenu->url . '">' . $submenu->title . '</a>' . "\n";
						}
					}

					if ( count( $menu_children_array ) > 0 ) {

						/**
						 * 显示二级菜单
						 */
						$is_current_parent = ( $is_child_current == '' ) ? '' : 'is-active-parent';
						$is_current        = ( wprs_get_current_url() == $menu_item_url ) ? 'is-active is-0' : '';

						$menu_list .= '<div class="navbar-item has-dropdown is-hoverable ' . $is_current_parent . '">' . "\n";
						$menu_list .= '<a href="' . $menu_item->url . '" class="navbar-link ' . $is_current . '">' . $menu_item->title . ' </a>' . "\n";

						$menu_list .= '<div class="navbar-dropdown is-boxed">' . "\n";
						$menu_list .= implode( "\n", $menu_children_array );
						$menu_list .= '</div>' . "\n";
						$menu_list .= '</div>' . "\n";

					} else {

						/**
						 * 顶级菜单
						 */
						$menu_list .= '<a class="navbar-item ' . $is_current . '" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' . "\n";

					}

				}

			}

			$menu_list .= '</div>' . "\n";
			$menu_list .= '</div>' . "\n";

		} else {
			$menu_list = '<!-- no menu defined in location "' . $theme_location . '" -->';
		}
		echo $menu_list;
	}
}



/**
 * 隐藏字符串中的部分字符
 *
 * @param     $str
 * @param int $start
 * @param int $length
 *
 * @return mixed
 */
function wprs_string_mask( $str, $start = 0, $length = 4 ) {
	$mask = preg_replace( "/\S/", "*", $str );
	if ( is_null( $length ) ) {
		$mask = substr( $mask, $start );
		$str  = substr_replace( $str, $mask, $start );
	} else {
		$mask = substr( $mask, $start, $length );
		$str  = substr_replace( $str, $mask, $start, $length );
	}

	return $str;
}
