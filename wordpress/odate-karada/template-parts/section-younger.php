<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['younger']; ?>
<section class="section section-younger" id="younger" aria-labelledby="younger-title">
    <div class="younger-card"><div class="younger-copy"><p class="section-kicker">FOR YOU</p><h2 id="younger-title"><?php echo esc_html( $s['title'] ); ?></h2><p><?php echo esc_html( $s['text'] ); ?></p><p class="soft-note"><?php echo esc_html( $s['note'] ); ?></p></div>
    <div class="younger-photo"><?php ok_lp_photo( $s['photo'], '4-3' ); ?></div></div>
</section>
