<?php

namespace Petswonderland\PosIntegration\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Address;

/**
 * Class CustomerAddressUpdateFlag
 * @package Petswonderland\PosIntegration\Observer
 */
class CustomerAddressFlag extends AbstractObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Address $customerAddress */
        $customerAddress = $observer->getEvent()->getDataByKey('customer_address');
        $this->raiseSyncFlag($customerAddress->getCustomer()->getId());
    }
}
