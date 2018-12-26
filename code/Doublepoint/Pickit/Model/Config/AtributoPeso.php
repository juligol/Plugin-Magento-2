<?php
namespace  Doublepoint\Pickit\Model\Config;
class AtributoPeso
{
   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->get('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
		$productAttrs = $model->getCollection();
		foreach ($productAttrs as $prodattr){
            if ($prodattr->getFrontendLabel()){
                $_attributes[$prodattr->getName()] = $prodattr->getFrontendLabel();
            }else{
                $_attributes[$prodattr->getName()] = $prodattr->getName();
			}
		}
        return $_attributes;
    }
}
