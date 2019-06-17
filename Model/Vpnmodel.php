<?php
namespace Amrendra\CustomCatalog\Model;
use Amrendra\CustomCatalog\Api\VpnInterface;
 
class Vpnmodel implements VpnInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Product name.
     * @return string Greeting message with product name.
     */
    public function getVpn($vpn) {
        $data = $this->getProductByVpn($vpn);
        $result = $data->getData();
        return $result;
    }


    public function getProductByVpn($vpn)
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $product = $objectManager->create('Magento\Catalog\Model\Product')->getCollection()->addFieldToFilter('vpn',$vpn);
        return $product;
    }

}