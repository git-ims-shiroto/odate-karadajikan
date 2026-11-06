<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['voices']; ?>
<section class="section section-voices" id="voices" aria-labelledby="voices-title">
    <?php ok_lp_heading( 'voices-title', 'VOICE', $s['title'] ); ?>
    <?php if ( $s['note'] ) : ?><p class="sample-warning"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
    <div class="voice-grid">
        <?php foreach ( $s['items'] as $item ) : ?>
        <article class="voice-card"><div class="voice-person"><span class="voice-avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $item['age'], -3, 1 ) ); ?></span><div><b><?php echo esc_html( $item['age'] ); ?></b><span><?php echo esc_html( $item['tag'] ); ?></span></div></div><blockquote><?php echo esc_html( $item['quote'] ); ?></blockquote></article>
        <?php endforeach; ?>
    </div>
    <p class="personal-note"><?php echo esc_html( $s['disclaimer'] ); ?></p>
</section>
