<?php

add_filter('admin_body_class', 'gostudy_admin_body_class');

function gostudy_admin_body_class($classes)
{

    if (gostudy_check_tvc()) {
        return "$classes no_gostudy_unlock";
    } else {
        return "$classes gostudy_unlock";
    }
}

function gostudy_tvf_register_settings()
{
    add_option('gostudy_tv_option', '');
    register_setting('gostudy_tv_options_group', 'gostudy_tv_option', 'gostudy_tv_callback');
}
add_action('admin_init', 'gostudy_tvf_register_settings');

function gostudy_tvf_register_options_page()
{
    add_options_page('Theme Verify', 'Theme Verify', 'manage_options', 'gostudy_tvf', 'gostudy_tv_options_page');
}
add_action('admin_menu', 'gostudy_tvf_register_options_page');

function gostudy_tv_options_page()
{
    ?>
<div class="gostudy-activation-theme_form">
    <div class="container-form">
<form method="post" action="options.php">
      <?php settings_fields('gostudy_tv_options_group');?>

        <h1 class="gostudy-title"><?php esc_html_e('Activate Your Licence', 'gostudy');?></h1>
        <div class="gostudy-content">
            <p class="gostudy-content_subtitle">
                <?php echo sprintf(esc_html__('Welcome and thank you for Choosing %s Theme!', 'gostudy'), esc_html(wp_get_theme()->get('Name'))); ?>
                <br/>
                <?php echo sprintf(esc_html__('The %s theme needs to be activated to enable demo import installation and customer support service.', 'gostudy'), esc_html(wp_get_theme()->get('Name'))); ?>
            </p>
        </div>

        <?php if (gostudy_check_tvc() == false): ?>
        <div class="help-description">
            <a href="https://www.youtube.com/watch?v=yTScONNFnZ8&feature=emb_title&ab_channel=Envato" target="_blank"><?php esc_html_e('How to find purchase code?', 'gostudy');?></a>
        </div>

        <input type="text" placeholder="Enter Your Purchase Code"  id="gostudy_tv_option" name="gostudy_tv_option" value="<?php echo get_option('gostudy_tv_option'); ?>" />

           <div class="licnese-active-button">
                <?php submit_button(__('Activate', 'gostudy'), 'primary');?>
           </div>
        <?php endif;?>

        <div class="form-group hidden_group">
            <input type="hidden" name="deactivate_theme" value=" " class="form-control">
        </div>

        <?php
            $theme_fv_code = get_option('gostudy_tv_option');
            if (!empty($theme_fv_code)) {
                ?>
                        <input type="hidden" name="gostudy_tv_option" value=" " class="form-control">
                    <?php
            }
        ?>

        <?php wp_nonce_field('purchase-activation', 'security');?>

        <?php if (gostudy_check_tvc()): ?>
            <button type="submit" class="button button-primary deactivate_theme-license" value="submit">
                <span class="text-btn"><?php esc_html_e('Deactivate', 'gostudy');?></span>
                <span class="loading-icon"></span>
            </button>
        <?php endif;?>

      </form>


        <?php
            if (gostudy_check_tvc()) {
        ?>

        <div class="gostudy-activation-theme_congratulations">
            <h1 class="gostudy-title">
                <?php esc_html_e('Thank you!', 'gostudy');?>
            </h1>
            <span><?php esc_html_e('Your theme\'s license is activated successfully.', 'gostudy');?></span>

        </div>
            <a href="<?php echo admin_url('themes.php?page=pt-one-click-demo-import'); ?>" class="button button-primary button-large button-next import-demo-next"><?php esc_html_e('Import Demo', 'gostudy');?></a>
        <?php

    } else {

        $theme_fv_code = get_option('gostudy_tv_option');?>

        <?php if (!empty($theme_fv_code)): ?>
             <div class="gostudy-activation-theme_congratulations invalid">
                <h1 class="gostudy-title">
                   <?php esc_html_e('Invalid Purchase Code', 'gostudy');?>
                </h1>
            </div>
        <?php endif?>

        <?php }?>

    </div>
</div>
 <?php
}

