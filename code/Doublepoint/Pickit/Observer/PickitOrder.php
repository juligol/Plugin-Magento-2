<?php
namespace Doublepoint\Pickit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class PickitOrder implements ObserverInterface
{
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession) 
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_orders = $this->_objectManager->create('\Magento\Sales\Model\Order');
		$this->_resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_checkoutSession = $checkoutSession;
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_checkoutSession->getPickitActivo()) {
            $orderIds = $observer->getEvent()->getOrderIds();
            foreach($orderIds as $orderId){
                $order = $this->_orders->load($orderId);
                $datos = $this->completarDatos($order);
                $Response = $this->getResponse($datos['dataCliente'], $orderId);
                $this->insertarPickitOrder($order, $Response, $datos);
            }
            $this->reiniciarSessiones();
        }
        return;
    }

    public function completarDatos($order)
    {
        $datos = $this->_checkoutSession->getPickitData();
        $ship = $order->getShippingAddress();
        $bill = $order->getBillingAddress();

        $datos['nombre']	= $bill->getFirstname();
        $datos['apellido']	= $bill->getLastname();
        $datos['telefono']	= $ship->getTelephone();
        if($ship->getEmail() != "") {
            $datos['email'] = $ship->getEmail();
        } elseif($bill->getEmail() != "") {
            $datos['email'] = $bill->getEmail();
        } elseif($order->getData("customer_email") != "") {
            $datos['email'] = $order->getData("customer_email");
        } else {
            $datos['email'] = "email@prueba.com";
        }

        $campoDni  = 'customer_';
        $campoDni .= $this->helper->getConfig('carriers/pickit/global_idusuario');
        if($order->getData($campoDni))
            $datos['dni'] = $order->getData($campoDni);
        else
            $datos['dni'] = $ship->getVatId();

        $datos['dataCliente'] = array(
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'dni' => $datos['dni'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono']
        );
        return $datos;
    }

    public function getResponse($dataCliente, $orderId)
    {
        $Response = null;
        $imposicionValida = $this->helper->getConfig('carriers/pickit/global_imposicion') == "automatica";
        if($imposicionValida) {
            $cotizacionId = $this->_checkoutSession->getCotizacionId();
            $response = $this->helper->imponerTransaccionEnvioV2($cotizacionId, $dataCliente, $orderId);
            $response = json_decode(json_encode($response), True);
            $Status = $response["Status"];
            $this->helper->log("imponerTransaccionEnvioV2 desde imposicion automatica");
            $this->helper->log("Code: " . $Status["Code"] . ", Text: " . $Status["Text"]);
            if($Status["Code"] == "200" && $Status["Text"] == "OK"){
                $Response = $response["Response"];
            }
        }
        return $Response;
    }

    public function insertarPickitOrder($order, $Response, $datos)
    {
        $connection = $this->_resources->getConnection();
        $sql = $this->generarQuery($order, $datos);
        $connection->query($sql);
        if($Response){
            $sql = $this->generarQueryImponer($order->getId(), $Response);
            $connection->query($sql);
        }
    }

    public function generarQuery($order, $datos)
    {
        $themeTable = $this->_resources->getTableName('pickit_order');
        $OrderId	= $order->getId();
        $OrderIncId = $order->getIncrementId();
        $cotizacionId = $this->_checkoutSession->getCotizacionId();
        $sql = "INSERT INTO " . $themeTable . " (id_orden, direccion, localidad, 
                provincia, cp_destino, nombre, apellido, email, telefono, precio, 
                valor_declarado, volumen, peso, constancia, 
                order_increment_id, id_cotizacion, dni, datos_sucursal)  
                VALUES (".intval($OrderId).", '".$datos['direccion']."', '".$datos['localidad']."', 
                '".$datos['provincia']."', '".$datos['cpDestino']."', '".$datos['nombre']."', 
                '".$datos['apellido']."', '".$datos['email']."', '".$datos['telefono']."', 
                ".$datos['precio'].", ".$datos['valorDeclarado'].", ".$datos['volumen'].", 
                ".$datos['peso'].", 2, ".$OrderIncId.", 
                ".$cotizacionId.", '".$datos['dni']."', '".$datos['sucursal_retiro']."')";
        return $sql;
    }

    public function generarQueryImponer($OrderId, $Response)
    {
        $themeTable = $this->_resources->getTableName('pickit_order');
        $estado = $this->helper->getEstadoInicial();
        $sql = "UPDATE " . $themeTable . " SET cod_tracking = '".$Response["CodigoTransaccion"]."',
                tracking = '".$Response["urlTrackingTransaccion"]."', estado = '".$estado."',
                id_transaccion = ".$Response["TransaccionId"]." WHERE id_orden = ".intval($OrderId);
        return $sql;
    }

    public function reiniciarSessiones()
    {
        $this->_checkoutSession->setPickitActivo(false);
        $this->_checkoutSession->unsInfoPuntoSeleccionado();
        $this->_checkoutSession->unsCotizacionId();
        $this->_checkoutSession->unsPickitData();
    }

}