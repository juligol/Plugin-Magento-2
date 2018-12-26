<?php
namespace  Doublepoint\Pickit\Model\Config;
class IdUsuario
{
   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->get('Magento\Customer\Model\Customer');
        $customer_attributes = $model->getAttributes();
        foreach ($customer_attributes as $attr){
            if ($attr->getFrontendLabel()){
                $_attributes[$attr->getName()] = $attr->getFrontendLabel();
            }else{
                $_attributes[$attr->getName()] = $attr->getName();
			}
        }
        return $_attributes;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            '1' => 'de 8:00 a 17:00 hs',
            '2' => 'de 8:00 a 12:00 hs',
            '3' => 'de 14:00 a 17:00 hs'
        );
    }

}
