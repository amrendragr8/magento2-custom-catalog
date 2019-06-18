<?php

namespace Amrendra\CustomCatalog\Model\Product;

use Magento\Framework\App\ResourceConnection;

use Magento\Framework\MessageQueue\MessageLockException;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\MessageQueue\CallbackInvoker;
use Magento\Framework\MessageQueue\ConsumerConfigurationInterface;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\QueueInterface;
use Magento\Framework\MessageQueue\LockInterface;
use Magento\Framework\MessageQueue\MessageController;
use Magento\Framework\MessageQueue\ConsumerInterface;
 
/**
 * Class Consumer used to process OperationInterface messages.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateConsumer implements ConsumerInterface
{
    /**
     * @var \Magento\Framework\MessageQueue\CallbackInvoker
     */
    private $invoker;
 
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;
 
    /**
     * @var \Magento\Framework\MessageQueue\ConsumerConfigurationInterface
     */
    private $configuration;
 
    /**
     * @var \Magento\Framework\MessageQueue\MessageController
     */
    private $messageController;
 
    /**
     * @var LoggerInterface
     */
    
 
    /**
     * @var OperationProcessor
     */
    private $operationProcessor;
 
    /**
     * Initialize dependencies.
     *
     * @param CallbackInvoker $invoker
     * @param ResourceConnection $resource
     * @param MessageController $messageController
     * @param ConsumerConfigurationInterface $configuration
     * @param OperationProcessorFactory $operationProcessorFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CallbackInvoker $invoker,
        ResourceConnection $resource,
        MessageController $messageController,
        ConsumerConfigurationInterface $configuration,
        \Amrendra\CustomCatalog\Model\ProcessQueueMsg $processQueueMsg
        
    ) {
        $this->invoker = $invoker;
        $this->resource = $resource;
        $this->messageController = $messageController;
        $this->configuration = $configuration;
        $this->processQueueMsg = $processQueueMsg;
        
    }
 
    /**
     * {@inheritdoc}
     */
    public function process($maxNumberOfMessages = null)
    {
        $queue = $this->configuration->getQueue();
        if (!isset($maxNumberOfMessages)) {
            $queue->subscribe($this->getTransactionCallback($queue));
        } else {
            $this->invoker->invoke($queue, $maxNumberOfMessages, $this->getTransactionCallback($queue));
        }
    }
 
    /**
     * Get transaction callback. This handles the case of async.
     *
     * @param QueueInterface $queue
     * @return \Closure
     */
    private function getTransactionCallback(QueueInterface $queue)
    {
        return function (EnvelopeInterface $message) use ($queue) {
            /** @var LockInterface $lock */
            $lock = null;
            try {
                $lock = $this->messageController->lock($message, $this->configuration->getConsumerName());
                $message1 = $message->getBody();
                /**
                 * $this->processQueueMsg->process() use for process message which you publish in queue
                 */
                $data = $this->processQueueMsg->process($message1);
                if ($data === false) {
                    $queue->reject($message1); // if get error in message process
                }
                $queue->acknowledge($message); // send acknowledge to queue 
            } catch (MessageLockException $exception) {
                $queue->acknowledge($message);
            } catch (ConnectionLostException $e) {
                $queue->acknowledge($message);
                if ($lock) {
                    $this->resource->getConnection()
                        ->delete($this->resource->getTableName('queue_lock'), ['id = ?' => $lock->getId()]);
                }
            } catch (NotFoundException $e) {
                $queue->acknowledge($message);
                
            } catch (\Exception $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
                if ($lock) {
                    $this->resource->getConnection()
                        ->delete($this->resource->getTableName('queue_lock'), ['id = ?' => $lock->getId()]);
                }
            }
        };
    }
}