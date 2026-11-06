<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['for_you']; ?>
<section class="section section-for" id="for-you" aria-labelledby="for-title">
    <?php ok_lp_heading( 'for-title', 'FOR YOU', $s['title'], $s['lead'] ); ?>
    <div class="concern-grid">
    <?php foreach ( $s['items'] as $item ) : ?>
        <div class="concern-card"><span class="concern-icon" aria-hidden="true"><?php echo esc_html( $item['icon'] ); ?></span><p><?php echo esc_html( $item['title'] ); ?></p></div>
    <?php endforeach; ?>
    </div>
    <div class="reassurance">
        <div class="reassurance-photo"><?php ok_lp_photo( $s['photo'], '3-2' ); ?></div>
        <div><h3><?php echo esc_html( $s['subtitle'] ); ?></h3><p><?php echo esc_html( $s['text'] ); ?></p>
            <ul class="check-list"><?php foreach ( $s['checks'] as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
        </div>
    </div>
</section>
