<?php

namespace Doublepoint\Pickit\Controller\Adminhtml\Post;

class Imprimir extends \Magento\Backend\App\Action
{
	protected $resultJsonFactory;

	public function __construct(\Magento\Backend\App\Action\Context $context, 
								\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
								array $data = [])
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->api = $this->_objectManager->create('Doublepoint\Pickit\Model\Apicall');
		$this->_resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context, $data);
	}

    public function execute()
    {
		$item = $this->getRequest()->getPost('item');
        $idTransaccion = $item["id_transaccion"];
		if($item["estado"] == 1){
			$response = $this->api->iniciar(null)->disponibleParaRetiro($idTransaccion);
			$estado = $this->verificarEstado($item, $response);
			if(!$estado){
				$resultJson = $this->resultJsonFactory->create();
				$resultJson->setData($response);
				return $resultJson;
			}
		}
		$response = $this->api->iniciar(null)->obtenerEtiqueta($idTransaccion);
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
	}
	
	public function verificarEstado($item, $response)
    {
		$response = json_decode(json_encode($response), True);
		$estado = $this->estadoCorrecto($response["Status"]);
		if($estado){
			$this->actualizarEstado($item);
		}
		return $estado;
	}
	
	public function estadoCorrecto($Status)
    {
		return $Status["Code"] == "200" && $Status["Text"] == "OK";
	}
	
	public function actualizarEstado($item)
    {
		$connection = $this->_resources->getConnection();
		$themeTable = $this->_resources->getTableName('pickit_order');
        $sql = "UPDATE " . $themeTable . " SET estado = 2 WHERE post_id = " . $item['post_id'];
        $connection->query($sql);
    }
}