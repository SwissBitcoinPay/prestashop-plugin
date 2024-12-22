<?php
/**
 * Copyright (c) 2024 Swiss Bitcoin Pay (https://swiss-bitcoin-pay.ch)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Swiss Bitcoin Pay <https://swiss-bitcoin-pay.ch>
 * @copyright 2024 Swiss Bitcoin Pay
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
if (!defined('_PS_VERSION_')) {
	exit;
}

class SwissBitcoinPayValidationModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
    	parent::initContent();
    	$this->setTemplate('module:swissbitcoinpay/views/templates/front/empty.tpl');
	}
	
    public function postProcess()
    {
		try {
        	$apiKey = Configuration::get('SWISS_BITCOIN_PAY_API_KEY');
        	if (!$apiKey) {
       			die($this->module->l('API key is not configured !', 'validation'));
        	}

        	$cart = $this->context->cart;
        	if (!$cart->id) {
            	Tools::redirect('index.php?controller=order');
        	}
    		$orderId = Order::getIdByCartId($cart->id);
    		if (!$orderId) {
        		$orderId = $cart->id . '-' . time();
    		}		
    		$storeName = Configuration::get('PS_SHOP_NAME');
        	$customer = new Customer($cart->id_customer);
        	$currency = new Currency($cart->id_currency);
        	$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

			$settings = [
    			"ApiUrl" => Configuration::get('SWISS_BITCOIN_PAY_API_URL'),
    			"ApiKey" => $apiKey,
    			"AcceptOnChain" => Configuration::get('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN')
			];
        
        	
			$returnUrl = '';
			if ($customer->is_guest) {
            $returnUrl = $this->context->link->getPageLink(
        			'guest-tracking',
       				true,
        			null,
        			[
            			'id_order' => $orderId,
            			'email' => $customer->email
        			]
    			);
			} else {
			    $returnUrl = $this->context->link->getPageLink(
        			'order-confirmation',
        			true,
        			null,
        			[
            			'id_cart' => $cart->id,
            			'id_module' => $this->module->id,
            			'id_order' => $orderId,
            			'key' => $customer->secure_key
        			]
    			);
			}
        
			$paymentData = [
    			"Description" => "From " . $storeName,
            	"BuyerName" => $customer->firstname . ' ' . $customer->lastname,
            	"OrderID" => $orderId,
            	"CartID" => $cart->id,
    			"CurrencyCode" => $currency->iso_code,
    			"Amount" => $total,
    			"BuyerEmail" => $customer->email,
    			"Lang" => $this->context->language->iso_code,
    			"RedirectionURL" => $returnUrl,
    			"WebHookURL" => $this->context->link->getModuleLink(
    				'swissbitcoinpay', 
    				'webhook',  
    				[],       
    				true 
				)
			];

    		$checkoutUrl = $this->createInvoice($settings, $paymentData);
    		Tools::redirect($checkoutUrl);
		} catch (Exception $e) {
    		echo "Error: " . $e->getMessage();
		}
    }


	private function createInvoice($settings, $paymentData)
	{
		try {
			$invoice = [
				"title" => $paymentData["Description"],
				"description" => $paymentData["BuyerName"] . " | Order : " . $paymentData["OrderID"],
				"unit" => $paymentData["CurrencyCode"],
				"amount" => $paymentData["Amount"],
				"email" => $paymentData["BuyerEmail"],
				"emailLanguage" => $paymentData["Lang"],
				"redirectAfterPaid" => $paymentData["RedirectionURL"],
				"webhook" => $paymentData["WebHookURL"],
				"delay" => 60,
				"onChain" => $settings["AcceptOnChain"],
				"extra" => [
					"customNote" => "Order " . $paymentData["OrderID"],
                	"orderID" => $paymentData["OrderID"],
                	"cartID" =>  $paymentData["CartID"]
				]
			];

			$invoiceJson = json_encode($invoice, JSON_UNESCAPED_UNICODE);

			$sUrl = rtrim($settings["ApiUrl"], '/') . '/checkout';

			// Créer une requête HTTP POST
			$headers = [
				'Content-Type: application/json',
				'api-key: ' . $settings["ApiKey"]
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $sUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $invoiceJson);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($httpCode !== 200 && $httpCode !== 201) {
				throw new Exception("HTTP Request failed with code " . $httpCode);
			}

			curl_close($ch);

			$jsonRep = json_decode($response, true);

			return $jsonRep["checkoutUrl"];

		} catch (Exception $e) {
			throw $e;
		}
	}

}
