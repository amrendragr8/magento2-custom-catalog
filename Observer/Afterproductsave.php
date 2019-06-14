<?php

namespace Amrendra\CustomCatalog\Observer;

use Magento\Framework\Event\ObserverInterface;

class Afterproductsave implements ObserverInterface
{    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $sku     = $product->getSku(); 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $catalog_model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog')->getCollection()->addFieldToFilter('product_id',$product->getId());
        
        foreach ($catalog_model as $catalog) {
            $catalog_id = $catalog->getId();
        }
        $model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog');
        if (count($catalog_model) == 0) {
        	
			$model->setProductId($product->getId())
			       ->setProductSku($sku)
			       ->setCopyWriteInfo($product->getCopywriteinfo())
			       ->setVpn($product->getVpn());
			$model->save();

        }else{
        	$model->load($catalog_id);
        	$model->setProductId($product->getId())
		       ->setProductSku($sku)
		       ->setCopyWriteInfo($product->getCopywriteinfo())
		       ->setVpn($product->getVpn());
			$model->save();
        }

    }   
}