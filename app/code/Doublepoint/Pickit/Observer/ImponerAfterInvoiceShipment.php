<?php
namespace Doublepoint\Pickit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class ImponerAfterInvoiceShipment implements ObserverInterface
{
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession) 
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_orders = $this->_objectManager->create('\Magento\Sales\Model\Order');
		$this->_resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$order = $observer->getOrder();
		$this->helper->log("Estado del order: " . $order->getState());
		$imposicionValida = $this->helper->getConfig('carriers/pickit/global_imposicion') == "manual";
		if($order->getState() == $this->_orders::STATE_COMPLETE && $imposicionValida) {
			$pickitOrder = $this->getPickitOrder($order->getId());
			$this->helper->log("Post Id: " . $pickitOrder["post_id"]);
			$dataCliente = $this->generarDataCliente($pickitOrder);
			$response = $this->helper->imponerTransaccionEnvioV2($pickitOrder["id_cotizacion"], $dataCliente, $pickitOrder["id_orden"]);
			$response = json_decode(json_encode($response), True);
			$Status = $response["Status"];
			$this->helper->log("imponerTransaccionEnvioV2 desde imposicion manual");
			$this->helper->log("Code: " . $Status["Code"] . ", Text: " . $Status["Text"]);
			if($this->estadoCorrecto($Status)){
				$this->actualizarOrden($pickitOrder["id_orden"], $response["Response"]);
			}
		}
        return;
    }
	
	public function getPickitOrder($OrderId)
    {
        $connection = $this->_resources->getConnection();
		$themeTable = $this->_resources->getTableName('pickit_order');
		$sql = "SELECT * FROM " . $themeTable . " WHERE id_orden = " . intval($OrderId);
		$result = $connection->fetchRow($sql);
        return $result;
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
}