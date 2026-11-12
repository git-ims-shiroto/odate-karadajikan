<?php
/** A bounded settings form, using the WordPress Settings API. */
defined( 'ABSPATH' ) || exit;
function ok_lp_labels() {
    return [
        'general'=>'基本情報・LINE', 'hero'=>'1. ファーストビュー', 'campaign'=>'体験キャンペーン', 'for_you'=>'2. こんな方へ', 'lessons'=>'3. 選ばれる理由・レッスン内容', 'younger'=>'4. 幅広い世代の方へ（40〜50代・産後の方を含む）', 'voices'=>'5. 参加者の声', 'instructor'=>'6. 講師紹介', 'pricing'=>'7. 料金', 'schedules'=>'7. クラス日程（5クラス）', 'venues'=>'7. 会場（3会場）', 'faq'=>'8. よくある質問', 'trial'=>'8. 体験申込み', 'footer'=>'フッター',
        'site_name'=>'教室名', 'hero_note'=>'ファーストビューの補足（例：見学だけでもOK）', 'trial_label'=>'体験欄のラベル', 'trial_offer'=>'体験欄のキャンペーン内容（最後の語を大きく表示）', 'link_label'=>'リンクの文言', 'link_url'=>'リンク先URL（https://）', 'video_id'=>'YouTube動画ID（URLの v= 以降の11文字）', 'kicker'=>'見出しの上の英字', 'unit'=>'単位', 'addon_label'=>'追加料金の説明', 'addon_amount'=>'追加料金', 'total_label'=>'合計の表示ラベル', 'total_amount'=>'合計金額', 'tagline'=>'教室名に添える説明', 'description'=>'検索結果用の説明', 'line_url'=>'公式LINE URL（https://lin.ee/ または https://line.me/）', 'line_qr'=>'公式LINEのQRコード', 'review_note'=>'確認中の案内（公開内容の確定後に空欄にする）', 'title'=>'見出し', 'eyebrow'=>'見出しの上の一言', 'accent'=>'キャッチコピー', 'lead'=>'導入文', 'photo'=>'写真', 'note'=>'補足・確認事項', 'enabled'=>'無料キャンペーンを表示する', 'label'=>'表示ラベル', 'offer'=>'キャンペーン内容', 'period'=>'キャンペーン期間（終了後は表示をオフにする）', 'normal_price'=>'通常の体験料金', 'items'=>'内容一覧', 'icon'=>'アイコン文字', 'subtitle'=>'小見出し', 'text'=>'説明文', 'checks'=>'案内項目', 'reasons'=>'選ばれる理由', 'number'=>'番号', 'methods'=>'レッスン内容', 'saturday'=>'土曜日の道具', 'name'=>'名称・氏名', 'quote'=>'参加者の声', 'age'=>'年代・イニシャル', 'tag'=>'ひとこと', 'disclaimer'=>'感想の注意書き', 'reading'=>'ふりがな', 'role'=>'肩書き', 'qualifications'=>'保有資格', 'org'=>'資格認定団体', 'amount'=>'月会費', 'frequency'=>'回数', 'notes'=>'補足一覧', 'extras'=>'追加料金', 'confirmation'=>'確認中の情報', 'day'=>'曜日', 'time'=>'時間', 'venue'=>'会場', 'address'=>'住所', 'phone'=>'施設の電話番号（携帯番号は掲載しない）', 'parking'=>'駐車場', 'q'=>'質問', 'a'=>'回答', 'pending'=>'LINE準備中の案内', 'image_id'=>'画像（メディアライブラリ）', 'alt'=>'画像の代替テキスト', 'image_note'=>'画像に添える注記（AI画像は「写真はイメージです」）',
    ];
}
function ok_lp_sanitize_tree( $input, $defaults, $key = '' ) {
    if ( is_array( $defaults ) ) {
        $result = [];
        foreach ( $defaults as $child => $default ) {
            $result[$child] = ok_lp_sanitize_tree( is_array( $input ) && array_key_exists( $child, $input ) ? $input[$child] : $default, $default, $child );
        }
        return $result;
    }
    if ( is_bool( $defaults ) ) {
        return in_array( $input, [ true, 1, '1' ], true );
    }
    if ( 'image_id' === $key ) {
        $id = is_scalar( $input ) ? absint( $input ) : 0;
        return $id && wp_attachment_is_image( $id ) ? $id : 0;
    }
    if ( 'video_id' === $key ) {
        return ok_lp_video_id( is_scalar( $input ) ? $input : '' );
    }
    if ( 'link_url' === $key ) {
        return ok_lp_https_url( is_scalar( $input ) ? $input : '' );
    }
    if ( 'line_url' === $key ) {
        $url = ok_lp_line_url( $input );
        if ( ! empty( $input ) && ! $url ) {
            add_settings_error( 'ok_lp_content', 'invalid_line', 'LINE URLは https://lin.ee/ または https://line.me/ のURLを入力してください。入力内容は保存されませんでした。' );
            return ok_lp_content()['general']['line_url'];
        }
        return $url;
    }
    return is_scalar( $input ) ? sanitize_textarea_field( (string) $input ) : $defaults;
}
function ok_lp_sanitize( $input ) {
    // Retain existing values on partial submissions; reject unknown keys.
    $result = ok_lp_sanitize_tree( $input, ok_lp_content() );
    // 動的な一覧：そのセクションが送信されたときは、送信された件数・順番をそのまま使う（0件も可）。
    foreach ( ok_lp_dynamic_lists() as [ $section, $key ] ) {
        if ( ! is_array( $input ) || ! isset( $input[$section] ) || ! is_array( $input[$section] ) ) {
            continue;
        }
        $template = ok_lp_list_template( $section, $key );
        $list = [];
        foreach ( array_slice( (array) ( $input[$section][$key] ?? [] ), 0, 50 ) as $item ) {
            $clean = ok_lp_sanitize_tree( ok_lp_normalize_item( $item, $template ), $template );
            if ( '' !== trim( implode( '', array_filter( $clean, 'is_string' ) ) ) ) {
                $list[] = $clean;
            }
        }
        $result[$section][$key] = $list;
    }
    return $result;
}
/** 動的な一覧の1件分（見出し・並べ替え・削除ボタン付き）。 */
function ok_lp_repeat_item( $item, $path, $index, $number ) {
    echo '<div class="ok-repeat-item"><div class="ok-repeat-head"><strong class="ok-repeat-title">' . esc_html( $number ) . '件目</strong>';
    echo '<span class="ok-repeat-actions"><button type="button" class="button ok-move-up" aria-label="上へ移動">↑</button><button type="button" class="button ok-move-down" aria-label="下へ移動">↓</button><button type="button" class="button button-link-delete ok-remove-item">削除</button></span></div>';
    ok_lp_setting_fields( $item, $path . '[' . $index . ']' );
    echo '</div>';
}
/** 件数を増減できる一覧の入力欄。 */
function ok_lp_repeater( $values, $path, $section, $key ) {
    echo '<fieldset class="ok-group ok-repeater"><legend>' . esc_html( 'voices' === $section ? '参加者の声（先頭3件を表示し、4件目以降は「もっと見る」で表示）' : '一覧' ) . '</legend><div class="ok-repeat-list">';
    foreach ( array_values( $values ) as $i => $item ) {
        ok_lp_repeat_item( $item, $path, $i, $i + 1 );
    }
    echo '</div><template class="ok-repeat-template">';
    ok_lp_repeat_item( ok_lp_list_template( $section, $key ), $path, '__INDEX__', '' );
    echo '</template><p><button type="button" class="button button-secondary ok-add-item">＋ 1件追加</button></p>';
    echo '<p class="description">↑↓で表示順を変えられます。追加・削除・並べ替えは「LP設定を保存」で反映されます。</p></fieldset>';
}
add_action( 'admin_init', function () {
    register_setting( 'ok_lp_settings', 'ok_lp_content', [ 'type'=>'array', 'sanitize_callback'=>'ok_lp_sanitize', 'default'=>ok_lp_defaults() ] );
} );
add_action( 'admin_menu', function () {
    add_theme_page( '教室LP設定', '教室LP設定', 'manage_options', 'ok-lp', 'ok_lp_settings_page' );
} );
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( 'appearance_page_ok-lp' !== $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'ok-lp-admin', get_theme_file_uri( 'assets/js/admin.js' ), [ 'media-views' ], '1.2.0', true );
    wp_enqueue_style( 'ok-lp-admin', get_theme_file_uri( 'assets/css/admin.css' ), [], '1.2.0' );
} );
function ok_lp_setting_fields( $values, $path ) {
    $labels = ok_lp_labels();
    foreach ( $values as $key => $value ) {
        $name = $path . '[' . $key . ']';
        $id = 'ok-' . preg_replace( '/[^a-zA-Z0-9_-]/', '-', $name );
        $label = is_int( $key ) ? ( $key + 1 ) . '件目' : ( $labels[$key] ?? $key );
        if ( is_array( $value ) ) {
            foreach ( ok_lp_dynamic_lists() as [ $section, $list_key ] ) {
                if ( 'ok_lp_content[' . $section . '][' . $list_key . ']' === $name ) {
                    ok_lp_repeater( $value, $name, $section, $list_key );
                    continue 2;
                }
            }
            echo '<fieldset class="ok-group"><legend>' . esc_html( $label ) . '</legend>';
            ok_lp_setting_fields( $value, $name );
            echo '</fieldset>';
            continue;
        }
        echo '<div class="ok-field"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
        if ( is_bool( $value ) ) {
            echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input id="' . esc_attr( $id ) . '" type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . checked( $value, true, false ) . '>';
        } elseif ( 'image_id' === $key ) {
            echo '<div class="ok-media"><input id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" type="number" min="0" value="' . esc_attr( $value ) . '"><button type="button" class="button ok-select-image">画像を選択</button><button type="button" class="button ok-remove-image">解除</button><span class="ok-media-status" aria-live="polite">' . esc_html( $value ? '画像を設定済み' : '未設定（仮枠を表示）' ) . '</span></div>';
        } elseif ( 'line_url' === $key ) {
            echo '<input id="' . esc_attr( $id ) . '" class="large-text" type="url" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
        } else {
            echo '<textarea id="' . esc_attr( $id ) . '" class="large-text" rows="2" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
        }
        echo '</div>';
    }
}
function ok_lp_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    echo '<div class="wrap ok-settings"><h1>教室LP設定</h1><p>料金・日程・文言・画像をここで更新できます。文章にHTMLは使用できません。写真の代替テキストも入力してください。</p><p>初期状態には未確認情報・サンプル文章が含まれます。確認済みの内容に差し替えてから公開してください。キャンペーンは自動終了しません。</p>';
    settings_errors( 'ok_lp_content' );
    echo '<p><a class="button" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener">ホームページを確認</a></p><form action="options.php" method="post">';
    settings_fields( 'ok_lp_settings' );
    $labels = ok_lp_labels();
    foreach ( ok_lp_content() as $key => $values ) {
        echo '<details class="ok-section"><summary>' . esc_html( $labels[$key] ?? $key ) . '</summary>';
        ok_lp_setting_fields( $values, 'ok_lp_content[' . $key . ']' );
        echo '</details>';
    }
    submit_button( 'LP設定を保存' );
    echo '</form></div>';
}
