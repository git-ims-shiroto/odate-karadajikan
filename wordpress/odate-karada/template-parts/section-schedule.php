<?php defined( 'ABSPATH' ) || exit; $lp = $args['lp']; $s = $lp['pricing'];
// クラスを曜日ごとにまとめ、曜日タブで切り替える（登録順を維持）。
$days = [];
foreach ( $lp['schedules'] as $item ) {
    $days[ $item['day'] ][] = $item;
}
?>
<section class="section section-schedule" id="schedule" aria-labelledby="schedule-title">
    <?php ok_lp_heading( 'schedule-title', 'PRICE & SCHEDULE', $s['title'] ); ?>
    <div class="price-card"><div class="price-main"><span><?php echo esc_html( $s['label'] ); ?></span><strong><?php echo esc_html( $s['amount'] ); ?></strong><small><?php echo esc_html( $s['frequency'] ); ?></small></div>
    <ul><?php foreach ( $s['notes'] as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
    <div class="price-options"><?php foreach ( $s['extras'] as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div></div>
    <div class="schedule-wrap" data-schedule-tabs>
        <h3 class="screen-reader-text">クラス一覧</h3>
        <?php /* タブは JavaScript が有効なときだけ表示。無効なときは全曜日のクラスを縦に並べる。 */ ?>
        <div class="schedule-tabs" role="tablist" aria-label="曜日を選択" hidden>
            <?php $i = 0; foreach ( $days as $day => $classes ) : ?>
            <button class="schedule-tab" type="button" role="tab" id="schedule-tab-<?php echo esc_attr( $i ); ?>" aria-controls="schedule-panel-<?php echo esc_attr( $i ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><?php echo esc_html( $day ); ?></button>
            <?php $i++; endforeach; ?>
        </div>
        <?php $i = 0; foreach ( $days as $day => $classes ) : ?>
        <div class="schedule-panel active" id="schedule-panel-<?php echo esc_attr( $i ); ?>" role="tabpanel" aria-labelledby="schedule-tab-<?php echo esc_attr( $i ); ?>">
            <p class="schedule-day-label"><?php echo esc_html( $day ); ?></p>
            <?php foreach ( $classes as $item ) : ?>
            <article class="class-row"><div class="class-time"><?php echo esc_html( $item['time'] ); ?><small><?php echo esc_html( $item['venue'] ); ?></small></div><div><h4><?php echo esc_html( $item['name'] ); ?></h4><p><?php echo esc_html( $item['text'] ); ?></p></div></article>
            <?php endforeach; ?>
        </div>
        <?php $i++; endforeach; ?>
    </div>
    <div class="venue-grid">
        <?php foreach ( $lp['venues'] as $item ) : ?>
        <article class="venue-card"><h3><?php echo esc_html( $item['name'] ); ?><?php if ( $item['note'] ) : ?><small><?php echo esc_html( $item['note'] ); ?></small><?php endif; ?></h3><p><?php echo esc_html( $item['address'] ); ?><br>TEL <?php echo esc_html( $item['phone'] ); ?></p><a href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $item['name'] . ' ' . $item['address'] ) ); ?>" target="_blank" rel="noopener noreferrer">Googleマップで見る →<span class="screen-reader-text">（<?php echo esc_html( $item['name'] ); ?>・新しいタブ）</span></a><?php if ( $item['parking'] ) : ?><span class="parking"><?php echo esc_html( $item['parking'] ); ?></span><?php endif; ?></article>
        <?php endforeach; ?>
    </div>
    <p class="confirm-note"><?php echo esc_html( $s['confirmation'] ); ?></p>
</section>
