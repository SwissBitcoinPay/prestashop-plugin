{if $status == 'ok'}
    <p class="payment-success">{l s='Your payment has been successfully completed. Thank you for your order!!' mod='swissbitcoinpay'}</p>
{else}
    <p class="payment-error">{l s='Payment could not be completed. Please try again.' mod='swissbitcoinpay'}</p>
{/if}
