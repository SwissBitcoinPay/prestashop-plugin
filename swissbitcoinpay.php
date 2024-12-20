<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class SwissBitcoinPay extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'swissbitcoinpay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Nisaba';
        $this->controllers = ['validation'];
        $this->is_eu_compatible = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Swiss Bitcoin Pay');
        $this->description = $this->l('Payment in bitcoins.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function install()
    {
        return parent::install()
        	&& Configuration::updateValue('SWISS_BITCOIN_PAY_API_KEY', '')
        	&& Configuration::updateValue('SWISS_BITCOIN_PAY_API_URL', 'https://api.swiss-bitcoin-pay.ch')
        	&& Configuration::updateValue('SWISS_BITCOIN_PAY_API_SECRET', '')
        	&& Configuration::updateValue('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN', false)
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn');
    }

    public function uninstall()
    {
        return parent::uninstall()
	        && Configuration::deleteByName('SWISS_BITCOIN_PAY_API_URL')
	        && Configuration::deleteByName('SWISS_BITCOIN_PAY_API_SECRET')
	        && Configuration::deleteByName('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN')
	    	&& Configuration::deleteByName('SWISS_BITCOIN_PAY_API_KEY');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return [];
        }

        $paymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $paymentOption->setCallToActionText($this->l('Pay with Bitcoin'))
                      ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
                      ->setAdditionalInformation($this->context->smarty->fetch('module:swissbitcoinpay/views/templates/front/payment_infos.tpl'));

        return [$paymentOption];
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $state = $params['order']->getCurrentState();
        if (in_array($state, [Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_OUTOFSTOCK')])) {
            $this->smarty->assign('status', 'ok');
        } else {
            $this->smarty->assign('status', 'failed');
        }

        return $this->fetch('module:swissbitcoinpay/views/templates/hook/payment_return.tpl');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitSwissBitcoinPaySettings')) {
            $apiKey = Tools::getValue('SWISS_BITCOIN_PAY_API_KEY');
            if ($apiKey && Validate::isGenericName($apiKey)) {
                Configuration::updateValue('SWISS_BITCOIN_PAY_API_KEY', $apiKey);
                $output .= $this->displayConfirmation($this->l('Settings updated.'));
            } else {
                $output .= $this->displayError($this->l('Invalid API Key.'));
            }
        	Configuration::updateValue('SWISS_BITCOIN_PAY_API_URL', Tools::getValue('SWISS_BITCOIN_PAY_API_URL'));
            Configuration::updateValue('SWISS_BITCOIN_PAY_API_SECRET', Tools::getValue('SWISS_BITCOIN_PAY_API_SECRET'));
            Configuration::updateValue('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN', Tools::getValue('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN') ? true : false);
        }

        // Assign variables for Smarty
        $this->smarty->assign([
            'api_key' => Configuration::get('SWISS_BITCOIN_PAY_API_KEY'),
            'api_url' => Configuration::get('SWISS_BITCOIN_PAY_API_URL'),
            'api_secret' => Configuration::get('SWISS_BITCOIN_PAY_API_SECRET'),
            'accept_onchain' => Configuration::get('SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN'),
            'module_name' => $this->name,
            'link' => $this->context->link,
        ]);

        // Return the form rendered by configure.tpl
        return $output . $this->fetch('module:swissbitcoinpay/views/templates/admin/configure.tpl');
    }

}
