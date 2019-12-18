<?php


/**
 * Tag generator helper scripts
 */
function cf7msm_admin_enqueue_scripts( $hook_suffix ) {
    if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
        return;
    }
    wp_enqueue_script( 'cf7msm-admin-taggenerator',
        cf7msm_url( 'form-tags/js/tag-generator.js' ),
        array( 'jquery' ), CF7MSM_VERSION, true );

    wp_enqueue_style( 'cf7msm-admin',
        cf7msm_url( 'form-tags/css/styles.css' ),
        array( 'contact-form-7-admin' ), CF7MSM_VERSION );

    if ( cf7msm_fs()->is_not_paying() ) {
        wp_enqueue_script( 'cf7msm-admin-checkout',
            'https://checkout.freemius.com/checkout.min.js',
            array( 'jquery' ),
            CF7MSM_VERSION, true );

        wp_enqueue_script( 'cf7msm-admin-panel',
            cf7msm_url( 'resources/cf7msm-admin.min.js' ),
            array( 'jquery', 'cf7msm-admin-checkout' ),
            CF7MSM_VERSION, true );

        wp_enqueue_style( 'cf7msm-admin-panel',
            cf7msm_url( 'resources/cf7msm-admin.css' ),
            array( 'contact-form-7-admin' ), CF7MSM_VERSION );

        add_action( 'wpcf7_admin_footer', 'cf7msm_upgrade_panel' );
    }

}
add_action( 'admin_enqueue_scripts', 'cf7msm_admin_enqueue_scripts' );

/**
 * Show upgrade information
 */
function cf7msm_upgrade_panel() {
?>
    <div id="upgradediv" class="postbox hide">
        <h3><?php echo esc_html( __( 'CF7 Multi-Step Forms', 'contact-form-7' ) ); ?></h3>
        <div class="inside">
            <?php echo cf7msm_kses( __( '<p>Not getting all information from your Multi-Step forms? </p><p>Consider upgrading to allow for longer multi-step forms.</p>' ) ); ?>
            <br>
            <div class="aligncenter">
                <?php printf( cf7msm_kses( 
                __( '<a href="#" class="cf7msm-freemius-purchase">Upgrade Now</a><br><a href="%1$s" target="_blank">Learn more</a><a href="%1$s" class="external dashicons dashicons-external" target="_blank"></a>', 'contact-form-7-multi-step-module' ) ), CF7MSM_LEARN_MORE_URL ); ?>
            </div>
        </div>
    </div><!-- #upgradediv -->
<?php
}
