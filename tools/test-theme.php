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
    check_theme( '' === ok_lp_video_id( '"><script>' ) && '8-LBeJjE_KA' === ok_lp_video_id( '8-LBeJjE_KA' ), 'YouTube ID allows only safe characters' );
    check_theme( '' === ok_lp_https_url( 'javascript:alert(1)' ) && '' === ok_lp_https_url( 'http://example.com/' ), 'Method links require HTTPS' );
    $carried = ok_lp_carry_settings( ok_lp_defaults(), [ 'general'=>[ 'line_url'=>'https://lin.ee/x', 'site_name'=>'旧' ], 'hero'=>[ 'photo'=>[ 'image_id'=>12, 'alt'=>'旧alt', 'image_note'=>'' ], 'title'=>'旧' ], 'campaign'=>[ 'enabled'=>false ] ] );
    check_theme( 'https://lin.ee/x' === $carried['general']['line_url'] && 12 === $carried['hero']['photo']['image_id'] && '旧alt' === $carried['hero']['photo']['alt'] && false === $carried['campaign']['enabled'] && '旧' !== $carried['hero']['title'] && '旧' !== $carried['general']['site_name'], 'Migration keeps images, LINE and campaign toggle; resets copy' );
    $input = ok_lp_defaults();
    $input['hero']['photo']['image_id'] = 999999;
    $input['campaign']['enabled'] = '0';
    $clean = ok_lp_sanitize( $input );
    check_theme( 0 === $clean['hero']['photo']['image_id'], 'Reject missing attachment' );
    check_theme( false === $clean['campaign']['enabled'], 'Campaign toggle sanitizes to boolean' );
    update_option( 'ok_lp_content', $clean );
    ob_start(); ok_lp_campaign(); $html = ob_get_clean();
    check_theme( str_contains( $html, ok_lp_defaults()['campaign']['normal_price'] ) && ! str_contains( $html, '無料' ), 'Disabled campaign shows normal price' );
    ob_start(); ok_lp_photo( ['image_id'=>999999,'alt'=>'<img onerror=alert(1)>','image_note'=>''], '1-1' ); $html=ob_get_clean();
    check_theme( str_contains( $html, 'photo-placeholder' ) && ! str_contains( $html, '<img onerror' ), 'Missing image fallback and escaped alt' );
    $clean['general']['site_name'] = '<script>alert(1)</script>';
    update_option( 'ok_lp_content', $clean );
    ob_start(); get_header(); $html = ob_get_clean();
    check_theme( ! str_contains( $html, '<script>alert(1)</script>' ), 'Stored text escapes on output' );
} finally {
    if ( null === $original ) { delete_option( 'ok_lp_content' ); } else { update_option( 'ok_lp_content', $original ); }
}
