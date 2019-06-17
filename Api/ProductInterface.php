<?php
namespace Amrendra\CustomCatalog\Api;
 
interface ProductInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $vpn Product data.
     * @return string Greeting message with Product data.
     */
    public function updateProduct();
}
