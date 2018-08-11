<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_confirmation.php 4067 2006-08-06 07:26:21Z drbyte $
 */

/* 
 *
 * Altered for ORDER DELIVERY DATE contribution
 * Zen Cart Version: 1.3.8a
 * Modification Date: 2008-03-24
 * Author of this modification: MrMeech
 * Previous authors to this contribution are: Peter Martin (pe7er), James Betesh
 * This contribution is licensed under the GNU Public License V2.0
 * http://www.zen-cart.com/license/2_0.txt
 *
 */

define('NAVBAR_TITLE_1', 'Checkout');

//-bof-one_page_checkout-lat9  *** 1 of 1 ***
if (defined ('CHECKOUT_ONE_ENABLED') && CHECKOUT_ONE_ENABLED == 'true') {
    define ('NAVBAR_TITLE_2', 'Review and Submit');
    define ('HEADING_TITLE', 'Review Order Details and Submit Your Order');
} else {
    define('NAVBAR_TITLE_2', 'Confirmation');
    define('HEADING_TITLE', 'Step 3 of 3 - Order Confirmation');
}
//-eof-one_page_checkout-lat9  *** 1 of 1 ***

define('HEADING_BILLING_ADDRESS', 'Billing/Payment Information');
define('HEADING_DELIVERY_ADDRESS', 'Delivery/Shipping Information');
define('HEADING_SHIPPING_METHOD', 'Shipping Method:');
define('HEADING_PAYMENT_METHOD', 'Payment Method:');
define('HEADING_PRODUCTS', 'Shopping Cart Contents');
define('HEADING_TAX', 'Tax');
define('HEADING_ORDER_COMMENTS', 'Special Instructions or Order Comments');
// no comments entered
define('NO_COMMENTS_TEXT', 'None');
define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', '<strong>Final Step</strong>');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- continue to confirm your order. Thank you!');

define('OUT_OF_STOCK_CAN_CHECKOUT', 'Products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' are out of stock.<br />Items not in stock will be placed on backorder.');

// Begin Order Delivery Date
define('TABLE_HEADING_DELIVERY_DATE', 'Desired Delivery Date');
define('NONE_SELECTED', 'None Selected');
// End Order Delivery Date
?>