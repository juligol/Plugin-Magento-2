<?php
namespace Doublepoint\Pickit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class Sesiones implements ObserverInterface
{
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession) 
    {
        $this->_checkoutSession = $checkoutSession;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_checkoutSession->setPickitActivo(false);
        $this->_checkoutSession->unsInfoPuntoSeleccionado();
        $this->_checkoutSession->unsCotizacionId();
    }
}