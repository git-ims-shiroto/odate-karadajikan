<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['voices'];
// 最初の3件を表示し、4件以上あるときは「もっと見る」で全件をドロワーに表示する（assets/js/voice-drawer.js）。
$limit = 3;
$items = array_values( $s['items'] );
$card = function ( $item ) {
    $name = trim( (string) $item['age'] );
    $parts = preg_split( '/\s+/u', $name );
    $initial = mb_substr( (string) end( $parts ), 0, 1 );
    echo '<article class="voice-card"><div class="voice-person"><span class="voice-avatar" aria-hidden="true">' . esc_html( $initial ) . '</span><div><b>' . esc_html( $name ) . '</b><span>' . esc_html( $item['tag'] ) . '</span></div></div><blockquote>' . esc_html( $item['quote'] ) . '</blockquote></article>';
};
?>
<section class="section section-voices" id="voices" aria-labelledby="voices-title">
    <?php ok_lp_heading( 'voices-title', 'VOICE', $s['title'] ); ?>
    <?php if ( $s['note'] ) : ?><p class="sample-warning"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
    <div class="voice-grid">
        <?php foreach ( array_slice( $items, 0, $limit ) as $item ) { $card( $item ); } ?>
    </div>
    <?php if ( count( $items ) > $limit ) : ?>
    <div class="voice-more" data-voice-more hidden><button class="button button-outline" type="button" data-drawer-open="voiceDrawer" aria-haspopup="dialog" aria-controls="voiceDrawer">もっと見る<span>（全<?php echo esc_html( count( $items ) ); ?>件）</span></button></div>
    <dialog class="voice-drawer" id="voiceDrawer" aria-labelledby="voiceDrawerTitle">
        <div class="voice-drawer-panel">
            <header class="voice-drawer-header">
                <strong id="voiceDrawerTitle"><?php echo esc_html( $s['title'] ); ?></strong>
                <button type="button" data-drawer-close aria-label="閉じる">×</button>
            </header>
            <div class="voice-drawer-list"><?php foreach ( $items as $item ) { $card( $item ); } ?></div>
            <p class="voice-drawer-note"><?php echo esc_html( $s['disclaimer'] ); ?></p>
        </div>
    </dialog>
    <?php endif; ?>
    <p class="personal-note"><?php echo esc_html( $s['disclaimer'] ); ?></p>
</section>
