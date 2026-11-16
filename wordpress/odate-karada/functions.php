<?php
/** Theme bootstrap. */
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/content.php';
require_once get_template_directory() . '/inc/settings.php';
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );
    load_theme_textdomain( 'odate-karada', get_template_directory() . '/languages' );
} );
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'odate-karada-fonts', 'https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&display=swap', [], null );
    wp_enqueue_style( 'odate-karada', get_theme_file_uri( 'assets/css/main.css' ), [ 'odate-karada-fonts' ], '1.3.7' );
    wp_enqueue_style( 'odate-karada-wordpress', get_theme_file_uri( 'assets/css/wordpress.css' ), [ 'odate-karada' ], '1.3.7' );
    if ( ok_lp_is_landing() ) {
        wp_enqueue_script( 'odate-karada-site-modal', get_theme_file_uri( 'assets/js/site-modal.js' ), [], '1.3.7', [ 'in_footer' => true, 'strategy' => 'defer' ] );
        wp_enqueue_script( 'odate-karada-voice-drawer', get_theme_file_uri( 'assets/js/voice-drawer.js' ), [], '1.3.7', [ 'in_footer' => true, 'strategy' => 'defer' ] );
        wp_enqueue_script( 'odate-karada-reveal', get_theme_file_uri( 'assets/js/reveal.js' ), [], '1.3.7', [ 'in_footer' => true, 'strategy' => 'defer' ] );
        wp_enqueue_script( 'odate-karada-schedule-tabs', get_theme_file_uri( 'assets/js/schedule-tabs.js' ), [], '1.3.7', [ 'in_footer' => true, 'strategy' => 'defer' ] );
    }
} );
add_filter( 'document_title_parts', function ( $parts ) {
    if ( ok_lp_is_landing() ) {
        $parts['title'] = ok_lp_content()['general']['site_name'] . '｜大館市の体操教室・骨盤底筋トレーニング';
        unset( $parts['tagline'], $parts['site'] );
    }
    return $parts;
} );
/** SEOプラグイン（Yoast / All in One SEO / Rank Math / SEOPress）が有効なら、重複を避けてテーマ側のメタ情報を出さない。 */
function ok_lp_seo_plugin_active() {
    return defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}
/** 説明文・OGP（LINEやSNSで共有したときの表示）を出力する。 */
add_action( 'wp_head', function () {
    if ( ! ok_lp_is_landing() || ok_lp_seo_plugin_active() ) {
        return;
    }
    $lp          = ok_lp_content();
    $description = $lp['general']['description'];
    $url         = is_front_page() ? home_url( '/' ) : get_permalink();
    $image       = ! empty( $lp['hero']['photo']['image_id'] ) ? wp_get_attachment_image_url( absint( $lp['hero']['photo']['image_id'] ), 'full' ) : '';
    if ( ! $image ) {
        $image = get_theme_file_uri( 'assets/images/younger.jpg' );
    }
    $meta = [
        [ 'name', 'description', $description ],
        [ 'property', 'og:type', 'website' ],
        [ 'property', 'og:locale', 'ja_JP' ],
        [ 'property', 'og:site_name', $lp['general']['site_name'] ],
        [ 'property', 'og:title', wp_get_document_title() ],
        [ 'property', 'og:description', $description ],
        [ 'property', 'og:url', $url ],
        [ 'property', 'og:image', $image ],
        [ 'name', 'twitter:card', 'summary_large_image' ],
    ];
    foreach ( $meta as [ $attr, $key, $value ] ) {
        if ( '' !== (string) $value ) {
            printf( '<meta %s="%s" content="%s">' . "\n", $attr, esc_attr( $key ), esc_attr( $value ) );
        }
    }
}, 1 );
/** WordPress標準のサイトマップ（/wp-sitemap.xml）から、ログイン名がわかるユーザー一覧を外す。 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
    return 'users' === $name ? false : $provider;
}, 10, 2 );
