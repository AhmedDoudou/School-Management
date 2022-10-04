<?php
/**
 * Template for displaying primary course meta data such as: Instructor, Categories, Reviews (addons)...
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) or die;
$course = LP_Global::course();

if ( ! $course ) {
    return;
}
?>

<div class="course-featured-review">
    
    <div class="featured-review__stars">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
    </div>

</div>