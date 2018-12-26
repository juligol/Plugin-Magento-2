<?php
namespace Doublepoint\Pickit\Controller\Ajax;

class ObtenerCotizacionEnvioV2 extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	
	public function __construct(\Magento\Framework\App\Action\Context $context, 
								\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
								\Magento\Checkout\Model\Cart $cart,
								array $data = [])
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
		$this->cart = $cart;
		$this->quote = $this->cart->getQuote();
		$this->resultJsonFactory = $resultJsonFactory;
		$this->api = $this->_objectManager->create('Doublepoint\Pickit\Model\Apicall');
		parent::__construct($context, $data);
	}
	
	public function execute() 
	{
		$articulos = $this->getItems();		
		$direccionCliente = $this->getRequest()->getPost('direccionCliente');
		$response = $this->api->iniciar(null)->obtenerCotizacionEnvioV2($direccionCliente[0], $articulos);
		$resultJson = $this->resultJsonFactory->create();
		$resultJson->setData($response);
		return $resultJson;
	}
	
	public function getItems()
	{
		// get quote items array
		$items = $this->quote->getAllItems();
		return $this->generateItemsArray($items);
	}
	
	public function generateItemsArray($items)
	{
		$articulos = array();
		foreach($items as $item) {
			$articulos[] = $this->generateItem($item);
		}
		return $articulos;
	}
	
	public function generateItem($item)
	{
		return array('sku' => $item->getSku(),
					 'articulo' => $item->getName(),
					 'valorDeclarado' => $item->getPrice(),
					 'alto' => 14,
					 'ancho' => 40,                            
					 'largo' => 0.275,
					 'peso' => 0.3
		);           
	}
}