<?php
defined( 'ABSPATH' ) || exit;
use Gostudy_Theme_Helper as Gostudy;
if ( ! class_exists( 'Gostudy_Author_archive' ) ) {
class Gostudy_Author_archive {
     public function __construct() {
          add_action( 'pre_get_posts', [ $this, 'gostudy_add_cpt_author' ] );
     }
    public function gostudy_add_cpt_author( $query ) {


        //$gostudy_author_archive_posts = Gostudy_Theme_Helper::get_mb_option('gostudy_author_archive_posts');
        $gostudy_author_archive_posts_type = Gostudy_Theme_Helper::get_mb_option('gostudy_author_archive_posts_type');

    

            if ( !is_admin() && $query->is_author() && $query->is_main_query() ) {
                $query->set( 'post_type', 'sfwd-courses'  );
            }
    



     }
}
$var = new Gostudy_Author_archive();
}

