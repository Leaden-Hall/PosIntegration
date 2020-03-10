<?php

namespace Petswonderland\PosIntegration\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Zend_Http_Client;
use Zend_Http_Response;
use Magento\Customer\Model\Customer;

/**
 * Class Request
 * @package Petswonderland\PosIntegration\Helper
 */
class Request extends AbstractData
{
    const ENDPOINT = 'https://api.xilnex.com/logic/v2/clients/';
    const XILNEX_ID = 'xilnexid';

    /**
     * @var CurlFactory
     */
    protected $_curlFactory;

    /**
     * Request constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CurlFactory $curlFactory
    ) {
        $this->_curlFactory = $curlFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $customerEmail
     * @return int
     */
    public function checkPosEmail($customerEmail)
    {
        $url = self::ENDPOINT . 'query?code=' . $customerEmail;
        $response = $this->sendCurl(Zend_Http_Client::GET, $url, $this->getHeader());

        if (isset($response['data']['clients']) && count($response['data']['clients'])) {
            return (int) $response['data']['clients'][0]['id'];
        }

        return 0;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        $header = 'header';
        $token = 'token';

        return [
            'Accept: application/json',
            'Header: ' . $header,
            'Token: ' . $token
        ];
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $header
     * @param string $body
     *
     * @return array
     */
    public function sendCurl($method, $url, $header, $body = '')
    {
        $curl = $this->_curlFactory->create();
        $curl->setConfig(['timeout' => 120, 'verifyhost' => 2]);

        $curl->write($method, $url, Zend_Http_Client::HTTP_1, $header, $body);
        $response = $curl->read();
        $curl->close();

        return self::jsonDecode(Zend_Http_Response::extractBody($response));
    }

    /**
     * @param Customer $customer
     * @return int
     * @throws LocalizedException
     */
    public function createCustomer($customer)
    {
        $url = self::ENDPOINT . 'client';
        $body = $this->getCustomerBody($customer);
        $response = $this->sendCurl(Zend_Http_Client::POST, $url, $this->getHeader(), $body);

        return isset($response['data']['client']['id']) ? (int) $response['data']['client']['id'] : 0;
    }

    /**
     * @param Customer $customer
     * @return int
     * @throws LocalizedException
     */
    public function updateCustomer($customer)
    {
        $url = self::ENDPOINT . 'clients/' . $customer->getData(self::XILNEX_ID);
        $body = $this->getCustomerBody($customer, false);
        $response = $this->sendCurl(Zend_Http_Client::PUT, $url, $this->getHeader(), $body);

        return isset($response['client']['id']) ? (int) $response['client']['id'] : 0;
    }

    /**
     * @param Customer $customer
     * @param bool $isNew
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerBody($customer, $isNew = true)
    {
        $billAddress = $customer->getDefaultBillingAddress();
        $shipAddress = $customer->getDefaultShippingAddress();

        $data = [
            'id' => 0,
            'alternateLookup' => null,
            'priceScheme' => null,
            'terms' => 0,
            'creditLimit' => 0,
            'billing' => [
                'street' => is_array($billAddress->getStreet()) && isset($billAddress->getStreet()[0])
                    ? $billAddress->getStreet()[0]
                    : $billAddress->getStreet(),
                'city' => $billAddress->getCity(),
                'state' => $billAddress->getRegion()
            ],
            'shipping' => [
                'street' => is_array($shipAddress->getStreet()) && isset($shipAddress->getStreet()[0])
                    ? $shipAddress->getStreet()[0]
                    : $shipAddress->getStreet(),
                'city' => $shipAddress->getCity(),
                'state' => $shipAddress->getRegion(),
                'zipcode' => $shipAddress->getPostcode(),
                'country' => $shipAddress->getCountry()
            ],
            'name' => $customer->getName(),
            'code' => '',
            'title' => $customer->getDataByKey('prefix'),
            'email' => $customer->getEmail(),
            'type' => 'Member',
            'group' => '',
            'registrationCode' => $customer->getDataByKey('ic'),
            'gender' => $customer->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender')),
            'ic' => '',
            'nationality' => '',
            'createdBy' => 'Online',
            'mobile' => $customer->getDataByKey('mobile'),
            'office' => '',
            'fax' => '',
            'phone' => '',
            'alternateContact' => '',
            'alternatePhone' => '',
            'category' => 'Personal',
            'firstName' => $customer->getDataByKey('firstname'),
            'lastName' => $customer->getDataByKey('lastname'),
        ];

        if ($isNew) {
            $data['billing']['zipcode'] = $billAddress->getPostcode();
            $data['billing']['country'] = $billAddress->getCountry();
            $data['accountNumber'] = '';
            $data['paymentTerms'] = 0;
            $data['paymentTermsRemark'] = '';
            $data['expiryDate'] = '0001-01-01T00:00:00.000Z';
            $data['pointValue'] = 0;
            $data['image'] = '';
            $data['active'] = 'true';
            $data['createdOutlet'] = 'Main Outlet';
            $data['defaultSalesType'] = 'Retail';
        }

        return self::jsonEncode(['client' => $data]);
    }
}
