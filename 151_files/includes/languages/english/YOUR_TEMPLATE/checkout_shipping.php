<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_shipping.php 18697 2011-05-04 14:35:20Z wilt $
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
define('NAVBAR_TITLE_2', 'Shipping Method');

define('HEADING_TITLE', 'Step 1 of 3 - Delivery Information');

define('TABLE_HEADING_SHIPPING_ADDRESS', 'Shipping Address');
define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'Your order will be shipped to the address at the left or you may change the shipping address by clicking the <em>Change Address</em> button.');
define('TITLE_SHIPPING_ADDRESS', 'Shipping Information:');

define('TABLE_HEADING_SHIPPING_METHOD', 'Shipping Options:');
define('TEXT_CHOOSE_SHIPPING_METHOD', 'Please select the preferred shipping method to use on this order.');
define('TITLE_PLEASE_SELECT', 'Please Select');
define('TEXT_ENTER_SHIPPING_INFORMATION', 'Choose Ship Via, Ship Date & Cancel Date.');
define('TITLE_NO_SHIPPING_AVAILABLE', 'Not Available At This Time');
define('TEXT_NO_SHIPPING_AVAILABLE','<span class="alert">Sorry, we are not shipping to your region at this time.</span><br />Please contact us for alternate arrangements.');

define('TABLE_HEADING_COMMENTS', 'Special Instructions or Comments About Your Order');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continue to Step 2');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- choose your payment method.');

// when free shipping for orders over $XX.00 is active
  define('FREE_SHIPPING_TITLE', 'Free Shipping');
  define('FREE_SHIPPING_DESCRIPTION', 'Free shipping for orders over %s');
define('ERROR_PLEASE_RESELECT_SHIPPING_METHOD', 'Your available shipping options have changed. Please re-select your desired shipping method.');

  // BEGIN Order Delivery Date
  define('TABLE_HEADING_DELIVERY_DATE', 'Desired Delivery Date is Required!');
  define('ERROR_PLEASE_CHOOSE_DELIVERY_DATE', 'Please choose a delivery date');
// END Order Delivery 
?>
