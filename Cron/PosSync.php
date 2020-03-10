<?php

namespace Petswonderland\PosIntegration\Cron;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Petswonderland\PosIntegration\Helper\Request as HelperRequest;
use Petswonderland\PosIntegration\Setup\InstallData as Setup;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Class PosSync
 * @package Petswonderland\PosIntegration\Cron
 */
class PosSync
{
    /**
     * @var CustomerCollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var HelperRequest
     */
    protected $_helperRequest;

    /**
     * @var Customer[]
     */
    protected $_updateCustomers = [];

    /**
     * @var Customer[]
     */
    protected $_createCustomers = [];

    /**
     * PosSync constructor.
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param HelperRequest $helperRequest
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        HelperRequest $helperRequest
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_helperRequest = $helperRequest;
    }

    /**
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute()
    {
        $syncedCustomers = [];
        $failedCustomers = [];
        $this->setCustomerCollection();

        if (!empty($this->_updateCustomers)) {
            foreach ($this->_updateCustomers as $updateCustomer) {
                $updatedId = $this->_helperRequest->updateCustomer($updateCustomer);
                $updatedId
                    ? $syncedCustomers[] = $updateCustomer->setData(HelperRequest::XILNEX_ID, $updatedId)
                    : $failedCustomers[] = $updateCustomer->getId();
            }
        }

        if (!empty($this->_createCustomers)) {
            foreach ($this->_createCustomers as $createCustomer) {
                $createId = $this->_helperRequest->createCustomer($createCustomer);
                $createId
                    ? $syncedCustomers[] = $createCustomer->setData(HelperRequest::XILNEX_ID, $createId)
                    : $failedCustomers[] = $createCustomer->getId();
            }
        }

        /** @var Customer $syncedCustomer */
        foreach ($syncedCustomers as $syncedCustomer) {
            $syncedCustomer->setData(Setup::POS_FLAG, 0);
            $syncedCustomer->save();
        }

        $writer = new Stream(BP . '/var/log/PosIntegration.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info('Synced Successfully: ' . count($syncedCustomers));
        $logger->info('Synced Unsuccessfully: ' . count($failedCustomers));
    }

    public function setCustomerCollection()
    {
        $customerCollection = $this->_customerCollectionFactory->create();
        $syncCustomers = $customerCollection->addFieldToFilter(Setup::POS_FLAG, [['eq' => 1], ['null' => true]]);

        /** @var Customer $syncCustomer */
        foreach ($syncCustomers as $syncCustomer) {
            if ($syncId = $this->_helperRequest->checkPosEmail($syncCustomer->getEmail())) {
                $this->_updateCustomers[] = $syncCustomer->setData(HelperRequest::XILNEX_ID, $syncId);
            } else {
                $this->_createCustomers[] = $syncCustomer;
            }
        }
    }
}
