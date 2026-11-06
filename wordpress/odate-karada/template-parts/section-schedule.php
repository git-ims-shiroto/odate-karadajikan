<?php defined( 'ABSPATH' ) || exit; $lp = $args['lp']; $s = $lp['pricing']; ?>
<section class="section section-schedule" id="schedule" aria-labelledby="schedule-title">
    <?php ok_lp_heading( 'schedule-title', 'PRICE & SCHEDULE', $s['title'] ); ?>
    <div class="price-card"><div class="price-main"><span><?php echo esc_html( $s['label'] ); ?></span><strong><?php echo esc_html( $s['amount'] ); ?></strong><small><?php echo esc_html( $s['frequency'] ); ?></small></div>
    <ul><?php foreach ( $s['notes'] as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
    <div class="price-options"><?php foreach ( $s['extras'] as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div></div>
    <div class="schedule-wrap"><h3>クラス一覧</h3>
        <?php foreach ( $lp['schedules'] as $item ) : ?>
        <article class="class-row"><div class="class-time"><b><?php echo esc_html( $item['day'] ); ?></b><span><?php echo esc_html( $item['time'] ); ?></span></div><div><h4><?php echo esc_html( $item['name'] ); ?></h4><p class="class-venue"><?php echo esc_html( $item['venue'] ); ?></p><p><?php echo esc_html( $item['text'] ); ?></p></div></article>
        <?php endforeach; ?>
    </div>
    <div class="venue-grid">
        <?php foreach ( $lp['venues'] as $item ) : ?>
        <article class="venue-card"><h3><?php echo esc_html( $item['name'] ); ?></h3><p><?php echo esc_html( $item['note'] ); ?></p><p><?php echo esc_html( $item['address'] ); ?></p><p>施設電話：<?php echo esc_html( $item['phone'] ); ?></p><p><?php echo esc_html( $item['parking'] ); ?></p><a href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $item['name'] . ' ' . $item['address'] ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item['name'] ); ?>の地図を開く<span class="screen-reader-text">（新しいタブ）</span></a></article>
        <?php endforeach; ?>
    </div>
    <p class="confirm-note"><?php echo esc_html( $s['confirmation'] ); ?></p>
</section>
