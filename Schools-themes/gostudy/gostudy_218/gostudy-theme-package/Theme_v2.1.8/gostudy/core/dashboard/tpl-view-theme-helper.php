<?php
/**
 * Template Activate Theme
 *
 *
 * @package gostudy\core\dashboard
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

$allowed_html = [
    'a' => [ 'href' => true, 'target' => true ],
];

?>
<div class="rt-theme-helper">
    <div class="container-form">
        <h1 class="rt-title">
            <?php echo esc_html__('Need Help? RaisTheme Help Center Here', 'gostudy');?>
        </h1>
        <div class="rt-content">
            <p class="rt-content_subtitle">
                <?php
                    echo wp_kses( __( 'Please read a <a target="_blank" href="https://themeforest.net/page/item_support_policy">Support Policy</a> before submitting a ticket and make sure that your question related to our product issues.', 'gostudy' ), $allowed_html);
                ?>
                <br/>
                    <?php
                    echo esc_html__('If you did not find an answer to your question, feel free to contact us.', 'gostudy');
                    ?>
            </p>
        </div>
        <div class="rt-row">
            <div class="rt-col rt-col-4">
                <div class="rt-col_inner">
                    <div class="rt-info-box_wrapper">
                        <div class="rt-info-box">
                            <div class="rt-info-box_icon-wrapper">
                                <div class="rt-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/document_icon.png'?>">
                                </div>
                            </div>
                            <div class="rt-info-box_content-wrapper">
                                <div class="rt-info-box_title">
                                    <h3 class="rt-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Documentation', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="rt-info-box_content">
                                    <p>
                                        <?php
                                        esc_html_e('Before submitting a ticket, please read the documentation. Probably, your issue already described.', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="rt-info-box_btn">
                                    <a target="_blank" href="http://raistheme.com/docs/gostudy/">
                                        <?php
                                            esc_html_e('Visit Documentation', 'gostudy');
                                        ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rt-col rt-col-4">
                <div class="rt-col_inner">
                    <div class="rt-info-box_wrapper">
                        <div class="rt-info-box">
                            <div class="rt-info-box_icon-wrapper">
                                <div class="rt-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/video_icon.png'?>">
                                </div>
                            </div>
                            <div class="rt-info-box_content-wrapper">
                                <div class="rt-info-box_title">
                                    <h3 class="rt-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Video Tutorials', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="rt-info-box_content">
                                    <p>
                                        <?php
                                            esc_html_e('There you can watch tutorial for main issues. How to import demo content? How to create a Mega Menu? etc..', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="rt-info-box_btn">
                                    <a target="_blank" href="#">
                                        <?php
                                            esc_html_e('Watch Tutorials', 'gostudy');
                                        ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rt-col rt-col-4">
                <div class="rt-col_inner">
                    <div class="rt-info-box_wrapper">
                        <div class="rt-info-box">
                            <div class="rt-info-box_icon-wrapper">
                                <div class="rt-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/support_icon.png'?>">
                                </div>
                            </div>
                            <div class="rt-info-box_content-wrapper">
                                <div class="rt-info-box_title">
                                    <h3 class="rt-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Support forum', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="rt-info-box_content">
                                    <p>
                                        <?php
                                            esc_html_e('If you did not find an answer to your question, submit a ticket with well describe your issue.', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="rt-info-box_btn">
                                    <a target="_blank" href="https://raistheme.com/support/">
                                        <?php
                                            esc_html_e('Create a ticket', 'gostudy');
                                        ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="theme-helper_desc">
            <?php
                echo wp_kses( __( 'Do You have some other questions? Need Customization? Pre-purchase questions? Ask it <a  target="_blank"  href="mailto:help.raistheme@gmail.com">there!</a>', 'gostudy' ), $allowed_html);
            ?>
        </div>

    </div>
</div>

