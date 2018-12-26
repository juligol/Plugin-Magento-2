<?php
namespace Doublepoint\Pickit\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getObjectManager(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManager;
    }

    public function getApi(){
        $objectManager = $this->getObjectManager();
        $api = $objectManager->create('Doublepoint\Pickit\Model\Apicall');
        return $api;
    }

    public function getRegionId($region){
        $objectManager = $this->getObjectManager();
        $region_code = $this->getRegionCode($region);
        $region = $objectManager->create('Magento\Directory\Model\Region');
		$regionId = $region->loadByCode($region_code, 'AR')->getId();
		return $regionId;
    }

    public function getRegionCode($region){
        $region = trim(strtoupper(str_replace(array('á','é','í','ó','ú'), array('a','e','i','o','u'), $region)));
        $regionMap = $this->getRegionMap();
        if ($region == 'AUTONOMOUS CITY OF BUENOS AIRES') $region = 'CIUDAD AUTONOMA DE BUENOS AIRES';
        if (!array_key_exists($region, $regionMap) ) $region =  'CIUDAD AUTONOMA DE BUENOS AIRES';
        $region_code = $regionMap[$region];
		return $region_code;
    }

    public function getRegionMap(){
        return array('BUENOS AIRES'                     => 'BA',
                     'CIUDAD AUTONOMA DE BUENOS AIRES'  => 'CABA',
                     'CATAMARCA'                        => 'CT',
                     'CHACO'			                => 'CC',
                     'CHUBUT'		                    => 'CH',
                     'CORDOBA'		                    => 'CD',
                     'CORRIENTES'	                    => 'CR',
                     'ENTRE RIOS'	                    => 'ER',
                     'FORMOSA'		                    => 'FO',
                     'JUJUY'			                => 'JY',
                     'LA PAMPA'		                    => 'LP',
                     'LA RIOJA'		                    => 'LR',
                     'MENDOZA'		                    => 'MZ',
                     'MISIONES'		                    => 'MN',
                     'NEUQUEN'		                    => 'NQ',
                     'RIO NEGRO'		                => 'RN',
                     'SALTA'			                => 'SA',
                     'SAN JUAN'		                    => 'SJ',
                     'SAN LUIS'		                    => 'SL',
                     'SANTA CRUZ'	                    => 'SC',
                     'SANTA FE'		                    => 'SF',
                     'SANTIAGO DEL ESTERO'              => 'SE',
                     'TIERRA DEL FUEGO'                 => 'TF',
                     'TUCUMAN'		                    => 'TM'
                    );
    }

    public function generateAddressArray($puntoPickit){
		$direccion = explode(',', $puntoPickit["Direccion"]);
		$street = $direccion[0]; // Sucursal domicilio.
		$city   = $direccion[1]; // Sucursal ciudad.
		$region = $direccion[2]; // Sucursal provincia.
		$regionCode = $this->getRegionCode($region);
		$regionId = $this->getRegionId($region);
		$array = array("telephone"	=> $puntoPickit["Telefono"],
					   "countryId" 	=> 'AR',
					   "region"    	=> $region,
					   "regionCode" => $regionCode,
					   "regionId" 	=> $regionId,
					   "city" 		=> $city,
					   "street" 	=> [$puntoPickit["Direccion"]],
					   "postcode" 	=> $puntoPickit["CodigoPostal"]
		);
		return $array;
    }

    public function imponerTransaccionEnvioV2($cotizacionId, $dataCliente, $orderId)
    {
        $api = $this->getApi();
        $estado = $this->getEstadoInicial();
        $response = $api->iniciar(null)->imponerTransaccionEnvioV2($cotizacionId, $dataCliente, $orderId, $estado);       
        return $response;
    }

    public function getEstadoInicial()
    {
        $estado = 1;
        if($this->getConfig('carriers/pickit/disponibleretiro') == "imponer")
            $estado = 2;
        
        return $estado;
    }

    public function log($mensaje){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dp.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($mensaje);
    }
}