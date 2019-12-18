<?php

function register_my_session(){
    if( ! session_id() ) {
        session_start();
    }
}

add_action('init', 'register_my_session');

add_action( 'wp_enqueue_scripts', 'basel_child_enqueue_styles', 1000 );

function basel_child_enqueue_styles() {
	if( basel_get_opt( 'minified_css' ) ) {
		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.min.css', array('bootstrap') );
	} else {
		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.css', array('bootstrap') );
	}
	
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('bootstrap') );
}

add_filter( 'basel_header_configuration', 'basel_custom_header_configuration', 1, 1 );

function basel_custom_header_configuration() {

	return array(
            'container' => array(
                'wrapp-header' => array(
                    'logo',
					'main_nav',
                    'right-column' => array(
						'header_links',
                        'mobile_icon',
				)
			)
		)			
	);
}

function wooc_add_field_to_registration(){
    wc_get_template( 'checkout/terms.php' );
}
add_action( 'woocommerce_register_form', 'wooc_add_field_to_registration' );
 
 
 function wooc_validation_registration( $errors, $username, $password, $email ){
    if ( empty( $_POST['terms'] ) ) {
        throw new Exception( __( 'Debes aceptar los términos y condiciones', 'woocommerce' ) );
    }
    return $errors;
}
add_action( 'woocommerce_process_registration_errors', 'wooc_validation_registration', 10, 4 );

add_action( 'template_redirect', function() {

        if ( !is_user_logged_in() && is_page('solicita/') ){
            wp_redirect( site_url( '/mi-cuenta' ) );
            exit();
        }

     });

// After registration, redirect page
function custom_registration_redirect() {
    return home_url('/solicita/');
}
add_action('woocommerce_registration_redirect', 'custom_registration_redirect', 2);
add_action( 'wp_footer', 'mycustom_wp_footer' );

add_filter('woocommerce_login_redirect', 'wc_login_redirect'); 
function wc_login_redirect( $redirect_to ) {

   $redirect_to = 'https://finba.com.ar/solicita/';
   return $redirect_to;

}
 
function mycustom_wp_footer() {
?>
<script type="text/javascript">
document.addEventListener('wpcf7submit', function( event ) {
	console.log(event);
    if ( '85' == event.detail.contactFormId ) {
        if (event.detail.apiResponse.message == "Pin invalido") {
            location.replace('https://finba.com.ar/ingresapin/');
        } else if (event.detail.apiResponse.message != "No Aprobado") {
            location.replace('https://finba.com.ar/confirma/');
        } else {
            location.replace('https://finba.com.ar/no-aprobado/');
        }
    }
}, false );
</script>
<script type="text/javascript">
document.addEventListener( 'wpcf7submit', function( event ) {
	console.log(event);
    if ( '79' == event.detail.contactFormId ) {
        if (event.detail.apiResponse.message == "Datos invalidos") {
            location.replace('https://finba.com.ar/identidad-incorrecta/');
        }
    }
}, false );
</script>
<?php
}



function getCuit() {
    return $_SESSION["cuit"];
}
add_shortcode('GET_CUIT', 'getCuit');

function getEmpleador() {
    return $_SESSION["empleador"];
}
add_shortcode('GET_EMPLEADOR', 'getEmpleador');

function getDireccion() {
    return $_SESSION["direccion"];
}
add_shortcode('GET_DIRECCION', 'getDireccion');

function getSms() {
    return $_SESSION["sms"];
}
add_shortcode('GET_SMS', 'getSms');

function cf7_dynamic_select_empleador($choices, $args=array()) {
		// this function returns and array of label => value pairs to be used in the select field
		$choices = array(
			'Seleccionar' => 'Seleccionar',
			'TRYNET S.R.L.' => 'TRYNET S.R.L.',
			'TUCUMAN BBS SRL' => 'TUCUMAN BBS SRL',
		);
	            if ($_SESSION["empleador"] == null) {
					$_SESSION["empleador"] = "No reconozco ninguna opción";
				}
                $choices[$_SESSION["empleador"]] = $_SESSION["empleador"];
	            $choices = assoc_array_shuffle($choices);
                $choices['default'] = array('Seleccionar');
		return $choices;
	} // end function cf7_dynamic_select_do_example1
	add_filter('cf7_dynamic_select_empleador', 'cf7_dynamic_select_empleador', 10, 2);


function assoc_array_shuffle($array)
{
    $orig = array_flip($array);
    shuffle($array);
    foreach($array AS $key=>$n)
    {
        $data[$n] = $orig[$n];
    }
    return array_flip($data);
}



function cf7_dynamic_select_direccion($choices, $args=array()) {
		// this function returns and array of label => value pairs to be used in the select field
		$choices = array(
			'Seleccionar' => 'Seleccionar',
			'CALZADA CIRCULAR 202' => 'CALZADA CIRCULAR 202',
			'SAN FRANCISCO 816' => 'SAN FRANCISCO 816',
		);
	            if ($_SESSION["direccion"] == null) {
					$_SESSION["direccion"] = "No reconozco ninguna opción";
				}
                $choices[$_SESSION["direccion"]] = $_SESSION["direccion"];
	            $choices = assoc_array_shuffle($choices);
                $choices['default'] = array('Seleccionar');
		return $choices;
	} // end function cf7_dynamic_select_do_example1
	add_filter('cf7_dynamic_select_direccion', 'cf7_dynamic_select_direccion', 10, 2);
    add_action( 'admin_menu', 'linked_url' );
    function linked_url() {
    add_menu_page( 'linked_url', 'Imp. CUITs Rechazados', 'read', 'my_slug', '', 'dashicons-text', 1 );
    }

    add_action( 'admin_menu' , 'linkedurl_function' );
    function linkedurl_function() {
    global $menu;
    $menu[1][2] = "https://www.finba.com.ar/cron/cargar-cuits.php";
    }