<?php
namespace Doublepoint\Pickit\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'pickit';

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->api = $this->_objectManager->create('Doublepoint\Pickit\Model\Apicall');
        $this->helper = $this->_objectManager->create('Doublepoint\Pickit\Helper\Data');
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $datos = $this->getShippingDetails($request);

        // Seteamos las reglas
        if(isset($datos["freeBoxes"]))
            $this->setFreeBoxes($datos["freeBoxes"]);

        $datos = $this->completePersonalData($datos, $request);

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $method = $this->_getPrecioPickit($datos, $request);
        $result->append($method);
        return $result;
    }

    public function getShippingDetails($ship){
        $masAlto                    = 0;
        $masAncho                   = 0;
        $largoTotal                 = 0;
        $datos["peso"]              = 0;
        $datos["valorDeclarado"]    = 0;
        $datos["volumen"]           = 0;
        $datos["freeBoxes"]         = 0;
        $datos["bultos"]            = 0;
        $sku                        = '';
        //$tipoPedido               = $this->getConfigData('paquete_tipo');

        // Tomamos el attr "medida" segun la configuracion del cliente
        if ($this->getConfigData('global_medida') == "") {
            $datos["medida"] = "gramos";
        } else {
            $datos["medida"] = $this->getConfigData('global_medida');
        }
        if ($datos["medida"] == "kilos") {
            $datos["medida"] = 1;
        } elseif ($datos["medida"] == "gramos") {
            $datos["medida"] = 1000;
        } else {
            $datos["medida"] = 1000;
        }

        foreach ($ship->getAllItems() as $_item) {
            if($sku != $_item->getSku()) {
                $sku                     = $_item->getSku();
                $price                   = floor($_item->getPrice());
                //Obtengo el atributo que usamos para peso de producto.
                $atributo_peso           = $this->getConfigData('atributo_peso');
                $datos["peso"]           = ($_item->getQty() * $_item->getData($atributo_peso) / $datos["medida"]) + $datos["peso"];
                $datos["valorDeclarado"] = ($_item->getQty() * $price) + $datos["valorDeclarado"];
                
                /*$product    = $this->productRepository->loadByAttribute('sku', $_item->getSku(), array('paquete_largo','paquete_ancho','paquete_alto','cantidad_bultos'));
                $pkgQty     = (float)$product->getData('cantidad_bultos');
                $datos["bultos"] = $datos["bultos"] + ($pkgQty) ? $pkgQty : 1;*/
                
                // Si la condicion de free shipping está seteada en el producto
                if ($_item->getFreeShippingDiscount() && !$_item->getProduct()->isVirtual()) {
                    $datos["freeBoxes"] += $_item->getQty();
                }
            }
        }
        $datos["volumen"] = $masAlto * $masAncho * $largoTotal;
        return $datos; 
    }

    public function completePersonalData($datos, $request){
        $quote  = $this->cart->getQuote();
        $shippingAddress        = $quote->getShippingAddress();
        $datos["cpDestino"]     = $request->getDestPostcode();
        $datos["localidad"]     = $request->getDestCity();
        $datos["provincia"]     = $request->getDestRegionCode();
        $datos["direccion"]     = $request->getDestStreet();
        $datos["nombre"]        = $shippingAddress->getData('firstname');
        $datos["apellido"]      = $shippingAddress->getData('lastname');
        $datos["telefono"]      = $shippingAddress->getData('telephone');
        $datos["email"]         = $shippingAddress->getData('email');
        return $datos; 
    }

    protected function _getPrecioPickit($datos, $request){
        $method = $this->_rateMethodFactory->create();
        $price = 0;
        $sucursal = '';
        $methodTitle = ''; 

        if($this->_checkoutSession->getPickitActivo()) {
            $response = $this->_checkoutSession->getInfoPuntoSeleccionado();
            $this->updateShippingAddress($response["Response"]["PuntoPickit"]);
            $price = $this->calculateShippingPrice($response);
            $sucursal = $response["Response"]["PuntoPickit"]["Nombre"].', '.$response["Response"]["PuntoPickit"]["Cadena"].' / '.$response["Response"]["PuntoPickit"]["Direccion"].' / '.$response["Response"]["PuntoPickit"]["CodigoPostal"].' / '.$response["Response"]["PuntoPickit"]["Telefono"];
            $datos = $this->completeSucursalData($datos, $response, $price, $sucursal);
            //$methodTitle = $this->getConfigData('name') . ' / Sucursal: '. $sucursal;  
            $methodTitle = ' Envío a punto: '. $sucursal;
        }

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($methodTitle);
        $method->setPrice($price);
        $method->setCost($price);

        $this->_checkoutSession->setPickitData($datos);
        return $method;
    }

    public function updateShippingAddress($puntoPickit){
        $address = $this->helper->generateAddressArray($puntoPickit);
        $quote = $this->_checkoutSession->getQuote();
        $shipAddress = $quote->getShippingAddress();
		$shipAddress->setCountryId($address["countryId"]);
		$shipAddress->setTelephone($address["telephone"]);
		$shipAddress->setRegion($address["region"]);
		$shipAddress->setRegionCode($address["regionCode"]);
		$shipAddress->setRegionId($address["regionId"]);
		$shipAddress->setPostcode($address["postcode"]);
		$shipAddress->setStreet($address["street"]);
		$shipAddress->setCity($address["city"]);
        $shipAddress->save();
	}

    public function calculateShippingPrice($response){
        //$config_precio == 'automatico'
        $price = $response["Response"]["ValorTransaccion"];
        //Obtenemos configuracion pickit para setear el precio.
        $config_precio = $this->getConfigData('precio');
        if ($config_precio == 'fijo'){
            $price = $this->getConfigData('preciofijo');
        }
        if ($config_precio == 'porcentaje'){
            $price = $price * $this->getConfigData('regla') / 100;
        }
        return $price; 
    }

    public function completeSucursalData($datos, $response, $price, $sucursal){
        $datos["precio"] = $price;
        $datos["sucursal_nombre"]    = $response["Response"]["PuntoPickit"]["Nombre"];
        $datos["sucursal_cadena"]    = $response["Response"]["PuntoPickit"]["Cadena"];
        $datos["sucursal_direccion"] = $response["Response"]["PuntoPickit"]["Direccion"];
        $datos["sucursal_cp"]        = $response["Response"]["PuntoPickit"]["CodigoPostal"];
        $datos["sucursal_tel"]       = $response["Response"]["PuntoPickit"]["Telefono"];
        $datos["sucursal_retiro"]    = $sucursal;
        return $datos; 
    }
}