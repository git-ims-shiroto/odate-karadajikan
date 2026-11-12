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
/** 管理画面で件数を増減できる一覧（[セクション, 項目]）。 */
function ok_lp_dynamic_lists() {
    return [ [ 'voices', 'items' ] ];
}
/** 動的な一覧の1件分の空の雛形（初期値の1件目と同じ項目を持つ）。 */
function ok_lp_list_template( $section, $key ) {
    $first = ok_lp_defaults()[$section][$key][0] ?? [];
    return array_map( function ( $value ) { return is_bool( $value ) ? false : ''; }, $first );
}
/** 1件分を雛形の項目にそろえる（不要な項目は捨て、足りない項目は空にする）。 */
function ok_lp_normalize_item( $item, $template ) {
    return array_replace( $template, array_intersect_key( is_array( $item ) ? $item : [], $template ) );
}
function ok_lp_content() {
    $saved = get_option( 'ok_lp_content', [] );
    $saved = is_array( $saved ) ? $saved : [];
    $data = array_replace_recursive( ok_lp_defaults(), $saved );
    // 動的な一覧は、保存済みの件数をそのまま使う（初期値の件数と混ぜない）。
    foreach ( ok_lp_dynamic_lists() as [ $section, $key ] ) {
        if ( isset( $saved[$section][$key] ) && is_array( $saved[$section][$key] ) ) {
            $template = ok_lp_list_template( $section, $key );
            $data[$section][$key] = array_values( array_map( function ( $item ) use ( $template ) {
                return ok_lp_normalize_item( $item, $template );
            }, $saved[$section][$key] ) );
        }
    }
    return $data;
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
const OK_LP_CONTENT_VERSION = '1.3.3';
/**
 * 1.2.0：参加者の声がサンプルのまま（未編集）なら、実際の感想に置き換え、サンプルの注記を消す。
 * 管理画面で書き換え済みの感想には触れない。
 */
function ok_lp_migrate_voices( $saved ) {
    $items = $saved['voices']['items'] ?? [];
    $first = is_array( $items ) && isset( $items[0]['quote'] ) ? (string) $items[0]['quote'] : '';
    if ( 0 === strpos( $first, '身体を動かすのが久しぶりで不安でしたが' ) ) {
        $saved['voices']['items'] = ok_lp_defaults()['voices']['items'];
    }
    $note = (string) ( $saved['voices']['note'] ?? '' );
    if ( false !== strpos( $note, 'サンプル' ) ) {
        $saved['voices']['note'] = '';
    }
    return $saved;
}
/**
 * 1.3.0 / 1.3.3：教室名とキャッチコピーが旧い初期値のまま（未編集）なら、新しい初期値に置き換える。
 * 1.3.3 で教室名の間の全角スペースを半角スペースに変えたため、1.3.2 の初期値も置き換える。
 */
function ok_lp_migrate_brand( $saved ) {
    $defaults = ok_lp_defaults()['general'];
    $old      = [ 'site_name' => [ '大館からだ時間', '大館　からだの調律時間' ], 'tagline' => [ '骨盤まわりから、のびのび体操' ] ];
    foreach ( $old as $key => $values ) {
        if ( isset( $saved['general'][$key] ) && in_array( $saved['general'][$key], $values, true ) ) {
            $saved['general'][$key] = $defaults[$key];
        }
    }
    return $saved;
}
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
    $version = (string) get_option( 'ok_lp_content_version', '0' );
    if ( OK_LP_CONTENT_VERSION === $version ) {
        return;
    }
    $saved = get_option( 'ok_lp_content', null );
    if ( is_array( $saved ) ) {
        if ( version_compare( $version, '1.1.0', '<' ) ) {
            add_option( 'ok_lp_content_backup_1_0', $saved, '', false );
            $saved = ok_lp_carry_settings( ok_lp_defaults(), $saved );
        }
        if ( version_compare( $version, '1.2.0', '<' ) ) {
            $saved = ok_lp_migrate_voices( $saved );
        }
        if ( version_compare( $version, '1.3.3', '<' ) ) {
            $saved = ok_lp_migrate_brand( $saved );
        }
        update_option( 'ok_lp_content', $saved );
    }
    update_option( 'ok_lp_content_version', OK_LP_CONTENT_VERSION );
} );
