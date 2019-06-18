<?php
namespace Amrendra\CustomCatalog\Model;
use Amrendra\CustomCatalog\Api\ProductInterface;
 
class productmodel implements ProductInterface
{

    private $publisher;
    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     */
    public function __construct(\Magento\Framework\MessageQueue\PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Product name.
     * @return string Greeting message with product name.
     */
    public function updateProduct() {

        try {
            $params = (array) json_decode(file_get_contents('php://input'), TRUE);

            $this->publisher->publish('amrendra.product.update', json_encode($params));
            $result = ['msg' => 'Your Request is added to RabbitMQ'];
            return $result;
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage()];
            return $result;
        }
    }

}