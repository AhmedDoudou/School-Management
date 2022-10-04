
    <?php 
        $allowed_html = [
            'a' => [ 'href' => true, 'target' => true ],
        ]; 
    ?>
    
    <h2 class="nav-tab-wrapper">
        <?php

        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-admin-menu' ), esc_html__( 'Welcome', 'gostudy' ) );

        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-theme-active' ), esc_html__( 'Activate Theme', 'gostudy' ) );

        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=rt-theme-options-panel' ), esc_html__( 'Theme Options', 'gostudy' ) );

        if (class_exists('OCDI_Plugin')):
            printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'themes.php?page=pt-one-click-demo-import' ), esc_html__( 'Demo Import', 'gostudy' ) );
        endif;

        // printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-theme-plugins' ), esc_html__( 'Theme Plugins', 'gostudy' ) );
        
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-requirements' ), esc_html__( 'Requirements', 'gostudy' ) );
        
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-help-center' ), esc_html__( 'Help Center', 'gostudy' ) );

        ?>
    </h2>



<div class="gostudy-theme-helper">
    <div class="container-form">
        <h1 class="gostudy-title">
            <?php echo esc_html__('Need Help? RaisTheme Help Center Here', 'gostudy');?>
        </h1>
        <div class="gostudy-content">
            <p class="gostudy-content_subtitle">
                <?php
                    echo wp_kses( __( 'Please read a <a target="_blank" href="https://themeforest.net/page/item_support_policy">Support Policy</a> before submitting a ticket and make sure that your question related to our product issues.', 'gostudy' ), $allowed_html);
                ?>
                <br/>
                    <?php
                    echo esc_html__('If you did not find an answer to your question, feel free to contact us.', 'gostudy');
                    ?>
            </p>
        </div>
        <div class="gostudy-row">
            <div class="gostudy-col gostudy-col-4">
                <div class="gostudy-col_inner">
                    <div class="gostudy-info-box_wrapper">
                        <div class="gostudy-info-box">
                            <div class="gostudy-info-box_icon-wrapper">
                                <div class="gostudy-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/document_icon.png'?>">
                                </div>
                            </div>
                            <div class="gostudy-info-box_content-wrapper">
                                <div class="gostudy-info-box_title">
                                    <h3 class="gostudy-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Documentation', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="gostudy-info-box_content">
                                    <p>
                                        <?php
                                        esc_html_e('Before submitting a ticket, please read the documentation. Probably, your issue already described.', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="gostudy-info-box_btn">
                                    <a target="_blank" href="https://raistheme.com/docs/gostudy/">
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
            <div class="gostudy-col gostudy-col-4">
                <div class="gostudy-col_inner">
                    <div class="gostudy-info-box_wrapper">
                        <div class="gostudy-info-box">
                            <div class="gostudy-info-box_icon-wrapper">
                                <div class="gostudy-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/video_icon.png'?>">
                                </div>
                            </div>
                            <div class="gostudy-info-box_content-wrapper">
                                <div class="gostudy-info-box_title">
                                    <h3 class="gostudy-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Video Tutorials', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="gostudy-info-box_content">
                                    <p>
                                        <?php
                                            esc_html_e('There you can watch tutorial for main issues. How to import demo content? How to create a Mega Menu? etc..', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="gostudy-info-box_btn">
                                    <a target="_blank" href="https://www.youtube.com/channel/UC9xQIVtTyZ0OseEik_-Gwdw">
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
            <div class="gostudy-col gostudy-col-4">
                <div class="gostudy-col_inner">
                    <div class="gostudy-info-box_wrapper">
                        <div class="gostudy-info-box">
                            <div class="gostudy-info-box_icon-wrapper">
                                <div class="gostudy-info-box_icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri()) . '/core/admin/img/dashboard/support_icon.png'?>">
                                </div>
                            </div>
                            <div class="gostudy-info-box_content-wrapper">
                                <div class="gostudy-info-box_title">
                                    <h3 class="gostudy-info-box_icon-heading">
                                        <?php
                                            esc_html_e('Support forum', 'gostudy');
                                        ?>
                                    </h3>
                                </div>
                                <div class="gostudy-info-box_content">
                                    <p>
                                        <?php
                                            esc_html_e('If you did not find an answer to your question, submit a ticket with well describe your issue.', 'gostudy');
                                        ?>
                                    </p>
                                </div>
                                <div class="gostudy-info-box_btn">
                                    <a target="_blank" href="https://raistheme.com/support/wp-admin/edit.php?post_type=ticket">
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
                echo wp_kses( __( 'Do You have some other questions? Need Customization? Pre-purchase questions? Ask it <a  target="_blank"  href="mailto:help.pixelcurve@gmail.com">there!</a>', 'gostudy' ), $allowed_html);
            ?>
        </div>

    </div>
</div>

