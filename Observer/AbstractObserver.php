<?php

namespace Petswonderland\PosIntegration\Observer;

use Exception;
use Magento\Customer\Model\CustomerFactory;
use Petswonderland\PosIntegration\Setup\InstallData as Setup;

/**
 * Class AbstractObserver
 * @package Petswonderland\PosIntegration\Observer
 */
abstract class AbstractObserver
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * CustomerFlag constructor.
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerFactory $customerFactory
    ) {
        $this->_customerFactory = $customerFactory;
    }

    /**
     * @param string|int $customerId
     * @throws Exception
     */
    public function raiseSyncFlag($customerId)
    {
        $customerFactory = $this->_customerFactory->create();
        $customer = $customerFactory->load($customerId);
        if ((int) $customer->getData(Setup::POS_FLAG) === 0) {
            $customer->setData(Setup::POS_FLAG, 1);
            $customer->save();
        }
    }
}
