<?php

/*
Plugin Name: API ROL
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/

add_action('wpcf7_before_send_mail', 'wpcf7_call_rol');
function wpcf7_call_rol ($WPCF7_ContactForm) {
	$submission = WPCF7_Submission::get_instance();
	$posted_data =& $submission->get_posted_data();
        if (isset($posted_data['validapin']) && $posted_data["cf7msm-step"] == "2-5") {

            if (intval($posted_data['validapin']) != getSms()) {
                $_SESSION["resultado"] = "-1";
            } else {
                $_SESSION["resultado"] = "";
                $_SESSION["resultadoVal"] = "";
    	
    	        //Check if report already exists
	        //$informe = json_decode(file_get_contents('https://informe.riesgoonline.com/api/informes/consultar/' . $_SESSION["dni"] . '?username=dm-ricardes&password=1234&formato=json'), true);
	        //$informe = json_decode(file_get_contents('https://informe.riesgoonline.com/api/informes/solicitar/' . $_SESSION["cuit"] . '?username=dm-ricardes&password=1234&formato=json'), true);

                $ch = curl_init("https://www.finba.com.ar/api/cuit-no-aprobado.php?nro=" . $_SESSION["cuit"]); // such as http://example.com/example.xml
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($data);
				
				if (!$data->exists) {

					$ch = curl_init("https://informe.riesgoonline.com/api/informes/consultar/" . $_SESSION["cuit"] . "?username=xaper&password=Xaper2019!&formato=json&version=1"); // such as http://example.com/example.xml
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$data = curl_exec($ch);
					curl_close($ch);
					$data = json_decode($data);
					
					if (!isset($data->resultado)) {
						$ch = curl_init("https://informe.riesgoonline.com/api/informes/solicitar/" . $_SESSION["cuit"] . "?username=xaper&password=Xaper2019!&formato=json&version=1"); // such as http://example.com/example.xml
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						$data = curl_exec($ch);
						curl_close($ch);
						$data = json_decode($data);
					}

					$experto = $data->resultado_modulos->experto;
					$resultado = $experto->codigo . $experto->resultado;
					if ($data->resultado->seccion_cj->seccion_cj_in[1][0]->encabezado_domicilios->domicilio_declarado != null &&
					   $data->resultado->seccion_cj->seccion_cj_in[1][0]->encabezado_domicilios->domicilio_declarado != "") {
						$direccion = $data->resultado->seccion_cj->seccion_cj_in[1][0]->encabezado_domicilios->domicilio_declarado;
					} else {
						$in = false;
						foreach ($data->resultado->seccion_cj->seccion_cj_in[1][0]->encabezado_domicilios as $key=>$value) {
							if ($in == false) {
								$in = true;
								$direccion = $value;
							}
						}
					}
					$empleador = $data->resultado->seccion_vl->seccion_vl_rl[0][0]->tabla_resumen_empleadores[0]->razon_social;

					if ($resultado != 'MPN' && $resultado != '') {
						$_SESSION["resultado"] = $resultado;
						$_SESSION["direccion"] = $direccion;
						$_SESSION["empleador"] = $empleador;
					}

				
				}
            }
        }
}

add_action('wpcf7_before_send_mail', 'wpcf7_call_rol_validation');
function wpcf7_call_rol_validation ($WPCF7_ContactForm) {

    	$submission = WPCF7_Submission::get_instance();
	$posted_data =& $submission->get_posted_data();
    if (isset($posted_data['validapin']) && $posted_data["cf7msm-step"] == "3-5") {
        if (isset($posted_data['empleadorVal'])) {
            if ($posted_data['empleadorVal'] != $_SESSION['empleador'] || $posted_data['direccionVal'] != $_SESSION['direccion']) {
                $_SESSION["resultadoVal"] = "-2";
            } else {
                $_SESSION["resultadoVal"] = "";
            }
        }
	}
}

function cf7_custom_redirect_uri($items, $result) {
    //echo $_SESSION["resultado"]; die();
if ($items["into"] != "#wpcf7-f4-p69-o1" && ($_SESSION["resultado"] == "" || $_SESSION["resultado"] == "MPN")) {
    $items["message"] = "No Aprobado";
} else if ($items["into"] != "#wpcf7-f4-p69-o1" && ($_SESSION["resultado"] == "-1")) {
    $items["message"] = "Pin invalido";
} else if ($items["into"] != "#wpcf7-f4-p69-o1" && ($_SESSION["resultadoVal"] == "-2")) {
    $items["message"] = "Datos invalidos";
}
    return $items;

}
add_filter('wpcf7_ajax_json_echo', 'cf7_custom_redirect_uri', 10, 2);