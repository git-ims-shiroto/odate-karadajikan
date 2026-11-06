<?php defined( 'ABSPATH' ) || exit; $lp = $args['lp']; $s = $lp['trial']; ?>
<section class="section section-trial" id="trial" aria-labelledby="trial-title">
    <div class="faq-area"><?php ok_lp_heading( 'trial-title', 'FAQ', $lp['faq']['title'] ); ?><div class="faq-list">
        <?php foreach ( $lp['faq']['items'] as $item ) : ?><details class="faq-item"><summary><?php echo esc_html( $item['q'] ); ?></summary><div class="faq-answer"><?php echo esc_html( $item['a'] ); ?></div></details><?php endforeach; ?>
    </div></div>
    <div class="trial-card"><p class="section-kicker">TRIAL</p><h3><?php echo esc_html( $s['title'] ); ?></h3><?php ok_lp_campaign(); ?><p><?php echo esc_html( $s['text'] ); ?></p><?php ok_lp_cta(); ?>
    <p id="line-status" tabindex="-1"><?php echo esc_html( ok_lp_line_url( $lp['general']['line_url'] ) ? '友だち追加後、トークからご連絡ください。' : $s['pending'] ); ?></p>
    <?php if ( ok_lp_line_url( $lp['general']['line_url'] ) && $lp['general']['line_qr']['image_id'] ) : ?><div class="line-qr"><?php ok_lp_photo( $lp['general']['line_qr'], '1-1' ); ?></div><?php endif; ?>
    <div class="trial-notes"><?php foreach ( $s['notes'] as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div></div>
</section>
