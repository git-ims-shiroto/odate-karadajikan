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
function ok_lp_heading( $id, $kicker, $title, $lead = '', $class = '' ) {
    echo '<div class="section-heading' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"><p class="section-kicker">' . esc_html( $kicker ) . '</p><h2 id="' . esc_attr( $id ) . '">' . ok_lp_lines( $title ) . '</h2>';
    if ( $lead ) {
        echo '<p>' . ok_lp_lines( $lead ) . '</p>';
    }
    echo '</div>';
}
/** 改行を <br> にして返す（各行はエスケープ済み）。 */
function ok_lp_lines( $text ) {
    $lines = preg_split( '/\r\n|\r|\n/', trim( (string) $text ) );
    return implode( '<br>', array_map( 'esc_html', $lines ) );
}
/** 空行区切りの段落を <p> で出力する。 */
function ok_lp_paragraphs( $text ) {
    foreach ( preg_split( '/(?:\r\n|\r|\n)\s*(?:\r\n|\r|\n)/', trim( (string) $text ) ) as $para ) {
        if ( '' !== trim( $para ) ) {
            echo '<p>' . ok_lp_lines( $para ) . '</p>';
        }
    }
}
/** YouTube の動画ID（英数字・-・_ のみ）。それ以外は空。 */
function ok_lp_video_id( $value ) {
    $value = trim( (string) $value );
    return preg_match( '/^[A-Za-z0-9_-]{6,20}$/', $value ) ? $value : '';
}
/** 外部リンク（https のみ）。 */
function ok_lp_https_url( $value ) {
    $url = esc_url_raw( trim( (string) $value ), [ 'https' ] );
    return $url && 'https' === wp_parse_url( $url, PHP_URL_SCHEME ) ? $url : '';
}
/** 最後の半角スペース以降を強調する（「体験 無料」→「体験 <b>無料</b>」）。 */
function ok_lp_emphasize_last( $text ) {
    $text = trim( (string) $text );
    $pos = strrpos( $text, ' ' ); // 半角スペースは1バイトなのでUTF-8でも安全
    if ( false === $pos ) {
        return '<b>' . esc_html( $text ) . '</b>';
    }
    return esc_html( substr( $text, 0, $pos ) ) . ' <b>' . esc_html( substr( $text, $pos + 1 ) ) . '</b>';
}
/** ファーストビューのキャンペーン（モックの .campaign と同じ構造）。数字部分を強調する。 */
function ok_lp_campaign() {
    $campaign = ok_lp_content()['campaign'];
    if ( ! $campaign['enabled'] ) {
        echo '<p class="trial-standard-price">' . esc_html( $campaign['normal_price'] ) . '</p>';
        return;
    }
    $plain = esc_html( $campaign['offer'] );
    $offer = preg_replace( '/([0-9０-９][0-9０-９,，]*)/u', '<b>$1</b>', $plain );
    if ( $offer === $plain ) {
        $offer = ok_lp_emphasize_last( $campaign['offer'] );
    }
    echo '<div class="campaign"><span>' . esc_html( $campaign['label'] ) . '</span><strong>' . $offer . '</strong><small>';
    if ( $campaign['hero_note'] ) {
        echo '<b>' . esc_html( $campaign['hero_note'] ) . '</b>';
    }
    if ( $campaign['period'] ) {
        echo '<span>' . esc_html( $campaign['period'] ) . '</span>';
    }
    echo '</small></div>';
}
/** 体験カードのキャンペーン（モックの .trial-price と同じ構造）。 */
function ok_lp_trial_price() {
    $campaign = ok_lp_content()['campaign'];
    if ( ! $campaign['enabled'] ) {
        echo '<p class="trial-standard-price">' . esc_html( $campaign['normal_price'] ) . '</p>';
        return;
    }
    $small = implode( '／', array_filter( [ $campaign['normal_price'], $campaign['period'] ] ) );
    echo '<div class="trial-price"><span class="campaign-label"><i aria-hidden="true">🎁</i> ' . esc_html( $campaign['trial_label'] ) . '</span>';
    echo '<strong>' . ok_lp_emphasize_last( $campaign['trial_offer'] ) . '</strong>';
    if ( $small ) {
        echo '<small>' . esc_html( $small ) . '</small>';
    }
    echo '</div>';
}
/**
 * 1.1.0 でモックの文言に合わせたため、保存済みの文言を新しい初期値に置き換える（1回だけ）。
 * 画像の選択（代替テキストを含む）・公式LINE・キャンペーンの表示有無は引き継ぐ。
 * 置き換え前の設定は ok_lp_content_backup_1_0 に保存する。
 */
const OK_LP_CONTENT_VERSION = '1.1.0';
function ok_lp_carry_settings( $defaults, $saved ) {
    if ( ! is_array( $defaults ) || ! is_array( $saved ) ) {
        return $defaults;
    }
    foreach ( $defaults as $key => $value ) {
        if ( ! array_key_exists( $key, $saved ) ) {
            continue;
        }
        if ( is_array( $value ) && array_key_exists( 'image_id', $value ) ) {
            if ( is_array( $saved[$key] ) && ! empty( $saved[$key]['image_id'] ) ) {
                $defaults[$key] = array_replace( $value, array_intersect_key( $saved[$key], $value ) );
            }
        } elseif ( is_array( $value ) ) {
            $defaults[$key] = ok_lp_carry_settings( $value, $saved[$key] );
        } elseif ( 'line_url' === $key || 'enabled' === $key ) {
            $defaults[$key] = $saved[$key];
        }
    }
    return $defaults;
}
add_action( 'init', function () {
    if ( OK_LP_CONTENT_VERSION === get_option( 'ok_lp_content_version' ) ) {
        return;
    }
    $saved = get_option( 'ok_lp_content', null );
    if ( is_array( $saved ) ) {
        add_option( 'ok_lp_content_backup_1_0', $saved, '', false );
        update_option( 'ok_lp_content', ok_lp_carry_settings( ok_lp_defaults(), $saved ) );
    }
    update_option( 'ok_lp_content_version', OK_LP_CONTENT_VERSION );
} );
