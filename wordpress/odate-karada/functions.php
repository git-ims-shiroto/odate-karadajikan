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
    wp_enqueue_style( 'odate-karada', get_theme_file_uri( 'assets/css/main.css' ), [ 'odate-karada-fonts' ], '1.2.1' );
    wp_enqueue_style( 'odate-karada-wordpress', get_theme_file_uri( 'assets/css/wordpress.css' ), [ 'odate-karada' ], '1.2.1' );
    if ( ok_lp_is_landing() ) {
        wp_enqueue_script( 'odate-karada-site-modal', get_theme_file_uri( 'assets/js/site-modal.js' ), [], '1.2.1', [ 'in_footer' => true, 'strategy' => 'defer' ] );
        wp_enqueue_script( 'odate-karada-voice-drawer', get_theme_file_uri( 'assets/js/voice-drawer.js' ), [], '1.2.1', [ 'in_footer' => true, 'strategy' => 'defer' ] );
        wp_enqueue_script( 'odate-karada-schedule-tabs', get_theme_file_uri( 'assets/js/schedule-tabs.js' ), [], '1.2.1', [ 'in_footer' => true, 'strategy' => 'defer' ] );
    }
} );
add_filter( 'document_title_parts', function ( $parts ) {
    if ( ok_lp_is_landing() ) {
        $parts['title'] = ok_lp_content()['general']['site_name'] . '｜大館市の体操教室・骨盤底筋トレーニング';
        unset( $parts['tagline'], $parts['site'] );
    }
    return $parts;
} );
add_action( 'wp_head', function () {
    if ( ok_lp_is_landing() ) {
        echo '<meta name="description" content="' . esc_attr( ok_lp_content()['general']['description'] ) . '">' . "\n";
    }
}, 1 );
