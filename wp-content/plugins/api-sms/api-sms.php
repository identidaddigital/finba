<?php
/*
Plugin Name: API-SMS
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/

add_action('wpcf7_before_send_mail', 'wpcf7_to_web_service');
function wpcf7_to_web_service ($WPCF7_ContactForm) {
	
	$submission = WPCF7_Submission::get_instance();
	$posted_data =& $submission->get_posted_data();
	if ($posted_data["cf7msm-step"] == "1-5") {

		//Save DNI so then i can use it to call ROL api and delete it
		$_SESSION["cuit"] = str_replace('-', '', $posted_data["nrocuit"]);
	
		// Extract data form the mail an map it
		$mapped_field['codarea'] = $posted_data['codarea'];
		$mapped_field['telefono'] = $posted_data['telefono'];

			
		$json_mapped_fields = json_encode($mapped_field);
		
//Invocamos el WebService
//
$codigo = substr(str_shuffle("0123456789"), 0, 4);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://servicio.smsmasivos.com.ar/enviar_sms.asp?api=1&usuario=xaper&clave=xaper2019&tos=".$posted_data['codarea'].$posted_data['telefono']."&texto=".$codigo,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => ""
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$_SESSION["sms"] = $codigo;
		
}
}