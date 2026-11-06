<?php defined( 'ABSPATH' ) || exit; $lp = ok_lp_content(); ?>
<footer class="site-footer">
    <div class="brand footer-brand"><span><b><?php echo esc_html( $lp['general']['site_name'] ); ?></b><small>大館市のやさしい体操教室</small></span></div>
    <p><?php echo esc_html( $lp['footer']['text'] ); ?></p>
    <small>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $lp['general']['site_name'] ); ?></small>
</footer>
<div class="mobile-cta"><?php ok_lp_cta(); ?></div>
<?php wp_footer(); ?>
</body>
</html>
