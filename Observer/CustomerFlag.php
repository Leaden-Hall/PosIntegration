<?php

namespace Petswonderland\PosIntegration\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerUpdateFlag
 * @package Petswonderland\PosIntegration\Observer
 */
class CustomerFlag extends AbstractObserver implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var CustomerInterface $savedCustomer */
        $savedCustomer = $observer->getEvent()->getDataByKey('customer_data_object');
        $this->raiseSyncFlag($savedCustomer->getId());
    }
}
