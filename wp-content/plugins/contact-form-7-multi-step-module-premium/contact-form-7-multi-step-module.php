<?php

/*
Plugin Name: Contact Form 7 Multi-Step Forms (Premium)
Plugin URI: http://www.mymonkeydo.com/contact-form-7-multi-step-module/
Description: Enables the Contact Form 7 plugin to create multi-page, multi-step forms.
Author: Webhead LLC.
Author URI: http://webheadcoder.com
Version: 3.0.7
Text Domain: contact-form-7-multi-step-module
*/
/*  Copyright 2018 Webhead LLC (email: info at webheadcoder.com)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'cf7msm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function cf7msm_fs()
    {
        global  $cf7msm_fs ;
        
        if ( !isset( $cf7msm_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $cf7msm_fs = fs_dynamic_init( array(
                'id'             => '1614',
                'slug'           => 'contact-form-7-multi-step-module',
                'type'           => 'plugin',
                'public_key'     => 'pk_b445061ad8b540f6a89c2c4f4df19',
                'is_premium'     => true,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'first-path' => 'plugins.php',
                'contact'    => false,
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $cf7msm_fs;
    }
    
    // Init Freemius.
    cf7msm_fs();
    // Signal that SDK was initiated.
    do_action( 'cf7msm_fs_loaded' );
    define( 'CF7MSM_VERSION', '3.0.7' );
    define( 'CF7MSM_PLUGIN', __FILE__ );
    define( 'CF7MSM_FREE_TEXT_PREFIX_RADIO', '_wpcf7_radio_free_text_' );
    define( 'CF7MSM_FREE_TEXT_PREFIX_CHECKBOX', '_wpcf7_checkbox_free_text_' );
    define( 'CF7MSM_MIN_CF7_VERSION', '4.8' );
    define( 'CF7MSM_LEARN_MORE_URL', 'https://webheadcoder.com/contact-form-7-multi-step-forms/' );
    /**
     * Change update message
     */
    function cf7msm_fs_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        $limited_time = '';
        return cf7msm_kses( sprintf( __( 'Please help improve the %1$s plugin!  I have chosen to use %2$s to get an idea of how users use my plugin.<br><br>  If you opt-in, the administrator email and some data about your usage of %1$s will be sent to %2$s. If you skip this, that\'s okay! The plugin will still work just fine.', 'contact-form-7-multi-step-module' ), '<strong>' . $plugin_title . '</strong>', $freemius_link ) ) . $limited_time;
    }
    
    cf7msm_fs()->add_filter(
        'connect_message_on_update',
        'cf7msm_fs_custom_connect_message_on_update',
        10,
        6
    );
    /**
     * Add account link if paying.
     */
    function cf7msm_plugin_action_links( $links )
    {
        
        if ( cf7msm_fs()->is__premium_only() ) {
            $links[] = '<a href="' . esc_url( cf7msm_fs()->get_account_url() ) . '">' . __( 'Account' ) . '</a>';
            $links[] = '<a href="' . esc_url( cf7msm_fs()->contact_url() ) . '">' . __( 'Contact' ) . '</a>';
        }
        
        if ( cf7msm_fs()->is_not_paying() ) {
            $links[] = '<a href="' . CF7MSM_LEARN_MORE_URL . '" target="_blank">' . __( 'Learn about PRO' ) . '</a>';
        }
        return $links;
    }
    
    add_filter( "plugin_action_links_" . plugin_basename( CF7MSM_PLUGIN ), 'cf7msm_plugin_action_links' );
    require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'cf7msm.php';
    require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'cf7msm-admin.php';
    require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-multistep.php';
    require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-session.php';
    require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-back.php';
}
