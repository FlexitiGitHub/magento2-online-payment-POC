<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SamplePaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\SamplePaymentGateway\Gateway\Http\Client\ClientMock;
use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'sample_gateway';
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
    protected $curl;

    public function __construct(
    ScopeConfig $scopeConfig,
     \Magento\Framework\HTTP\Client\Curl $curl
    ) {
    $this->scopeConfig = $scopeConfig;

    $this->_curl = $curl;
    }
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */


    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $url_prefix = 'https://oauth-uat.flexiti.fi';
        $url = $url_prefix . '/flexiti/online-api/oauth/token';
        $username = $this->scopeConfig->getValue('payment/sample_gateway/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $password = $this->scopeConfig->getValue('payment/sample_gateway/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $client_id = $this->scopeConfig->getValue('payment/sample_gateway/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $auth_token = $this->scopeConfig->getValue('payment/sample_gateway/merchant_gateway_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic '. $auth_token ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=password&username=".$username."&password=".$password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url_prefix . '/flexiti/online-api/online/client-id/' . $client_id . '/systems/init');
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer '. json_decode($response)->access_token ));
        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, 'amount_requested=123&email=test@gmail.com&fname=test&mname=test&lname=test&city=Ontario&postal_code=m1m1m1' );
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $response2  = curl_exec($ch2);
        curl_close($ch2);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 

        // retrieve quote items array
        $items = $cart->getQuote()->getAllItems();


        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ],
                    'redirect_url' => json_decode($response2),
                    'total_amount' => $cart->getQuote()->getGrandTotal() 
                ]   
            ]
        ];
    }
}
