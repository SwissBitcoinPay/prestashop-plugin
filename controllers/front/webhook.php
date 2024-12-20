<?php

class swissbitcoinpaywebhookModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function postProcess()
    {
        $step = 0;
        $jsonStr = '';

        try {
            $SwissBtcPaySig = $_SERVER['HTTP_SBP_SIG'] ?? null;
            if (empty($SwissBtcPaySig)) {
                $this->logError("Secret key not set");
                http_response_code(400);
                die('Secret key not set');
            }

            $step++;
            $jsonStr = Tools::file_get_contents('php://input');
        	$this->logError($jsonStr);
            $jsonData = json_decode($jsonStr, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logError("Invalid JSON payload");
                http_response_code(400);
                die('Invalid JSON payload');
            }

            $step++;
            $SwissSecret = explode('=', $SwissBtcPaySig)[1];
            if (!$this->checkSecretKey(Configuration::get('SWISS_BITCOIN_PAY_API_SECRET'), $jsonStr, $SwissSecret)) {
                $this->logError("Invalid signature");
                http_response_code(403);
                die('Invalid signature');
            }

            $step++;
            /*$isPaid = $jsonData['isPaid'] ?? false;
            $isExpired = $jsonData['isExpired'] ?? false;*/
            $isPaid = $jsonData['status'] === 'settled' ?? false;
            $isExpired = $jsonData['status'] === 'expired' ?? false;
        	$isUnconfirmed = $jsonData['status'] === 'unconfirmed' ?? false;

            $step++;
        	$orderID = $jsonData['extra']['orderID'];
        	$cartID = $jsonData['extra']['cartID'];
        
            $order = new Order($orderID);
            if (!Validate::isLoadedObject($order)) {
                $cart = new Cart($cartID);
                if (!Validate::isLoadedObject($cart)) {
                    $this->logError("Cart not found " . $cartID);
                    http_response_code(404);
                    die('Cart not found ' . $cartID);
                }

                $customer = new Customer($cart->id_customer);
                $currency = new Currency($cart->id_currency);
                $paymentModule = Module::getInstanceByName('swissbitcoinpay');

                $paymentModule->validateOrder(
                    $cart->id,
                    Configuration::get('PS_OS_PAYMENT'),
                    $amount,
                    'Swiss Bitcoin Pay',
                    null,
                    [],
                    (int)$currency->id,
                    false,
                    $customer->secure_key
                );

                $order = Order::getByCartId($cart->id);
            }

            $step++;        
            if ($isPaid) {
                $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
            } elseif ($isUnconfirmed && $order->current_state !== Configuration::get('PS_OS_PAYMENT')) {
                $order->setCurrentState(Configuration::get('PS_OS_BANKWIRE'));
            } elseif ($isExpired && $order->current_state !== Configuration::get('PS_OS_PAYMENT')) {
                $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
            }

            $order->addOrderPayment(
                new OrderPayment([
                    'order_reference' => $orderGuid,
                    'amount' => $jsonData['amount'],
                    'payment_method' => 'Swiss Bitcoin Pay'
                ])
            );

            $step++;
            $order->save();

            http_response_code(200);
            die('OK');
        } catch (Exception $e) {
            $this->logError("Step: $step - $jsonStr - " . $e->getMessage());
            http_response_code(500);
            die('Error processing webhook');
        }
    }

	private function checkSecretKey($key, $message, $signature)
	{
    	$hashBytes = hash_hmac('sha256', $message, $key, true);
    	$hashString = '';
    	foreach (str_split($hashBytes) as $byte) {
        	$hashString .= sprintf('%02x', ord($byte));
    	}
    	return hash_equals($hashString, $signature);
	}

    private function logError($message)
    {
        PrestaShopLogger::addLog($message, 3);
    }
}
