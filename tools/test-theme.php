<?php
/** Run: php tools/test-theme.php /absolute/path/to/wordpress */
if ( PHP_SAPI !== 'cli' || empty( $argv[1] ) ) { exit( 1 ); }
require rtrim( $argv[1], '/\\' ) . '/wp-load.php';
$original = get_option( 'ok_lp_content', null );
function check_theme( $condition, $message ) {
    if ( ! $condition ) { throw new RuntimeException( $message ); }
    echo 'PASS: ' . $message . PHP_EOL;
}
try {
    check_theme( 'odate-karada' === get_option( 'stylesheet' ), 'Theme activation' );
    check_theme( '' === ok_lp_line_url( 'https://lin.ee.evil.test/path' ), 'Reject lookalike LINE host' );
    check_theme( '' === ok_lp_line_url( 'javascript:alert(1)' ), 'Reject script URL' );
    check_theme( '' === ok_lp_line_url( 'http://line.me/path' ), 'Require HTTPS' );
    check_theme( 'https://lin.ee/example' === ok_lp_line_url( 'https://lin.ee/example' ), 'Accept LINE HTTPS URL' );
    $clean = ok_lp_sanitize( [ 'general'=>[ 'site_name'=>'<script>alert(1)</script><b>体操</b>' ], 'extra'=>'not allowed' ] );
    check_theme( '体操' === $clean['general']['site_name'] && ! isset( $clean['extra'] ), 'Strip HTML and unknown keys' );
    check_theme( count( $clean['schedules'] ) === 5, 'Partial submissions retain schedules' );
    $input = ok_lp_defaults();
    $input['hero']['photo']['image_id'] = 999999;
    $input['campaign']['enabled'] = '0';
    $clean = ok_lp_sanitize( $input );
    check_theme( 0 === $clean['hero']['photo']['image_id'], 'Reject missing attachment' );
    check_theme( false === $clean['campaign']['enabled'], 'Campaign toggle sanitizes to boolean' );
    update_option( 'ok_lp_content', $clean );
    ob_start(); ok_lp_campaign(); $html = ob_get_clean();
    check_theme( str_contains( $html, '通常 初回500円' ) && ! str_contains( $html, '無料' ), 'Disabled campaign shows normal price' );
    ob_start(); ok_lp_photo( ['image_id'=>999999,'alt'=>'<img onerror=alert(1)>','image_note'=>''], '1-1' ); $html=ob_get_clean();
    check_theme( str_contains( $html, 'photo-placeholder' ) && ! str_contains( $html, '<img onerror' ), 'Missing image fallback and escaped alt' );
    $clean['general']['site_name'] = '<script>alert(1)</script>';
    update_option( 'ok_lp_content', $clean );
    ob_start(); get_header(); $html = ob_get_clean();
    check_theme( ! str_contains( $html, '<script>alert(1)</script>' ), 'Stored text escapes on output' );
} finally {
    if ( null === $original ) { delete_option( 'ok_lp_content' ); } else { update_option( 'ok_lp_content', $original ); }
}
