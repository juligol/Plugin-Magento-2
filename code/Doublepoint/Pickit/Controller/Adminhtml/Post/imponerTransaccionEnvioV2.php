<?php

namespace Doublepoint\Pickit\Controller\Adminhtml\Post;

class ImponerTransaccionEnvioV2 extends \Magento\Backend\App\Action
{
	protected $resultJsonFactory;

	public function __construct(\Magento\Backend\App\Action\Context $context, 
								\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
								array $data = [])
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
		$this->_resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context, $data);
	}

    public function execute()
    {
		$imposicionValida = $this->helper->getConfig('carriers/pickit/global_imposicion') == "noimponer";
        if($imposicionValida) {
			$item = $this->getRequest()->getPost('item');
			$dataCliente = $this->generarDataCliente($item);
			$response = $this->helper->imponerTransaccionEnvioV2($item["id_cotizacion"], $dataCliente, $item["id_orden"]);
            $response = json_decode(json_encode($response), True);
            $Status = $response["Status"];
            $this->helper->log("imponerTransaccionEnvioV2 desde imposicion no realizada");
            $this->helper->log("Code: " . $Status["Code"] . ", Text: " . $Status["Text"]);
			if($this->estadoCorrecto($Status)){
				$this->actualizarOrden($item["id_orden"], $response["Response"]);
			}
        }else{
			$response = $this->generarResponse();
		}
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
	}

	public function generarDataCliente($item)
    {
		$dataCliente = array(
			'nombre' => $item['nombre'],
			'apellido' => $item['apellido'],
			'dni' => $item['dni'],
			'email' => $item['email'],
			'telefono' => $item['telefono']
		);
		return $dataCliente;
	}
	
	public function estadoCorrecto($Status)
    {
		return $Status["Code"] == "200" && $Status["Text"] == "OK";
	}
	
	public function actualizarOrden($OrderId, $Response)
    {
		$connection = $this->_resources->getConnection();
		$themeTable = $this->_resources->getTableName('pickit_order');
		$estado = $this->helper->getEstadoInicial();
        $sql = "UPDATE " . $themeTable . " SET cod_tracking = '".$Response["CodigoTransaccion"]."',
                tracking = '".$Response["urlTrackingTransaccion"]."', estado = '".$estado."',
                id_transaccion = ".$Response["TransaccionId"]." WHERE id_orden = ".intval($OrderId);
        $connection->query($sql);
	}
	
	public function generarResponse()
    {
		return array('Status' => ["Code" => 0, "Text" => "No realizar imposición está desactivada, por favor vaya a la configuración y actívela"],
					 'Response' => null);
	}
}