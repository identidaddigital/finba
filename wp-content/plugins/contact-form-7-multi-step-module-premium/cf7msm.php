<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load text domain for translations
 */
add_action( 'init', 'cf7msm_load_textdomain' );
function cf7msm_load_textdomain() {
    load_plugin_textdomain( 'contact-form-7-multi-step-module', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Print a warning if cf7 not installed or activated.
 */
function contact_form_7_form_codes() {
    global $pagenow;
    if ( $pagenow != 'plugins.php' )
        return;

    if ( defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, CF7MSM_MIN_CF7_VERSION ) >= 0 ) {
        return;
    }
    add_action('admin_notices', 'cfformfieldserror');
    wp_enqueue_script('thickbox');
    function cfformfieldserror() {
        $out = '<div class="error" id="messages"><p>';
        if ( defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, CF7MSM_MIN_CF7_VERSION ) < 0 ) {
            $out .= sprintf( __( 'Please update the Contact Form 7 plugin.  Contact Form 7 Multi-Step Form plugin requires Contact Form 7 version %s or above.', 'contact-form-7-multi-step-module' ), CF7MSM_MIN_CF7_VERSION );
        }
        else {
            if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
                $out .= __( 'The Contact Form 7 plugin is installed, but <strong>you must activate Contact Form 7</strong> below for the Contact Form 7 Multi-Step Form to work.', 'contact-form-7-multi-step-module' );
            } else {
                $out .= sprintf( __( 'The Contact Form 7 plugin must be installed for the Contact Form 7 Multi-Step Form to work. <a href="%s" class="thickbox" title="Contact Form 7">Install Now.</a>', 'contact-form-7-multi-step-module' ), admin_url( 'plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550' ) );
            }

        }
        $out .= '</p></div>';
        echo cf7msm_kses( $out );
    }
}
add_action('plugins_loaded', 'contact_form_7_form_codes', 10);

/**
 * Allow a set of default html tags
 */
function cf7msm_kses( $string, $additional_html = array() ) {
    $allowed_html = array_merge( array(
        'a' => array(
            'href'   => array(),
            'title'  => array(),
            'target' => array(),
            'class'  => array()
        ),
        'div' => array(
            'class' => array(),
            'id'    => array(),
        ),
        'button' => array(
            'class' => array(),
            'id'    => array(),
        ),
        'p' => array(),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
    ), $additional_html );
    return wp_kses( $string, $allowed_html );
}

/**
 * Return the url with the plugin url prepended.
 */
function cf7msm_url( $path ) {
    return plugins_url( $path, CF7MSM_PLUGIN );
}

/**
 * init_sessions()
 *
 * @uses session_id()
 * @uses session_start()
 */
function cf7msm_init_sessions() {
    //try to set cookie
    if ( empty( $_COOKIE['cf7msm_check'] ) ) {
        $force_session = apply_filters('cf7msm_force_session', false);
        if ( !$force_session ) {
            setcookie('cf7msm_check', 1, 0, COOKIEPATH, COOKIE_DOMAIN);
        }

        if (!session_id()) {
            session_start();
        }
    }
}
add_action('init', 'cf7msm_init_sessions');


/**
 * Add scripts to be able to go back to a previous step.
 */
function cf7msm_scripts() {
    $step = cf7msm_get('step');
    wp_enqueue_script('cf7msm',
        plugins_url('/resources/cf7msm.min.js', CF7MSM_PLUGIN),
        array('jquery'),
        CF7MSM_VERSION,
        true );
    wp_enqueue_style('cf7msm_styles',
        plugins_url('/resources/cf7msm.css', CF7MSM_PLUGIN),
        array(),
        CF7MSM_VERSION
        );

    $cf7msm_posted_data = cf7msm_get('cf7msm_posted_data');
    if ( empty( $cf7msm_posted_data ) ) {
        $cf7msm_posted_data = array();
    }
    $cf7msm_posted_data['cf7msm_prev_urls'] = cf7msm_get('cf7msm_prev_urls');
    wp_localize_script( 'cf7msm', 'cf7msm_posted_data', $cf7msm_posted_data);

    if ( cf7msm_fs()->is__premium_only() ) {
        //let js know "Contact Form 7 Conditional Fields" plugin is active.
        if ( defined( 'WPCF7CF_PLUGIN' ) ) {
            wp_localize_script( 'cf7msm', 'cf7msm_wpcf7cf', array(1) );   
        }
    }
}
add_action('wp_enqueue_scripts', 'cf7msm_scripts');

/**
 *  Saves a variable to cookies or if not enabled, to session.
 */
