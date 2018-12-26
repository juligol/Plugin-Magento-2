<?php
namespace Doublepoint\Pickit\Controller\Ajax;

class ObtenerInformacionPuntoSeleccionado extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	
	public function __construct(\Magento\Framework\App\Action\Context $context, 
								\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
								\Magento\Checkout\Model\Session $checkoutSession,
								\Magento\Customer\Model\Session $customerSession,
								array $data = [])
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->api = $this->_objectManager->create('Doublepoint\Pickit\Model\Apicall');
		$this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
		$this->_checkoutSession = $checkoutSession;
		$this->customerSession = $customerSession;
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context, $data);
	}
	
	public function execute() 
	{
		//$quote->getCustomerTaxVat();
		$cotizacionId = $this->getRequest()->getPost('cotizacionId');
		$response = $this->api->iniciar(null)->obtenerInformacionPuntoSeleccionado($cotizacionId);
		$array = json_decode(json_encode($response), True);
		$Status = $array["Status"];
		$this->logStatus($Status);
		if($Status["Code"] == "200" && $Status["Text"] == "OK"){
			$this->initializeSessions($array, $cotizacionId);
			$address = $this->helper->generateAddressArray($array["Response"]["PuntoPickit"]);	
			$response = ["Status" => $Status, "Response" => $array["Response"], "Address" => $address];
		}
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
	}

	public function logStatus($Status){
		$this->helper->log("obtenerInformacionPuntoSeleccionado");
		$this->helper->log("Code: " . $Status["Code"] . ", Text: " . $Status["Text"]);
	}

	public function initializeSessions($array, $cotizacionId){
		$this->_checkoutSession->setCotizacionId($cotizacionId);
		$this->_checkoutSession->setInfoPuntoSeleccionado($array);
		$this->_checkoutSession->setPickitActivo(true);
	}
}