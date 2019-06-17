<?php
namespace Amrendra\CustomCatalog\Model;
use Amrendra\CustomCatalog\Api\ProductInterface;
 
class productmodel implements ProductInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Product name.
     * @return string Greeting message with product name.
     */
    public function updateProduct() {
        $params = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result = $this->updateProductByEntity($params); 
        if ($result) {
            return "Product with the Id ".$params['entity_id']." Updated Successfully";
         }else{
            return "There is something problem. Please try again later";
         } 
    }


    public function updateProductByEntity($params)
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       try {
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($params['entity_id']);
            $product->setCopywriteinfo($params['copywrite_info']);
            $product->setVpn($params['vpn']);
            if ($product->save()) {
                $catalog_model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog')->getCollection()->addFieldToFilter('product_id',$params['entity_id']);

                foreach ($catalog_model as $catalog) {
                    $catalog_id = $catalog->getId();
                }
                $model = $objectManager->create('Amrendra\CustomCatalog\Model\CustomCatalog')->load($catalog_id);
                $model->setCopyWriteInfo($params['copywrite_info'])->setVpn($params['vpn']);
                $model->save();
                return true;
            }else{
                return false;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $product = false;
        }
    }

}