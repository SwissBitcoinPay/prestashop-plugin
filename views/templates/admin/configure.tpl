<div class="panel">
    <h3>{$module_name|escape} - Configuration</h3>
	<br/>
    <form method="post" action="{$link->getAdminLink('AdminModules')|escape:'html'}&configure={$module_name}" class="form">
        <div class="form-group">
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
