<?php
/**
 * The template for displaying content of archive page meetings
 *
 * This template can be overridden by copying it to yourtheme/video-conferencing-zoom/content-meeting.php.
 *
 * @author Deepen
 * @since 3.0.0
 * @updated 3.6.0
 */

defined( 'ABSPATH' ) || exit;
if (class_exists('Zoom_Video_Conferencing_Api')) {
global $zoom;

if ( ! vczapi_pro_version_active() && vczapi_pro_check_type( $zoom['api']->type ) || empty( $zoom ) || ! empty( $zoom['api']->code ) ) {
    return;
}
?>
<div class="vczapi-col-4 vczapi-pb-4">
    <div class="vczapi-list-zoom-meetings--item">
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="vczapi-list-zoom-meetings--item__image">
                 <a href="<?php echo esc_url( get_the_permalink() ) ?>" class="">
                <?php the_post_thumbnail(); ?>
                </a>
                <div class="course__categories">
                    <?php echo get_the_term_list(get_the_ID(), 'zoom-meeting'); ?>
                </div>
            </div><!--Image End-->
        <?php } ?>
        <div class="vczapi-list-zoom-meetings--item__details">
            <a href="<?php echo esc_url( get_the_permalink() ) ?>" class="vczapi-list-zoom-title-link"><h3><?php the_title(); ?></h3></a>
            <div class="vczapi-list-zoom-meetings--item__details__meta">
                <div class="hosted-by meta">
                    <i class="flaticon-user-1"></i> <strong><?php esc_html_e( 'Hosted By', 'gostudy' ); ?></strong> <?php echo esc_attr( '&nbsp;:&nbsp;' ); ?> <span> <?php echo apply_filters( 'vczapi_host_name', $zoom['host_name'] ); ?></span>
                </div>
                <?php
                if ( vczapi_pro_version_active() && vczapi_pro_check_type($zoom['api']->type ) ) {
                    $type      = ! empty( $zoom['api']->type ) ? $zoom['api']->type : false;
                    $timezone  = ! empty( $zoom['api']->timezone ) ? $zoom['api']->timezone : false;
                    $occurence = ! empty( $zoom['api']->occurrences ) ? $zoom['api']->occurrences : false;
                    if ( ! empty( $occurence ) ) {
                        $start_time = Codemanas\ZoomPro\Helpers::get_latest_occurence_by_type( $type, $timezone, $occurence );
                        ?>
                        <div class="start-date meta">
                            <i class="flaticon-next"></i> <strong><?php esc_html_e( 'Next Occurrence', 'gostudy' ); ?><?php echo esc_attr( '&nbsp;:&nbsp;' ); ?></strong>
                            <span><?php echo vczapi_dateConverter( $start_time, $timezone, 'F j, Y @ g:i a' ); ?></span>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="start-date meta">
                            <i class="flaticon-wall-clock"></i> <strong><?php esc_html_e( 'Start Time', 'gostudy' ); ?>:</strong>
                            <span><?php echo vczapi_dateConverter( $zoom['start_date'], 'UTC', 'F j, Y @ g:i a' ); ?></span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="start-date meta">
                        <i class="flaticon-price-tag"></i> <strong><?php esc_html_e( 'Type', 'gostudy' ); ?><?php echo esc_attr( '&nbsp;:&nbsp;' ); ?></strong>
                        <span><?php esc_html_e( 'Recurring ', 'gostudy' ); ?></span>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="start-date meta">
                        <i class="flaticon-wall-clock"></i> <strong><?php esc_html_e( 'Start', 'gostudy' ); ?><?php echo esc_attr( '&nbsp;:&nbsp;' ); ?></strong>
                        <span><?php echo vczapi_dateConverter( $zoom['api']->start_time, $zoom['api']->timezone, 'F j, Y @ g:i a' ); ?></span>
                    </div>
                <?php } ?>
                <?php if (1>1) { ?>
                    <div class="timezone meta">
                         <i class="flaticon-worldwide"></i> <strong><?php esc_html_e( 'Timezone', 'gostudy' ); ?><?php echo esc_attr( '&nbsp;:&nbsp;' ); ?></strong> <span><?php echo esc_html($zoom['api']->timezone); ?></span>
                    </div>
                <?php } ?>
            </div>

        </div><!--Details end-->
    </div><!--List item end-->
</div>
<?php } ?>
