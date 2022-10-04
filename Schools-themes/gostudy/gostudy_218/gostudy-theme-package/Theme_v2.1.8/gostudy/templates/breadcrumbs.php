<?php
defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Breadcrumbs')) {
    /**
     * Breadcrumbs area
     *
     *
     * @category Class
     * @package gostudy\templates
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class Gostudy_Breadcrumbs
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private $text_home;
        private $text_category;
        private $text_search;
        private $text_tag;
        private $text_author;
        private $text_404;
        private $text_page;
        private $text_cpage;

        private function text_options()
        {
            $this->text_home     = esc_html__('Home', 'gostudy'); // text for the `Home` link
            $this->text_category = esc_html__('Archive by Category "%s"', 'gostudy'); // text for a category page
            $this->text_search   = esc_html__('Search Results for "%s"', 'gostudy'); // text for a search results page
            $this->text_tag      = esc_html__('Posts Tagged "%s"', 'gostudy'); // text for a tag page
            $this->text_author   = esc_html__('Articles Posted by %s', 'gostudy'); // text for an author page
            $this->text_404      = esc_html__('Error 404', 'gostudy'); // text for the 404 page
            $this->text_page     = esc_html__('Page %s', 'gostudy'); // text 'Page N'
            $this->text_cpage    = esc_html__('Comment Page %s', 'gostudy'); // text `Comment Page N`
        }

        public function __construct ()
        {
            $this->text_options();
            $out = '';

            $wrap_before    = '<div class="breadcrumbs">'; // the opening wrapper tag
            $wrap_after     = '</div><!-- .breadcrumbs -->'; // the closing wrapper tag
            $sep_before     = '<span class="divider">'; // tag before separator
            $sep            = ''; // separator between crumbs
            $sep_after      = '</span>'; // tag after separator
            $show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
            $show_on_home   = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
            $show_current   = 1; // 1 - show current page title, 0 - don't show
            $before         = '<span class="current">'; // tag before the current crumb
            $after          = '</span>'; // tag after the current crumb

            global $post;
            $home_url       = esc_url(home_url('/'));
            $link_before    = '';
            $link_after     = '';
            $link_attr      = '';
            $link_in_before = '';
            $link_in_after  = '';
            $link           = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;
            $frontpage_id   = get_option('page_on_front');
            $parent_id      = ($post) ? $post->post_parent : '';
            $sep            = ' ' . $sep_before . $sep . $sep_after . ' ';
            $home_link      = $link_before . '<a href="' . $home_url . '"' . $link_attr . ' class="home">' . $link_in_before . $this->text_home . $link_in_after . '</a>' . $link_after;
            if ( is_home() || is_front_page() ) {
                if ($show_on_home) $out .= $wrap_before . $home_link . $wrap_after;
            } else {
                $out .= $wrap_before;
                if ( $show_home_link && !(class_exists( 'WooCommerce' ) && is_product())) $out .= $home_link;
	            if( class_exists( 'WooCommerce' ) && ( is_product() || is_product_category() || is_product_tag() || is_cart() || is_checkout())){
		            $args = array();
		            $args['delimiter'] = $sep;
		            if ($show_home_link && !is_product()) $out .= $sep;
		            if (($shop_id = wc_get_page_id( 'shop' )) > 0){
			            $out .= '<a href="' . get_permalink( $shop_id ) .'">' . get_the_title( $shop_id ) .'</a>' . $sep;
		            }
		            $out .= gostudy_woocommerce_breadcrumb($args);
	            } elseif ( class_exists( 'WooCommerce' ) && is_woocommerce() && !is_product() && ($shop_id = wc_get_page_id( 'shop' )) > 0 ) {
		            if ($show_home_link) $out .= $sep;
		            if (!is_shop()) $out .= '<a href="' . get_permalink( $shop_id ) .'">' . get_the_title( $shop_id ) .'</a>' . $sep;
		            $out .= $before . woocommerce_page_title(false) . $after;
	            } elseif ( is_category() ) {
                    if ( function_exists('learn_press_is_course_category') && learn_press_is_course_category() ) {
                        $parents = array_reverse( get_ancestors( get_queried_object_id(), 'course_category' ) );
                        if (!empty($parents) && $show_home_link) $out .= $sep;
                        foreach ( $parents as $i => $parent ) {
                            $cat = get_term( $parent, 'course_category' );
                            $divider = $i != 0 ? $sep : '';
                            $out .= sprintf( '<a href="%s">%s</a>', get_term_link($cat), $cat->name, $divider );
                        }
                    } else {
                        $cat = get_category(get_query_var('cat'), false);
                        if ($cat->parent != 0) {
                            $cats = get_category_parents($cat->parent, TRUE, $sep);
                            $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                            $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                            if ($show_home_link) $out .= $sep;
                            $out .=  $cats;
                        }
                    }
                    if ( get_query_var('paged') ) {
                        $cat = $cat->cat_ID;
                        $out .= $sep . sprintf($link, esc_url(get_category_link($cat)), get_cat_name($cat)) . $sep . $before . sprintf($this->text_page, get_query_var('paged')) . $after;
                    } else {
                        if ($show_current) $out .= $sep . $before . sprintf($this->text_category, single_cat_title('', false)) . $after;
                    }
                } elseif ( is_search() ) {
                    if ( have_posts() ) {
                        if ($show_home_link && $show_current) $out .= $sep;
                        if ($show_current) $out .= $before . sprintf($this->text_search, get_search_query()) . $after;
                    } else {
                        if ($show_home_link) $out .= $sep;
                        $out .= $before . sprintf($this->text_search, get_search_query()) . $after;
                    }
                } elseif ( is_day() ) {
                    if ($show_home_link) $out .= $sep;
                    $out .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
                    $out .= sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'));
                    if ($show_current) $out .= $sep . $before . get_the_time('d') . $after;
                } elseif ( is_month() ) {
                    if ($show_home_link) $out .= $sep;
                    $out .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
                    if ($show_current) $out .= $sep . $before . get_the_time('F') . $after;
                } elseif ( is_year() ) {
                    if ($show_home_link && $show_current) $out .= $sep;
                    if ($show_current) $out .= $before . get_the_time('Y') . $after;
                } elseif (
                    is_single()
                    && !is_attachment()
                    && !(function_exists('is_product') && is_product())
                ) {
                    if ( $show_home_link ) $out .= $sep;
                    if ( 'post' !== get_post_type() ) {
                        $post_type = get_post_type_object(get_post_type());
	                    $slug = !empty($post_type->rewrite) ? $post_type->rewrite : ['slug' => ''];
                        $out .= sprintf($link, $home_url . $slug['slug'] . '/', $post_type->label);
                        if ($show_current) $out .= $sep . $before . get_the_title() . $after;
                    } else {
                        $cat = get_the_category()[0];
                        $cats = get_category_parents($cat, TRUE, $sep);
                        if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                        $out .= $cats;
                        if ( get_query_var('cpage') ) {
                            $out .= $sep . sprintf($link, esc_url(get_permalink()), get_the_title()) . $sep . $before . sprintf($this->text_cpage, get_query_var('cpage')) . $after;
                        } else {
                            if ($show_current) $out .= $before . get_the_title() . $after;
                        }
                    }
                // Portfolio Taxonomies Archive
                } elseif (is_tax(['portfolio_tag', 'portfolio-category'])) {
                    $archive_link = get_post_type_archive_link(Gostudy_Theme_Helper::get_option('portfolio_slug') ?: 'portfolio');
                    $is_category = is_tax('portfolio-category') ? true : false;
                    $tax_name = get_queried_object()->name;

                    if (get_query_var('paged')) {
                        $out .= $sep . sprintf($link, $archive_link, esc_html__('Portfolio', 'gostudy')) . $sep
                            . $before
                            . ($is_category ? sprintf($this->text_category, $tax_name) : sprintf($this->text_tag, $tax_name))
                            . ', ' . sprintf($this->text_page, get_query_var('paged'))
                            . $after;
                    } else {
                        if ($show_current) $out .= $sep . sprintf($link, $archive_link, esc_html__('Portfolio', 'gostudy')) . $sep
                            . $before
                            . ($is_category ? sprintf($this->text_category, $tax_name) : sprintf($this->text_tag, $tax_name))
                            . $after;
                    }

                // Custom Post Type
                } elseif (
                    !is_single()
                    && !is_page()
                    && get_post_type() != 'post'
                    && !is_404()
                    && !is_author()
                ) {
                    $postObj = get_queried_object();

                    if (!empty($postObj->term_taxonomy_id)) {
                        $postObj->label = $postObj->name;
                    }

                    if (get_query_var('paged')) {
                        $out .= $sep
                            . sprintf($link, get_post_type_archive_link($postObj->name), $postObj->label)
                            . $sep
                            . $before
                            . sprintf($this->text_page, get_query_var('paged'))
                            . $after;
                    } elseif ($show_current && $postObj->label) {
                        $out .= $sep
                            . $before
                            . $postObj->label
                            . $after;
                    }

                } elseif ( is_attachment() ) {
                    if ($show_home_link) $out .= $sep;
                    $parent = get_post($parent_id);
                    $cat = get_the_category($parent->ID)[0] ?? '';
                    if ($cat) {
                        $cats = get_category_parents($cat, TRUE, $sep);
                        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                        $out .= $cats;
                    }
                    $out .= sprintf($link, esc_url(get_permalink($parent)), $parent->post_title);
                    if ($show_current) $out .= $sep . $before . get_the_title() . $after;
                } elseif ( is_page() && ! $parent_id ) {
                    if ($show_current) $out .= $sep . $before . get_the_title() . $after;
                } elseif ( is_page() && $parent_id ) {
                    if ($show_home_link) $out .= $sep;
                    if ($parent_id != $frontpage_id) {
                        $breadcrumbs = array();
                        while ($parent_id) {
                            $page = get_post($parent_id);
                            if ($parent_id != $frontpage_id) {
                                $breadcrumbs[] = sprintf($link, esc_url(get_permalink($page->ID)), get_the_title($page->ID));
                            }
                            $parent_id = $page->post_parent;
                        }
                        $breadcrumbs = array_reverse($breadcrumbs);
                        for ($i = 0; $i < count($breadcrumbs); $i++) {
                            $out .= $breadcrumbs[$i];
                            if ($i != count($breadcrumbs)-1) $out .= $sep;
                        }
                    }
                    if ($show_current) $out .= $sep . $before . get_the_title() . $after;
                } elseif ( is_tag() ) {
                    if ( get_query_var('paged') ) {
                        $tag_id = get_queried_object_id();
                        $tag = get_tag($tag_id);
                        $out .= $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($this->text_page, get_query_var('paged')) . $after;
                    } else {
                        if ($show_current) $out .= $sep . $before . sprintf($this->text_tag, single_tag_title('', false)) . $after;
                    }
                } elseif ( is_author() ) {
                    global $author;
                    $author = get_userdata($author);
                    if ( get_query_var('paged') ) {
                        if ($show_home_link) $out .= $sep;
                        $out .= sprintf($link, get_author_posts_url($author->ID), $author->display_name) . $sep . $before . sprintf($this->text_page, get_query_var('paged')) . $after;
                    } else {
                        if ($show_home_link && $show_current) $out .= $sep;
                        if ($show_current) $out .= $before . sprintf($this->text_author, $author->display_name) . $after;
                    }
                } elseif ( is_404() ) {
                    if ($show_home_link && $show_current) $out .= $sep;
                    if ($show_current) $out .= $before . $this->text_404 . $after;
                } elseif ( has_post_format() && ! is_singular() ) {
                    if ($show_home_link) $out .= $sep;
                    $out .= get_post_format_string( get_post_format() );
                } elseif (
                    function_exists('is_product') && is_product()
                    && function_exists('gostudy_woocommerce_breadcrumb')
                ) {
                    $args = [];
                    $args['delimiter'] = $sep;
                    if ($show_home_link) $out .= $sep;
                    $out .= gostudy_woocommerce_breadcrumb($args);
                }
                $out .= $wrap_after;
            }

            echo Gostudy_Theme_Helper::render_html($out);
        }
    }

    new Gostudy_Breadcrumbs();
}
