<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['lessons'];
// モックの renderResource と同じ：動画IDがあれば埋め込み、リンクがあればポップアップで外部サイトを表示する。
$resource = function ( $item ) {
    $video = ok_lp_video_id( $item['video_id'] ?? '' );
    if ( $video ) {
        echo '<div class="video-embed"><iframe src="' . esc_url( 'https://www.youtube-nocookie.com/embed/' . $video ) . '" title="' . esc_attr( $item['name'] . 'の紹介動画' ) . '" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>';
        return;
    }
    $url = ok_lp_https_url( $item['link_url'] ?? '' );
    if ( $url && ! empty( $item['link_label'] ) ) {
        // JS が無効なときは新しいタブで開く。有効なときはポップアップで表示する（assets/js/site-modal.js）。
        echo '<a class="method-link method-popup" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" data-site-modal data-site-title="' . esc_attr( $item['link_label'] ) . '">' . esc_html( $item['link_label'] ) . ' →</a>';
    }
};
?>
<section class="section section-lessons" id="lessons" aria-labelledby="lessons-title">
    <?php ok_lp_heading( 'lessons-title', 'LESSON', $s['title'] ); ?>
    <div class="reason-grid">
        <?php foreach ( $s['reasons'] as $index => $item ) : ?>
        <article class="reason-card"><span class="reason-number" aria-hidden="true"><?php echo esc_html( $item['number'] ); ?></span><span class="reason-symbol" aria-hidden="true"><?php echo esc_html( [ '貸', '円', '楽' ][$index] ?? '楽' ); ?></span><h3><?php echo esc_html( $item['title'] ); ?></h3><p><?php echo esc_html( $item['text'] ); ?></p></article>
        <?php endforeach; ?>
    </div>
    <div class="lesson-intro"><div><?php if ( $s['label'] ) : ?><p class="mini-label"><?php echo esc_html( $s['label'] ); ?></p><?php endif; ?><h3><?php echo ok_lp_lines( $s['subtitle'] ); ?></h3></div><p><?php echo esc_html( $s['lead'] ); ?></p></div>
    <div class="method-grid">
        <?php foreach ( $s['methods'] as $item ) : ?>
        <article class="method-card"><?php ok_lp_photo( $item['photo'] ); ?><div><h4><?php echo esc_html( $item['name'] ); ?></h4><p><?php echo esc_html( $item['text'] ); ?></p><?php $resource( $item ); ?></div></article>
        <?php endforeach; ?>
    </div>
    <div class="saturday-box"><p><span>★</span> 土曜日のクラス <span>★</span></p><div id="saturdayMethods">
        <?php foreach ( $s['saturday'] as $item ) : ?><div class="saturday-method"><b><?php echo esc_html( $item['name'] ); ?></b><span><?php echo esc_html( $item['text'] ); ?></span><?php $resource( $item ); ?></div><?php endforeach; ?>
    </div><small><?php echo esc_html( $s['note'] ); ?></small></div>
</section>
