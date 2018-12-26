<?php
namespace Doublepoint\Pickit\Model\Plugin\Checkout;

class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout) 
	{
		/*$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['dni'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'options' => [],
                'id' => 'dni'
            ],
            'dataScope' => 'shippingAddress.custom_attributes.dni',
            'label' => 'DNI',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => true],
            'sortOrder' => 50,
            'id' => 'dni'
		];*/
		
		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
			['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id'] = [
			'component' => 'Magento_Ui/js/form/element/abstract',
			'config' => [
				'customScope' => 'shippingAddress',
				'template' => 'ui/form/field',
				'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => 'shippingAddress.vat_id',
            'label' => __('VAT Number'),
            'provider' => 'checkoutProvider',
            'visible' => true,
			'validation' => ['required-entry' => true],
			'sortOrder' => 50
		];
		
		return $jsLayout;
    }
}