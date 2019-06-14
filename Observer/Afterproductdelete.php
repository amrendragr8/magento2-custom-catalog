<?php

namespace Amrendra\CustomCatalog\Observer;

use Magento\Framework\Event\ObserverInterface;

class Afterproductdelete implements ObserverInterface
{    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalog_model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog')->getCollection()->addFieldToFilter('product_id',$product->getId());
        
        foreach ($catalog_model as $catalog) {
            $catalog_id = $catalog->getId();
        }
        if (count($catalog_model) > 0 && $catalog_id > 0) {
            $model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog');
            $model->load($id);
            $model->delete();
        }
    }   
}