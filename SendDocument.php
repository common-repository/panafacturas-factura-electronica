<?php
class GTIPFESendDocument
{

    // This plugin utilizes one of our services to receive specific order information, for the purpose of generating and issuing electronic documents.
    public $tokenDev = 'https://pruebas.panafacturas.com.pa:444/APICargaFactura/api/Authz/TokenAcceso';
    public $tokenProd = 'https://www.panafacturas.com/APICargaFactura/api/Authz/TokenAcceso';
    public $apiDev = 'https://pruebas.panafacturas.com.pa:444/APICargaFactura/api/RecepcionFe/RecepcionFe';
    public $apiProd = 'https://www.panafacturas.com/APICargaFactura/api/RecepcionFe/RecepcionFe';

    public function GTIPFE_ObtenerToken($GTIPFEUsuario, $GTIPFEClave)
    {
        $pruebas = get_option('GTIPFEambiente');
        $url = ($pruebas == 'on') ? $this->tokenDev : $this->tokenProd;
        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'timeout'   => 45,
            'headers'   => array(
                'pUsuario' => $GTIPFEUsuario,
                'pClave'   => $GTIPFEClave
            ),
            'body'      => array()
        ));
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return array('error' => $error_message);
        } else {
            $body = wp_remote_retrieve_body($response);
            $response_data = json_decode($body, true);
            $token = isset($response_data['Procesamiento']['Token']) ? $response_data['Procesamiento']['Token'] : null;
            return $token;
        }
    }

    public function GTIPFE_SendDocument($token, $json)
    {
        $pruebas = get_option('GTIPFEambiente');
        $url = ($pruebas == 'on') ? $this->apiDev : $this->apiProd;

        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'timeout'   => 45,
            'headers'   => array(
                'pMedioEmision' => 'Z6jdSrGuCHuK1165mElN1w==',
                'pRetornaXml'   => '0',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
            ),
            'body'      => $json
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return array('error' => $error_message);
        } else {
            $body = wp_remote_retrieve_body($response);
            return json_decode($body, true);
        }
    }

}

?>