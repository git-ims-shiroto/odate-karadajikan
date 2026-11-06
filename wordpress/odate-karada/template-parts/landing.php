<?php defined( 'ABSPATH' ) || exit; $lp = ok_lp_content(); ?>
<main id="main">
<?php if ( $lp['general']['review_note'] ) : ?>
<p class="review-notice"><?php echo esc_html( $lp['general']['review_note'] ); ?></p>
<?php endif; ?>
<?php
foreach ( [ 'hero', 'for-you', 'lessons', 'younger', 'voices', 'instructor', 'schedule', 'trial' ] as $section ) {
    get_template_part( 'template-parts/section', $section, [ 'lp' => $lp ] );
}
?>
</main>
