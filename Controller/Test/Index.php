<?php

namespace Petswonderland\PosIntegration\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Mageplaza\Core\Helper\AbstractData;
use Petswonderland\PosIntegration\Helper\Request as HelperRequest;

/**
 * Class Index
 * @package Petswonderland\PosIntegration\Controller\Test
 */
class Index extends Action
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var HelperRequest
     */
    protected $_helperRequest;

    /**
     * Index constructor.
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param HelperRequest $helperRequest
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        CollectionFactory $customerCollectionFactory,
        HelperRequest $helperRequest
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_helperRequest = $helperRequest;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
//        $customerFactory = $this->_customerFactory->create();
//        echo $this->_helperRequest->getCustomerBody($customerFactory->load(1));
//
//        die;

        $a = '{
          "ok": true,
          "status": "SuccessQuery",
          "warning": null,
          "error": null,
          "data": {
            "client": null,
            "clients": [
              {
                "id": 5042,
                "alternateLookup": "",
                "priceScheme": "0",
                "terms": 0,
                "creditLimit": 0,
                "billingAddress": "",
                "shippingAddress": "",
                "billing": {
                  "street": "",
                  "city": "",
                  "state": "",
                  "zipcode": "",
                  "country": ""
                },
                "shipping": {
                  "street": "",
                  "city": "",
                  "state": "",
                  "zipcode": "",
                  "country": ""
                },
                "name": "Cindy E. Bilodeau",
                "code": "12052016",
                "title": "",
                "email": "",
                "type": "",
                "group": "",
                "registrationCode": "",
                "gender": "female",
                "dob": "",
                "ic": "",
                "nationality": null,
                "createdBy": "realmart@xilnex.com",
                "mobile": null,
                "office": null,
                "fax": null,
                "phone": null,
                "alternateContact": null,
                "alternatePhone": null,
                "category": null,
                "firstName": null,
                "lastName": null,
                "expiryDate": "0001-01-01T00:00:00.000Z",
                "pointValue": 128,
                "image": "",
                "active": false,
                "xilnexConnectOutlet": null,
                "paymentTerms": 0,
                "paymentTermsRemark": null,
                "gstNo": null,
                "currencyCode": null,
                "gstNumber": null,
                "taxCode": null,
                "createDate": null,
                "enableDOB": false,
                "updateTimeStamp": "0x00000000B42AD5FD",
                "individualDiscount": 0,
                "listBranchClients": [],
                "customFieldValue1": null,
                "customFieldValue2": null,
                "customFieldValue3": null,
                "customFieldValue4": null,
                "customFieldValue5": null,
                "customFieldValue6": null,
                "customFieldValue7": null,
                "customFieldValue8": null,
                "customFieldValue9": null,
                "customFieldValue10": null,
                "customFieldValue11": null,
                "customFieldValue12": null,
                "customFieldValue13": null,
                "customFieldValue14": null,
                "customFieldValue15": null,
                "verified": false
              }
            ]
          },
          "timestamp": 1487901986
        }';

        $b = AbstractData::jsonDecode($a);
        var_dump($b['data']['clients'][0]);
    }
}
