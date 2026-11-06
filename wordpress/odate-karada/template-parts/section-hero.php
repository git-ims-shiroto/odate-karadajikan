<?php defined( 'ABSPATH' ) || exit; $lp = $args['lp']; $s = $lp['hero']; ?>
<section class="hero" id="top" aria-labelledby="hero-title">
    <div class="hero-copy">
        <p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
        <h1 id="hero-title"><span><?php echo esc_html( $s['title'] ); ?></span><em><?php echo esc_html( $s['accent'] ); ?></em></h1>
        <p class="hero-lead"><?php echo esc_html( $s['lead'] ); ?></p>
        <?php ok_lp_campaign(); ok_lp_cta(); ?>
        <p class="microcopy"><?php echo esc_html( $s['note'] ); ?></p>
    </div>
    <div class="hero-visual"><?php ok_lp_photo( $s['photo'], '16-9', 'full' ); ?></div>
</section>
