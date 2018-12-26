<?php
namespace Doublepoint\Pickit\Model;

class Apicall extends \Magento\Framework\Model\AbstractModel
{
    protected $_tokenId;
    protected $_storeId;
    /**
     * Cotiza el envio de los productos segun los parametros
     *
     * @param $params 
     * @return $result array tal cual pickit or null en error
     */

    public function _construct() {
    }

    public function iniciar($storeid) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
        if($storeid == null) {
            $this->_storeId = $this->_storeManager->getStore()->getStoreId();
        } else {
            $this->_storeId = $storeid;
        }
        $this->_tokenId = $this->helper->getConfig('carriers/pickit/token_id');
        return $this;
    }

    public function _callWS($metodo, $parametros)
	{
        if($metodo == "ObtenerDetalleTransaccion") {
            $apikey = $this->helper->getConfig('carriers/pickit/apikey_webapp');
        } else {
            $apikey = $this->helper->getConfig('carriers/pickit/apikey');
        }
		$parametros = json_encode($parametros);
		$data = json_encode(array('ApiKey' => $apikey, 'Metodo' => $metodo, 'Parametros' => $parametros));
		$postdata = http_build_query(array('value' => $data), '', '&');
		$opts = array('http' =>
			array(
			  'method'  => 'POST',
			  'header'  => 'Content-type: application/x-www-form-urlencoded',
			  'content' => $postdata
			)
		);
		$context  = stream_context_create($opts);
		$testeoHabilitado = $this->helper->getConfig('carriers/pickit/global_testmode') == 1;
		if($testeoHabilitado)
			$result = file_get_contents($this->helper->getConfig('carriers/pickit/url_webservice_test'), false, $context);
		else
			$result = file_get_contents($this->helper->getConfig('carriers/pickit/url_webservice_prod'), false, $context);
		return json_decode($result);
	}

    public function obtenerCotizacionEnvioV2($direccion, $articulos) {
        $metodo = "ObtenerCotizacionEnvioV2";
        $params = array('tokenId' => $this->_tokenId,
                        'SLA' => 1,
                        'direccionCliente' => $direccion,
                        'articulos' => $articulos,
                        "dataDireccionAlternativa" =>  array(
                            "provinciaId"=> "",
                            "direccion"=> "",
                            "localidad"=> "",
                            "codigoPostal"=> ""
                        )
                    );
        return $this->_callWS($metodo, $params);
    }

    public function obtenerInformacionPuntoSeleccionado($idCotizacion) {
        $metodo = "ObtenerInformacionPuntoSeleccionado";
        $params = array('cotizacionId' => $idCotizacion,
                        'tokenId' => $this->_tokenId
                    );
        return $this->_callWS($metodo, $params);
    }

    public function imponerTransaccionEnvioV2($idCotizacion, $dataCliente, $numeroOrden, $estado) {
        $metodo = "ImponerTransaccionEnvioV2";
        $params = array('cotizacionId' => $idCotizacion,
                        'tokenId' => $this->_tokenId,
                        'observaciones' => '',
                        'numeroOrden' => $numeroOrden,
                        'numeroShipment' => '',
                        'estadoInicial' => $estado,
                        'dataCliente' => $dataCliente
                    );

        return $this->_callWS($metodo, $params);
    }

    public function disponibleParaRetiro($transaccionId) {
        $metodo = "DisponibleParaRetiro";
        $params = array('tokenId' => $this->_tokenId,
                        'transaccionId' => $transaccionId
                    );

        return $this->_callWS($metodo, $params);
    }

    public function obtenerEtiqueta($transaccionId) {
        $metodo = "ObtenerEtiqueta";
        $params = array('transaccionId' => $transaccionId);

        return $this->_callWS($metodo, $params);
    }
}
?>