function cf7msm_set($var_name, $var_value) {
    if ( empty( $_COOKIE['cf7msm_check'] ) ) {
        $_SESSION[$var_name] = $var_value;
    }
    else {
        $json_encoded = '';
        //for php < 5.4
        if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
            $json_encoded = json_encode( $var_value, JSON_UNESCAPED_UNICODE );
        }
        else {
            $json_encoded = json_encode( $var_value );
        }
        setcookie($var_name, $json_encoded, 0, COOKIEPATH, COOKIE_DOMAIN);
    }
}

/**
 *  Get a variable from cookies or if not enabled, from session.
 */
function cf7msm_get($var_name, $default = '') {
    $ret = $default;
    if ( empty( $_COOKIE['cf7msm_check'] ) ) {
        $ret = isset( $_SESSION[$var_name] ) ? $_SESSION[$var_name] : $default;
    }
    else {
        $ret = isset( $_COOKIE[$var_name] ) ? $_COOKIE[$var_name] : $default;
        if (get_magic_quotes_gpc() || function_exists('wp_magic_quotes')) {
            $ret = stripslashes($ret);
        }
        $ret = json_decode( $ret, true );
    }
    return $ret;
}

/**
 * Remove a saved variable.
 */
function cf7msm_remove($var_name) {
    $ret = '';
    if ( empty( $_COOKIE['cf7msm_check'] ) ) {
        if ( isset( $_SESSION[$var_name] ) ) {
            unset( $_SESSION[$var_name] );
        }
    }
    else {
        if ( isset( $_COOKIE[$var_name] ) ) {
            setcookie($var_name, '', 1, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
}


/**
 * Hide the second step of a form.  looks at hidden field 'step'.
 * Always show if the form is the first step.
 * If it's not the first step, make sure it's the next form in the steps.
 */
function cf7msm_step_2($cf7) {
    $has_wpcf7_class = class_exists( 'WPCF7_ContactForm' ) && method_exists($cf7, 'prop');
    $form_id  = '';
    if ( $has_wpcf7_class ) {
        $formstring = $cf7->prop('form');
        $form_id = $cf7->id();
    }
    else {
        $formstring = $cf7->form;
        $form_id = $cf7->id;
    }

    //check if form has a step field
    if (!is_admin() &&
        ( preg_match('/\[multistep "(\d+)-(\d+)-?(.*)"\]/', $formstring, $matches) || preg_match('/\[hidden cf7msm-step "(\d+)-(\d+)"\]/', $formstring, $matches) ) ) {
        $step = cf7msm_get('cf7msm-step');
        if ( !isset($matches[1])
            || ($matches[1] != 1 && empty($step) )
            || ($matches[1] != 1 && ((int) $step) + 1 < $matches[1]) ) {
            if ( $has_wpcf7_class ) {
                $cf7->set_properties( array(
                    'form' => apply_filters('wh_hide_cf7_step_message', esc_html( __( 'Please fill out the form on the previous page', 'contact-form-7-multi-step-module' ) ), $form_id )
                ) );
            }
            else {
                $cf7->form = apply_filters('wh_hide_cf7_step_message', esc_html( __( 'Please fill out the form on the previous page', 'contact-form-7-multi-step-module' ) ), $form_id );
            }
        }
    }

    return $cf7;
}
add_action('wpcf7_contact_form', 'cf7msm_step_2');

/**
 * Handle a multi-step cf7 form for cf7 3.9+
 */
function cf7msm_store_data_steps_filter( $cf7_posted_data ) {
    if (isset($cf7_posted_data['cf7msm-step'])) {
        if (preg_match('/(\d+)-(\d+)/', $cf7_posted_data['cf7msm-step'], $matches)) {
            $curr_step = $matches[1];
            $last_step = $matches[2];
        }

        //for back button
        $prev_urls = cf7msm_get('cf7msm_prev_urls');
        if ( empty($prev_urls) ) {
            $prev_urls = array();
        }
        //example:
        // on step 1,
        //    prev url {"2-3":"page-2"} will be set.
        //    back button is pressed, key "1-3" is looked up and returns undefined
        // on step 2,
        //    prev url {"3-3":"page-2"} will be set.
        //    back button is pressed, key "2-3" is looked up and returns "page-1"
        // on step 3,
        //    prev url {"4-3":"page-3"} will be set. - not used
        //    back button is pressed, key "3-3" is looked up and returns "page-2"
        // step
        $prev_urls[ ($curr_step+1) . '-' . $last_step] = cf7msm_current_url();
        cf7msm_set('cf7msm_prev_urls', $prev_urls);
        $use_cookies = true;
        if ( cf7msm_fs()->is__premium_only() ) {
            $use_cookies = false;
        }
        if ( !empty( $cf7_posted_data['cf7msm-no-ss'] ) || $use_cookies ) {

            $prev_data = cf7msm_get('cf7msm_posted_data', '' );
            if (!is_array($prev_data)) {
                $prev_data = array();
            }
            //remove empty [form] tags from posted_data so $prev_data can be stored.
            $fes = wpcf7_scan_form_tags();
            foreach ( $fes as $fe ) {
                if ( empty( $fe['name'] ) || ( $fe['type'] != 'form' && $fe['type'] != 'multiform' ) )
                    continue;
                unset($cf7_posted_data[$fe['name']]);
            }
            $free_text_keys = array();
            foreach ( $prev_data as $key => $value ) {
                if ( strpos( $key, CF7MSM_FREE_TEXT_PREFIX_RADIO ) === 0 ) {
                    $free_text_keys[$key] = str_replace(CF7MSM_FREE_TEXT_PREFIX_RADIO, '', $key);
                }
                else if ( strpos( $key, CF7MSM_FREE_TEXT_PREFIX_CHECKBOX ) === 0 ) {
                    $free_text_keys[$key] = str_replace( CF7MSM_FREE_TEXT_PREFIX_CHECKBOX, '', $key );
                }
            }
            //if original key is set and not free text, remove free text to reflect posted data.
            foreach( $free_text_keys as $free_text_key => $original_key ) {
                if ( isset( $cf7_posted_data[$original_key] ) && !isset( $cf7_posted_data[$free_text_key] ) ) {
                    unset( $prev_data[$free_text_key] );
                }
            }
            if ($curr_step != $last_step) {
                $posted_data = array_merge($prev_data, $cf7_posted_data);
                cf7msm_set('cf7msm_posted_data', $posted_data );
            }
            else {
                $cf7_posted_data = array_merge($prev_data, $cf7_posted_data);
            }
        }
    }
    return $cf7_posted_data;
}
add_filter( 'wpcf7_posted_data', 'cf7msm_store_data_steps_filter', 9 );

/**
 * Skip sending the mail if this is a multi step form and not the last step.
 */
function cf7msm_skip_send_mail( $skip_mail, $wpcf7 ) {
    $step_string = parse_form_for_multistep( $wpcf7, true );
    if ( !empty( $step_string ) ) {
        $steps = explode( '-', $step_string );
        $curr_step = $steps[0];
        $last_step = $steps[1];
        if ($curr_step != $last_step) {
            $skip_mail = true;
        }
    }
    return $skip_mail;
}
add_filter( 'wpcf7_skip_mail', 'cf7msm_skip_send_mail', 10, 2 );

/**
 * Sets the current step if valid.
 */
function cf7msm_set_step( $result, $tags ) {
    if ( !empty( $_POST['cf7msm-step'] ) ) {
        $step = $_POST['cf7msm-step'];
        if (preg_match('/(\d+)-(\d+)/', $step, $matches)) {
            $curr_step = $matches[1];
            $last_step = $matches[2];
            if ( $result->is_valid() ) {
                if ($curr_step != $last_step) {
                    cf7msm_set('cf7msm-step', $curr_step);
                }
            }
            else {
                $stored_step = cf7msm_get('cf7msm-step');
                if ( $stored_step >= $curr_step ) {
                    //reduce it so user cannot move onto next step.
                    cf7msm_set('cf7msm-step', intval( $curr_step ) - 1);
                }
            }
        }
    }
    return $result;
}
add_filter( 'wpcf7_validate', 'cf7msm_set_step', 99, 2 );

/**
 * Clean things up after mail has been sent.
 */
function cf7msm_mail_sent() {
    if ( isset( $_POST['cf7msm-step'])) {
        $step = $_POST['cf7msm-step'];
        if (preg_match('/(\d+)-(\d+)/', $step, $matches)) {
            $curr_step = $matches[1];
            $last_step = $matches[2];
        }
        if ( $curr_step == $last_step ) {
            cf7msm_remove('cf7msm-step');
            cf7msm_remove('cf7msm_posted_data');
            cf7msm_remove('cf7msm_prev_urls');
        }
        else {
            $wpcf7 = WPCF7_ContactForm::get_current();
            $formstring = $wpcf7->prop('form');
            // redirect when ajax is disabled and not doing rest and not doing ajax
            if ( !wpcf7_load_js() || ( 
                !( defined( 'REST_REQUEST' ) && REST_REQUEST )
                && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) {
                //get url from saved form, not $_POST.  be safe.
                $redirect_url = parse_form_for_multistep( $wpcf7 );
                if ( empty( $redirect_url ) ) {
                    // if using old additional_settings way
                    $subject = $wpcf7->prop('additional_settings');
                    $pattern = '/location\.replace\(\'([^\']*)\'\);/';
                    preg_match( $pattern, $subject, $matches );
                    if ( count( $matches ) == 2 ) {
                        $redirect_url = $matches[1];
                    }
                }
                $redirect_url = apply_filters( 'cf7msm_redirect_url', $redirect_url, $wpcf7->id() );
                if ( !empty( $redirect_url ) ) {
                    wp_redirect( esc_url( $redirect_url ) );
                    exit;
                }
            }
        }
    }
}
add_action( 'wpcf7_mail_sent', 'cf7msm_mail_sent' );

/**
 * Go through a wpcf7 form's formstring and find the multistep url.
 * If $steps is true, return the steps part as "<curr>-<total>", otherwise return the url.
 */
function parse_form_for_multistep( $wpcf7, $steps = false ) {
    $formstring = $wpcf7->prop('form');
    if (preg_match('/\[multistep "(\d+)-(\d+)-(.+)"\]/', $formstring, $matches)) {
        if ( $steps ) {
            if ( !empty( $matches[1] ) && !empty( $matches[2] ) ) {
                return $matches[1] . '-' . $matches[2];
            }   
        }
        else {
            if ( !empty( $matches[3] ) ) {
                return $matches[3];
            }   
        }
    }
    return '';
}

/**
 * return the full url.
 */
function cf7msm_current_url() {
    $page_url = 'http';
    if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on") {
        $page_url .= "s";
    }
    $page_url .= "://";
    if ( isset( $_SERVER["SERVER_PORT"] ) && $_SERVER["SERVER_PORT"] != "80") {
        $page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $page_url;
}


/**
 * note at top of form tags
 */
function cf7msm_form_tag_header_text( $header_description ) {
    $description = $header_description . __( ". For more details, see %s.", 'contact-form-7' );
    $desc_link = wpcf7_link( 
        'https://wordpress.org/plugins/contact-form-7-multi-step-module/', 
        esc_html( __( 'the plugin page on WordPress.org', 'contact-form-7-multi-step-module' ) ), 
        array( 'target' => '_blank' )
    );
    printf( esc_html( $description ), $desc_link );
}

/**
 * Links to help the plugin.
 */
function cf7msm_form_tag_footer_text() {
    $url_donate = 'https://webheadcoder.com/donate-cf7-multi-step-forms';
    $url_review = 'https://wordpress.org/support/view/plugin-reviews/contact-form-7-multi-step-module#postform';
?>
    <p class="description" style="font-size:12px;margin-top:0;padding-top:0;font-style:normal;">
        <?php 
        printf( cf7msm_kses( 
            __( 'Like the Multi-step addition to CF7?  Let me know - <a href="%s" target="_blank">Donate</a> and <a href="%s" target="_blank">Review</a>.', 'contact-form-7-multi-step-module' )
            ), $url_donate, $url_review );
         ?>
    </p>
    <div style="position:absolute; right:25px; bottom:5px;">
        <a href="https://webheadcoder.com" target="_blank"><img src="<?php echo cf7msm_url( '/resources/logo.png' )?>" width="40"></a>
    </div>
<?php
}

/**
 * 
 */
function cf7msm_setup_next_url($not_used) {
    global $cf7msm_redirect_urls;
    if ( empty( $cf7msm_redirect_urls ) ) {
        $cf7msm_redirect_urls = array();
    }
    $wpcf7 = WPCF7_ContactForm::get_current();
    $redirect_url = parse_form_for_multistep( $wpcf7 );
    $redirect_url = apply_filters( 'cf7msm_redirect_url', $redirect_url, $wpcf7->id() );
    if ( !empty( $redirect_url ) ) {
        $cf7msm_redirect_urls[$wpcf7->id()] = $redirect_url;   
    }

    return $not_used;
}
add_filter( 'wpcf7_form_action_url', 'cf7msm_setup_next_url' );

 /**
  * Set event listener instead of using soon to be removed onSentOk.
  */
function cf7msm_footer() {
    global $cf7msm_redirect_urls;
    $js = '';
    if ( !empty( $cf7msm_redirect_urls ) ) {
        foreach( $cf7msm_redirect_urls as $id => $url ) {
            $js .= 'if ( event.detail.contactFormId === "' . $id . '") { ';
            $js .= sprintf('location.replace("%1$s");', htmlspecialchars_decode( esc_url( $url ) ) );
            $js .= '}';            
        }
    }
?>
<script type="text/javascript">
document.addEventListener( 'wpcf7mailsent', function( event ) {
    <?php echo $js; ?>
}, false );
</script>
<?php
}
add_action( 'wp_footer', 'cf7msm_footer', 90 );
