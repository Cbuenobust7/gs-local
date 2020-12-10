<?php
/**
 * Template Name: User Dashboard CodeTactic
 * Created by Carlos Bueno B.
 * User: Carlos
 * Date: 11/09/16
 * Time: 11:00 PM
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $paged, $houzez_local, $current_user, $dashboard_invoices;
$dashboard_invoices = houzez_get_template_link_2('template/user_dashboard_invoices.php');

global $houzez_local;
get_header();

if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}
?>
<header class="header-main-wrap dashboard-header-main-wrap">
 
</header><!-- .header-main-wrap -->

<section class="dashboard-content-wrap">
    <div class="dashboard-content-inner-wrap">
        <div class="dashboard-content-block-wrap">
        
        <?php
     include './PAD.php';
     ?>

        </div><!-- dashboard-content-block-wrap -->
    </div><!-- dashboard-content-inner-wrap -->
</section><!-- dashboard-content-wrap -->
<section class="dashboard-side-wrap">
    <?php get_template_part('template-parts/dashboard/side-wrap'); ?>
</section>

<?php get_footer(); ?>