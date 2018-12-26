<?php
namespace Doublepoint\Pickit\Block;

class Configuracion extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = []) 
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
        parent::__construct($context, $data);
    }
	
	public function getLinkText() 
	{
        return $this->helper->getConfig('carriers/pickit/link_text');
    }

    public function getDescripcion() 
	{
        return $this->helper->getConfig('carriers/pickit/description');
    }

    public function getDescripcionPrecioDefault() 
	{
        return $this->helper->getConfig('carriers/pickit/descripcion_precio_default');
    }
	
}