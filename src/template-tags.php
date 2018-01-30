<?php

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
			$wizhi_query = $wp_query;
		} else {
			$wizhi_query = $query;
		}

		if ( $pages == '' ) {
			$pages = $wizhi_query->max_num_pages;
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


/**
 *  获取存档或文章标题作为页面标题使用
 */
if ( ! function_exists( 'wprs_get_archive_title' ) ) {
	/**
	 * 获取存档或文章标题作为页面标题使用
	 *
	 * @return mixed
	 */
	function wprs_get_archive_title() {
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

			if ( ! empty( $title ) ) {
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
		return apply_filters( 'wprs_get_archive_title', $title );
	}
}


if ( ! function_exists( 'wprs_get_archive_description' ) ) {
	/**
	 * 获取存档描述
	 *
	 * @return mixed|string
	 */
	function wprs_get_archive_description() {

		$description = '';

		if ( is_post_type_archive() ) {
			$description = wprs_get_archive_option( get_queried_object()->name, 'description' );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$description = get_the_archive_description();
		}

		return $description;
	}
}
