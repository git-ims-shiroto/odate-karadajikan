<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['lessons']; ?>
<section class="section section-lessons" id="lessons" aria-labelledby="lessons-title">
    <?php ok_lp_heading( 'lessons-title', 'LESSON', $s['title'] ); ?>
    <div class="reason-grid">
        <?php foreach ( $s['reasons'] as $index => $item ) : ?>
        <article class="reason-card"><span class="reason-number" aria-hidden="true"><?php echo esc_html( $item['number'] ); ?></span><span class="reason-symbol" aria-hidden="true"><?php echo esc_html( [ '貸', '円', '楽' ][$index] ?? '楽' ); ?></span><h3><?php echo esc_html( $item['title'] ); ?></h3><p><?php echo esc_html( $item['text'] ); ?></p></article>
        <?php endforeach; ?>
    </div>
    <div class="lesson-intro"><h3><?php echo esc_html( $s['subtitle'] ); ?></h3><p><?php echo esc_html( $s['lead'] ); ?></p></div>
    <div class="method-grid">
        <?php foreach ( $s['methods'] as $item ) : ?>
        <article class="method-card"><?php ok_lp_photo( $item['photo'] ); ?><div><h4><?php echo esc_html( $item['name'] ); ?></h4><p><?php echo esc_html( $item['text'] ); ?></p></div></article>
        <?php endforeach; ?>
    </div>
    <div class="saturday-box"><p>★ 土曜日のクラス ★</p><div id="saturdayMethods">
        <?php foreach ( $s['saturday'] as $item ) : ?><div class="saturday-method"><b><?php echo esc_html( $item['name'] ); ?></b><span><?php echo esc_html( $item['text'] ); ?></span></div><?php endforeach; ?>
    </div><small><?php echo esc_html( $s['note'] ); ?></small></div>
</section>
