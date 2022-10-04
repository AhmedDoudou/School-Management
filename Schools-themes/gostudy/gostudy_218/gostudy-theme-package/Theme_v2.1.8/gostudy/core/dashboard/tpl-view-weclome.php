<?php
/**
 * Template Welcome
 *
 *
 * @package gostudy\core\dashboard
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

$theme = wp_get_theme();

$allowed_html = [
    'a' => [
        'href' => true,
        'target' => true,
    ],
];

?>
<div class="rt-welcome_page">
    <div class="rt-welcome_title">
        <h1><?php esc_html_e('Welcome to', 'gostudy');?>
            <?php echo esc_html(wp_get_theme()->get('Name')); ?>
        </h1>
    </div>
    <div class="rt-version_theme">
        <?php esc_html_e('Version - ', 'gostudy');?>
        <?php echo esc_html(wp_get_theme()->get('Version')); ?>
    </div>
    <div class="rt-welcome_subtitle">
            <?php
                echo sprintf(esc_html__('%s is already installed and ready to use! Let\'s build something impressive.', 'gostudy'), esc_html(wp_get_theme()->get('Name'))) ;
            ?>
    </div>

    <div class="rt-welcome-step_wrap">
        <div class="rt-welcome_sidebar left_sidebar">
            <div class="theme-screenshot">
                <img src="<?php echo esc_url(get_template_directory_uri() . "/screenshot.png"); ?>">

            </div>
        </div>
        <div class="rt-welcome_content">
            <div class="step-subtitle">
                <?php
                    echo sprintf(esc_html__('Just complete the steps below and you will be able to use all functionalities of %s theme by RaisTheme:', 'gostudy'), esc_html(wp_get_theme()->get('Name')));
                ?>
            </div>
            <ul>
              <li>
                  <span class="step">
                      <?php esc_html_e('Step 1', 'gostudy');?>
                  </span>
                  <?php esc_html_e('Activate your license.', 'gostudy');?>
                  <span class="attention-title">
                      <strong>
                          <?php esc_html_e('Important:', 'gostudy');?>
                      </strong>
                      <?php esc_html_e('one license  only for one website', 'gostudy');?>
                  </span>
              </li>
              <li>
                  <span class="step">
                      <?php esc_html_e('Step 2', 'gostudy');?>
                  </span>
                  <?php
                echo sprintf( wp_kses( __( 'Check <a target="_blank" href="%s">requirements</a> to avoid errors with your WordPress.', 'gostudy' ), $allowed_html), esc_url( admin_url( 'admin.php?page=gostudy-requirements' ) ) );

                  ?>
              </li>
              <li>
                  <span class="step">
                      <?php esc_html_e('Step 3', 'gostudy');?>
                  </span>
                  <?php esc_html_e('Install Required and recommended plugins.', 'gostudy');?>
              </li>
              <li>
                  <span class="step">
                      <?php esc_html_e('Step 4', 'gostudy');?>
                  </span>
                  <?php esc_html_e('Import demo content', 'gostudy');?>
              </li>
            </ul>
        </div>

    </div>


</div>
