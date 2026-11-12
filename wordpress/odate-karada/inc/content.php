<?php
/** Content and presentation helpers. */
defined( 'ABSPATH' ) || exit;
function ok_lp_defaults() {
    static $defaults;
    if ( null === $defaults ) {
        $defaults = require __DIR__ . '/default-content.php';
    }
    return $defaults;
}
function ok_lp_content() {
    $saved = get_option( 'ok_lp_content', [] );
    return array_replace_recursive( ok_lp_defaults(), is_array( $saved ) ? $saved : [] );
}
function ok_lp_is_landing() {
    return is_front_page() || is_page_template( 'page-lp.php' );
}
function ok_lp_anchor( $anchor ) {
    return ok_lp_is_landing() ? '#' . $anchor : home_url( '/#' . $anchor );
}
function ok_lp_line_url( $value ) {
    $url = esc_url_raw( is_string( $value ) ? $value : '', [ 'https' ] );
    $host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
    return in_array( $host, [ 'line.me', 'lin.ee' ], true ) && 'https' === wp_parse_url( $url, PHP_URL_SCHEME ) ? $url : '';
}
function ok_lp_cta() {
    $data = ok_lp_content();
    $url = ok_lp_line_url( $data['general']['line_url'] );
    if ( $url ) {
        echo '<a class="button button-line" href="' . esc_url( $url ) . '"><span aria-hidden="true">●</span> LINEで体験を申し込む</a>';
    } else {
        echo '<a class="button button-line" href="' . esc_url( ok_lp_anchor( 'line-status' ) ) . '"><span aria-hidden="true">●</span> LINEで体験を申し込む<span class="screen-reader-text">（準備中）</span></a>';
    }
}
function ok_lp_photo( $photo, $ratio = '4-3', $size = 'large' ) {
    $image = ! empty( $photo['image_id'] ) ? wp_get_attachment_image( absint( $photo['image_id'] ), $size, false, [ 'alt' => $photo['alt'] ] ) : '';
    echo '<figure class="lp-photo ratio-' . esc_attr( $ratio ) . '">';
    if ( $image ) {
        echo $image; // WordPress constructs and escapes attachment markup.
        if ( $photo['image_note'] ) {
            echo '<figcaption class="image-note">' . esc_html( $photo['image_note'] ) . '</figcaption>';
        }
    } else {
        echo '<div class="photo-placeholder" role="img" aria-label="' . esc_attr( $photo['alt'] ) . '"><span>写真：' . esc_html( $photo['alt'] ) . '</span></div>';
    }
    echo '</figure>';
}
function ok_lp_heading( $id, $kicker, $title, $lead = '' ) {
    echo '<div class="section-heading"><p class="section-kicker">' . esc_html( $kicker ) . '</p><h2 id="' . esc_attr( $id ) . '">' . esc_html( $title ) . '</h2>';
    if ( $lead ) {
        echo '<p>' . esc_html( $lead ) . '</p>';
    }
    echo '</div>';
}
function ok_lp_campaign() {
    $campaign = ok_lp_content()['campaign'];
    if ( $campaign['enabled'] ) {
        echo '<div class="campaign"><span>' . esc_html( $campaign['label'] ) . '</span><strong>' . esc_html( $campaign['offer'] ) . '</strong><small>' . esc_html( $campaign['normal_price'] ) . '<br>' . esc_html( $campaign['period'] ) . '</small></div>';
    } else {
        echo '<p class="trial-standard-price">' . esc_html( $campaign['normal_price'] ) . '</p>';
    }
}
/** 体験カード用のキャンペーン表示（モックの trial-price と同じ構造）。オファーの最後の語を大きく表示する。 */
function ok_lp_trial_price() {
    $campaign = ok_lp_content()['campaign'];
    if ( ! $campaign['enabled'] ) {
        echo '<p class="trial-standard-price">' . esc_html( $campaign['normal_price'] ) . '</p>';
        return;
    }
    $offer = trim( (string) $campaign['offer'] );
    $pos = strrpos( $offer, ' ' ); // 半角スペースは1バイトなのでUTF-8でも安全
    $lead = false === $pos ? '' : substr( $offer, 0, $pos );
    $main = false === $pos ? $offer : substr( $offer, $pos + 1 );
    $small = implode( '／', array_filter( [ $campaign['normal_price'], $campaign['period'] ] ) );
    echo '<div class="trial-price"><span class="campaign-label"><i aria-hidden="true">🎁</i> ' . esc_html( $campaign['label'] ) . '</span>';
    echo '<strong>' . ( '' !== $lead ? esc_html( $lead ) . ' ' : '' ) . '<b>' . esc_html( $main ) . '</b></strong>';
    if ( $small ) {
        echo '<small>' . esc_html( $small ) . '</small>';
    }
    echo '</div>';
}
