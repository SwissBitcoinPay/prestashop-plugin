{**
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
 *}
{* licence *}
<div class="panel">
    <h3>Swiss Bitcoin Pay - Configuration</h3>
	<br/>
    <form method="post" action="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure={$module_name|escape:'html':'UTF-8'}" class="form">        <div class="form-group">
            <label for="SWISS_BITCOIN_PAY_API_KEY">{l s='API URL' mod='swissbitcoinpay'}</label>
            <input type="text" id="SWISS_BITCOIN_PAY_API_URL" name="SWISS_BITCOIN_PAY_API_URL" 
                   value="{$api_url|escape:'htmlall':'UTF-8'}" class="form-control" required />
        </div>

        <div class="form-group">
            <label for="SWISS_BITCOIN_PAY_API_KEY">{l s='API Key' mod='swissbitcoinpay'}</label>
            <input type="text" id="SWISS_BITCOIN_PAY_API_KEY" name="SWISS_BITCOIN_PAY_API_KEY" 
                   value="{$api_key|escape:'htmlall':'UTF-8'}" class="form-control" required />
        </div>

        <div class="form-group">
            <label for="SWISS_BITCOIN_PAY_API_SECRET">{l s='API Secret' mod='swissbitcoinpay'}</label>
            <input type="password" id="SWISS_BITCOIN_PAY_API_SECRET" name="SWISS_BITCOIN_PAY_API_SECRET" 
                   value="{$api_secret|escape:'htmlall':'UTF-8'}" class="form-control" required />
        </div>

        <div class="form-group">
        	<label for="SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN">Accept On-Chain Payments</label>
            <input type="checkbox" name="SWISS_BITCOIN_PAY_ACCEPT_ONCHAIN" value="1" {if $accept_onchain}checked{/if} />
        </div>
        
        <button type="submit" name="submitSwissBitcoinPaySettings" class="btn btn-primary">
            {l s='Save' mod='swissbitcoinpay'}
        </button>
    </form>
</div>
