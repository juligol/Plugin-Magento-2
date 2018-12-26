<?php
namespace Doublepoint\Pickit\Block\Adminhtml;

class Url extends \Magento\Backend\Block\Template
{
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = []) 
	{
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
        parent::__construct($context, $data);
    }
	
	public function getImposicion() 
	{
        return $this->helper->getConfig('carriers/pickit/global_imposicion');
    }
	
}