function gostudy_check_tvc()
{
update_option('gostudy_tv_option','b73c9ad8-9184-4a9f-907d-f5b4a9fb35e4');

    $theme_fv_code = trim(get_option('gostudy_tv_option'));

    if (!empty($theme_fv_code && strlen($theme_fv_code) == '36')) {
        return true;
    }
}

function gostudy_import_files()
{
    return array(

        array(
            'import_file_name'             => 'Home Default (Tutor)',
            'categories'                   => array('Tutor'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/tutor/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/tutor/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/tutor/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/tutor/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Home Default (LearnPress)',
            'categories'                   => array('LearnPress'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Home Default (LearnDash)',
            'categories'                   => array('LearnDash'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learndash/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learndash/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learndash/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learndash/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/',
            'import_notice'                => __("<span home style='color:#ec5761'>- Before import the demo, make sure you have installed the LearnDash LMS plugin.</span> <br> <br>-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Online Academy (Tutor)',
            'categories'                   => array('Tutor'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/tutor/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/tutor/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/tutor/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-online-academy.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/tutor/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-academy',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Online Academy (LearnPress)',
            'categories'                   => array('LearnPress'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-online-academy.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-academy',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Online Academy (LearnDash)',
            'categories'                   => array('LearnDash'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learndash/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learndash/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learndash/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learndash-online-academy.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learndash/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-academy',
            'import_notice'                => __("<span home style='color:#ec5761'>- Before import the demo, make sure you have installed the LearnDash LMS plugin.</span> <br> <br>-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Online Courses (Tutor)',
            'categories'                   => array('Tutor'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/tutor/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/tutor/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/tutor/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-online-courses.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/tutor/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-courses',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),

        array(
            'import_file_name'             => 'Online Courses (LearnPress)',
            'categories'                   => array('LearnPress'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-online-courses.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-courses',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Online Courses (LearnDash)',
            'categories'                   => array('LearnDash'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learndash/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learndash/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learndash/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learndash-online-courses.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learndash/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/online-courses',
            'import_notice'                => __("<span home style='color:#ec5761'>- Before import the demo, make sure you have installed the LearnDash LMS plugin.</span> <br> <br>-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Learning Coach (Tutor)',
            'categories'                   => array('Tutor'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/tutor/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/tutor/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/tutor/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-learning-coach.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/tutor/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/Learning Coach/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),

        array(
            'import_file_name'             => 'Learning Coach (LearnPress)',
            'categories'                   => array('LearnPress'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-learning-coach.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/Learning Coach/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),

        array(
            'import_file_name'             => 'Learning Coach (LearnDash)',
            'categories'                   => array('LearnDash'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learndash/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learndash/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learndash/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learndash-learning-coach.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learndash/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/Learning Coach/',
            'import_notice'                => __("<span home style='color:#ec5761'>- Before import the demo, make sure you have installed the LearnDash LMS plugin.</span> <br> <br>-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Home Classic (Tutor)',
            'categories'                   => array('Tutor'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/tutor/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/tutor/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/tutor/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-home-classic.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/tutor/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/home-classic/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Home Classic (LearnPress)',
            'categories'                   => array('LearnPress'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-home-classic.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learnpress/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/home-classic/',
            'import_notice'                => __("-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        ),
        array(
            'import_file_name'             => 'Home Classic (LearnDash)',
            'categories'                   => array('LearnDash'),
            'local_import_file'            => trailingslashit(get_template_directory()) . 'admin/demo/learndash/content.xml',
            'local_import_widget_file'     => trailingslashit(get_template_directory()) . 'admin/demo/learndash/widget_data.wie',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'admin/demo/learndash/customizer.dat',
            'import_preview_image_url'     => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learndash-home-classic.jpg',
            'local_import_redux'           => array(
                array(
                    'file_path'   => trailingslashit(get_template_directory()) . 'admin/demo/learndash/redux.json',
                    'option_name' => 'gostudy_set',
                ),
            ),
            'preview_url'                  => 'https://raistheme.com/wp/gostudy/home-classic/',
            'import_notice'                => __("<span home style='color:#ec5761'>- Before import the demo, make sure you have installed the LearnDash LMS plugin.</span> <br> <br>-  Don't activate more than one LMS plugin at the same site. Otherwise, same LMS page URL will create a problem. <br>- Images do not include in demo import If you want to use images from demo content, you should check the license for every image.", 'gostudy'),
        )
    );
}

function gostudy_import_flies()
{
    return array(

        array(
            'import_file_name'         => 'Home Default (Tutor)',
            'categories'               => array('Tutor'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/',
        ),
        array(
            'import_file_name'         => 'Home Default (LearnPress)',
            'categories'               => array('LearnPress'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/',
        ),

        array(
            'import_file_name'         => 'Home Default (LearnDash)',
            'categories'               => array('LearnDash'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'screenshot.png',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/',
        ),

        array(
            'import_file_name'         => 'Online Academy (Tutor)',
            'categories'               => array('Tutor'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-online-academy.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-academy',
        ),

        array(
            'import_file_name'         => 'Online Academy (LearnPress)',
            'categories'               => array('LearnPress'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-online-academy.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-academy',
        ),
   array(
            'import_file_name'         => 'Online Academy (LearnDash)',
            'categories'               => array('LearnDash'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-online-academy.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-academy',
        ),

        array(
            'import_file_name'         => 'Online Courses (Tutor)',
            'categories'               => array('Tutor'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-courses',
        ),
        array(
            'import_file_name'         => 'Online Courses (LearnPress)',
            'categories'               => array('LearnPress'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-courses',
        ),
        array(
            'import_file_name'         => 'Online Courses (LearnDash)',
            'categories'               => array('LearnDash'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/online-courses',
        ),
        array(
            'import_file_name'         => 'Learning Coach (Tutor)',
            'categories'               => array('Tutor'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/learning-coach/',
        ),
        array(
            'import_file_name'         => 'Learning Coach (LearnPress)',
            'categories'               => array('LearnPress'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/learning-coach/',
        ),
        array(
            'import_file_name'         => 'Learning Coach (LearnDash)',
            'categories'               => array('LearnDash'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-learning-coach.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/learning-coach/',
        ),
        array(
            'import_file_name'         => 'Home Classic (Tutor)',
            'categories'               => array('Tutor'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/tutor-home-classic.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/home-classic/',
        ),
        array(
            'import_file_name'         => 'Home Classic (LearnPress)',
            'categories'               => array('LearnPress'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-home-classic.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/learnpress/home-classic/',
        ),
        array(
            'import_file_name'         => 'Home Classic (LearnDash)',
            'categories'               => array('LearnDash'),
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'admin/demo/images/learnpress-home-classic.jpg',
            'preview_url'              => 'https://raistheme.com/wp/gostudy/learnpress/home-classic/',
        )

    );
}

if (gostudy_check_tvc()) {
    $gostudy_tvfi = "gostudy_import_files";
} else {
    $gostudy_tvfi = "gostudy_import_flies";
}
add_filter('pt-ocdi/import_files', $gostudy_tvfi);

function gostudy_dialog_options($options)
{
    return array_merge($options, array(
        'width'       => 300,
        'dialogClass' => 'wp-dialog',
        'resizable'   => false,
        'height'      => 'auto',
        'modal'       => true,
    ));
}
add_filter('pt-ocdi/confirmation_dialog_options', 'gostudy_dialog_options', 10, 1);
add_filter('pt-ocdi/disable_pt_branding', '__return_true');

function gostudy_after_import_setup($selected_import)
{
    // Assign menus to their locations.
    $main_menu = get_term_by('name', 'Main', 'nav_menu');

    set_theme_mod('nav_menu_locations', array(
        'main_menu' => $main_menu->term_id,
    )
    );

    // Tutor LMS
    if ('Home Default (Tutor)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Default');
    } elseif ('Online Academy (Tutor)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Academy');
    } elseif ('Online Courses (Tutor)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Courses');
    } elseif ('Learning Coach (Tutor)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Learning Coach');
    }
    // LearnDash LMS
    if ('Home Default (LearnDash)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Default');
    } elseif ('Online Academy (LearnDash)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Academy');
    } elseif ('Online Courses (LearnDash)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Courses');
    } elseif ('Learning Coach (LearnDash)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Learning Coach');
    }
    // LearnPress LMS
    if ('Home Default (LearnPress)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Default');


    } elseif ('Home Classic (Tutor)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Classic');

        //Import Revolution Slider
           if ( class_exists( 'RevSlider' ) ) {
               $slider_array = array(
                trailingslashit(get_template_directory()) . 'admin/demo/slider/home-classic.zip',
                  );

               $slider = new RevSlider();

               foreach($slider_array as $filepath){
                 $slider->importSliderFromPost(true,true,$filepath);
               }

               echo ' Slider processed';
          }

    }elseif ('Home Classic (LearnPress)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Classic');

        //Import Revolution Slider
           if ( class_exists( 'RevSlider' ) ) {
               $slider_array = array(
                trailingslashit(get_template_directory()) . 'admin/demo/slider/home-classic.zip',
                  );

               $slider = new RevSlider();

               foreach($slider_array as $filepath){
                 $slider->importSliderFromPost(true,true,$filepath);
               }

               echo ' Slider processed';
          }

    } elseif ('Home Classic (LearnDash)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Home Classic');

        //Import Revolution Slider
           if ( class_exists( 'RevSlider' ) ) {
               $slider_array = array(
                trailingslashit(get_template_directory()) . 'admin/demo/slider/home-classic.zip',
                  );

               $slider = new RevSlider();

               foreach($slider_array as $filepath){
                 $slider->importSliderFromPost(true,true,$filepath);
               }

               echo ' Slider processed';
          }

    } elseif ('Online Academy (LearnPress)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Academy');
    } elseif ('Online Courses (LearnPress)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Online Courses');
    } elseif ('Learning Coach (LearnPress)' === $selected_import['import_file_name']) {
        $front_page_id = get_page_by_title('Learning Coach');
    }

    $blog_page_id = get_page_by_title('Blog');
    update_option('show_on_front', 'page');
    update_option('page_on_front', $front_page_id->ID);
    update_option('page_for_posts', $blog_page_id->ID);

    // Reset site permalink
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');

}
add_action('pt-ocdi/after_import', 'gostudy_after_import_setup');

function ocdi_before_content_import($selected_import)
{
    // Customizer reset
    delete_option('theme_mods_' . get_option('stylesheet'));
    // Old style.
    $theme_name = get_option('current_theme');
    if (false === $theme_name) {
        $theme_name = wp_get_theme()->get('gostudy');
    }
    delete_option('mods_' . $theme_name);

    // Activate/Deactivate plugins
    // Check LearnPress LMS
    if ('Home Default (LearnPress)' === $selected_import['import_file_name']) {
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        if (file_exists($plugin_file) && !class_exists('LearnPress')) {
            activate_plugin('/learnpress/learnpress.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress-course-review/learnpress-course-review.php';
        if (file_exists($plugin_file) && !class_exists('LP_Addon_Course_Review_Preload')) {
            activate_plugin('/learnpress-course-review/learnpress-course-review.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }
    elseif ('Online Academy (LearnPress)' === $selected_import['import_file_name']) {
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        if (file_exists($plugin_file) && !class_exists('LearnPress')) {
            activate_plugin('/learnpress/learnpress.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress-course-review/learnpress-course-review.php';
        if (file_exists($plugin_file) && !class_exists('LP_Addon_Course_Review_Preload')) {
            activate_plugin('/learnpress-course-review/learnpress-course-review.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }

    elseif ('Online Courses (LearnPress)' === $selected_import['import_file_name']) {
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        if (file_exists($plugin_file) && !class_exists('LearnPress')) {
            activate_plugin('/learnpress/learnpress.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress-course-review/learnpress-course-review.php';
        if (file_exists($plugin_file) && !class_exists('LP_Addon_Course_Review_Preload')) {
            activate_plugin('/learnpress-course-review/learnpress-course-review.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }

    elseif ('Learning Coach (LearnPress)' === $selected_import['import_file_name']) {
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        if (file_exists($plugin_file) && !class_exists('LearnPress')) {
            activate_plugin('/learnpress/learnpress.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress-course-review/learnpress-course-review.php';
        if (file_exists($plugin_file) && !class_exists('LP_Addon_Course_Review_Preload')) {
            activate_plugin('/learnpress-course-review/learnpress-course-review.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }
    elseif ('Home Classic (LearnPress)' === $selected_import['import_file_name']) {
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        if (file_exists($plugin_file) && !class_exists('LearnPress')) {
            activate_plugin('/learnpress/learnpress.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/learnpress-course-review/learnpress-course-review.php';
        if (file_exists($plugin_file) && !class_exists('LP_Addon_Course_Review_Preload')) {
            activate_plugin('/learnpress-course-review/learnpress-course-review.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }

    // Check Tutor LMS
    elseif ('Home Default (Tutor)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor/tutor.php';
        if (file_exists($plugin_file) && !function_exists('tutor')) {
            activate_plugin('/tutor/tutor.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php';
        if (file_exists($plugin_file) && !function_exists('tutor_pro')) {
            activate_plugin('/tutor-pro/tutor-pro.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Online Academy (Tutor)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor/tutor.php';
        if (file_exists($plugin_file) && !function_exists('tutor')) {
            activate_plugin('/tutor/tutor.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php';
        if (file_exists($plugin_file) && !function_exists('tutor_pro')) {
            activate_plugin('/tutor-pro/tutor-pro.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Online Courses (Tutor)' === $selected_import['import_file_name']) {
        if (class_exists('Tutor')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor/tutor.php';
        if (file_exists($plugin_file) && !function_exists('tutor')) {
            activate_plugin('/tutor/tutor.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php';
        if (file_exists($plugin_file) && !function_exists('tutor_pro')) {
            activate_plugin('/tutor-pro/tutor-pro.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Learning Coach (Tutor)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor/tutor.php';
        if (file_exists($plugin_file) && !function_exists('tutor')) {
            activate_plugin('/tutor/tutor.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php';
        if (file_exists($plugin_file) && !function_exists('tutor_pro')) {
            activate_plugin('/tutor-pro/tutor-pro.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Home Classic (Tutor)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (class_exists('SFWD_LMS')) {
            deactivate_plugins('/sfwd-lms/sfwd_lms.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor/tutor.php';
        if (file_exists($plugin_file) && !function_exists('tutor')) {
            activate_plugin('/tutor/tutor.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php';
        if (file_exists($plugin_file) && !function_exists('tutor_pro')) {
            activate_plugin('/tutor-pro/tutor-pro.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }

    // Check LearnDash LMS
    elseif ('Home Default (LearnDash)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php';
        if (file_exists($plugin_file) && !class_exists('SFWD_LMS')) {
            activate_plugin('/sfwd-lms/sfwd_lms.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Online Academy (LearnDash)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php';
        if (file_exists($plugin_file) && !class_exists('SFWD_LMS')) {
            activate_plugin('/sfwd-lms/sfwd_lms.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Online Courses (LearnDash)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php';
        if (file_exists($plugin_file) && !class_exists('SFWD_LMS')) {
            activate_plugin('/sfwd-lms/sfwd_lms.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Learning Coach (LearnDash)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php';
        if (file_exists($plugin_file) && !class_exists('SFWD_LMS')) {
            activate_plugin('/sfwd-lms/sfwd_lms.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }
    elseif ('Home Classic (LearnDash)' === $selected_import['import_file_name']) {
        if (class_exists('LearnPress')) {
            deactivate_plugins('/learnpress/learnpress.php');
        }
        if (class_exists('LP_Addon_Course_Review_Preload')) {
            deactivate_plugins('/learnpress-course-review/learnpress-course-review.php');
        }
        if (function_exists('tutor')) {
            deactivate_plugins('/tutor/tutor.php');
        }
        if (function_exists('tutor_pro')) {
            deactivate_plugins('/tutor-pro/tutor-pro.php');
        }
        $plugin_file = WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php';
        if (file_exists($plugin_file) && !class_exists('SFWD_LMS')) {
            activate_plugin('/sfwd-lms/sfwd_lms.php');
        }
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');

    }


}
add_action('pt-ocdi/before_content_import', 'ocdi_before_content_import');

