<?php defined( 'ABSPATH' ) || exit; $lp = ok_lp_content(); ?>
<footer class="site-footer">
    <div class="brand footer-brand"><span class="brand-mark"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.png' ) ); ?>" alt="ひめトレ®" width="160" height="160" loading="lazy"></span><span><b><?php echo esc_html( $lp['general']['site_name'] ); ?></b><small><?php echo esc_html( $lp['general']['tagline'] ); ?></small></span></div>
    <p><?php foreach ( preg_split( '/\r\n|\r|\n/', trim( $lp['footer']['text'] ) ) as $line ) : ?><span class="footer-line"><?php echo esc_html( $line ); ?></span><?php endforeach; ?></p>
    <small>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $lp['general']['site_name'] ); ?></small>
</footer>
<div class="mobile-cta"><?php ok_lp_cta(); ?></div>
<?php if ( ok_lp_is_landing() ) : ?>
<dialog class="site-modal" id="siteModal" aria-labelledby="siteModalTitle">
    <div class="site-modal-panel">
        <header class="site-modal-header">
            <strong id="siteModalTitle">公式サイト</strong>
            <div class="site-modal-actions">
                <a id="siteModalExternal" href="#" target="_blank" rel="noopener noreferrer">新しいタブで開く</a>
                <button id="siteModalClose" type="button" aria-label="ポップアップを閉じる">×</button>
            </div>
        </header>
        <iframe id="siteModalFrame" title="公式サイト" src="about:blank" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>
        <p class="site-modal-note">表示されない場合は「新しいタブで開く」をご利用ください。</p>
    </div>
</dialog>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
