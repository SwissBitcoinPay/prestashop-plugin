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
{if $status == 'ok'}
    <p class="payment-success">{l s='Your payment has been successfully completed. Thank you for your order!!' mod='swissbitcoinpay'}</p>
{else}
    <p class="payment-error">{l s='Payment could not be completed. Please try again.' mod='swissbitcoinpay'}</p>
{/if}
