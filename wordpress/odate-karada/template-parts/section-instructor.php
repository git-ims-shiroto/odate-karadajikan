<?php defined( 'ABSPATH' ) || exit; $s = $args['lp']['instructor']; ?>
<section class="section section-instructor" id="instructor" aria-labelledby="instructor-title">
    <div class="instructor-layout"><div class="instructor-photo"><?php ok_lp_photo( $s['photo'], '1-1' ); ?></div>
        <div class="instructor-copy"><p class="section-kicker">INSTRUCTOR</p><h2 id="instructor-title"><?php echo esc_html( $s['title'] ); ?></h2><p class="role"><?php echo esc_html( $s['role'] ); ?></p><p class="instructor-name"><span><?php echo esc_html( $s['name'] ); ?></span><small><?php echo esc_html( $s['reading'] ); ?></small></p><p><?php echo esc_html( $s['text'] ); ?></p>
        <?php if ( $s['note'] ) : ?><p class="draft-note"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?></div>
    </div>
    <details class="qualifications"><summary>主な保有資格を見る</summary><div id="qualificationsList">
        <?php foreach ( $s['qualifications'] as $item ) : ?><div class="qualification-row"><b><?php echo esc_html( $item['org'] ); ?></b><span><?php echo esc_html( $item['items'] ); ?></span></div><?php endforeach; ?>
    </div></details>
</section>
