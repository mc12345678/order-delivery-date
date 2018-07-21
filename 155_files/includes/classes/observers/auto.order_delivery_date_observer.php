<?php
/**
 * File contains just the observer class
 *
 * @copyright Copyright 2006-2017 Zen Cart Development Team
 * @copyright Copyright 2017 mc12345678 http://mc12345678.com
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version GIT: $Id: Author: mc12345678  Created for v1.5.5 $
 */
/**
 * observer class for Order Delivery Date plugin that extends the base class and
 * supports both the catalog and the admin side. The admin side must load the observer
 * for it to call the applicable code.
 * - package provided with auto.xxx.php style observer functional for ZC 1.5.3 and above.
 * applicable notifiers may not exist in ZC versions pre ZC 1.5.5,
 * files (ie. admin/includes/classes/orders.php) may not extend the base or include some sort of notifier.
 * pre-ZC 1.5.3 will not call the update camelized functions, instead they will call the update function.
 * pre-ZC 1.5.3 only passes the first parameter of the notifier (three are received).
 *   the remaining parameters would have to be obtained by use of a global variable. The update 
 *   camelized functions can then be called with the obtained data.
 *
 * @package order delivery date
 */

class zcObserverOrderDeliveryDateObserver extends base {

  function __construct() {
    $attachNotifier = array();
    $attachNotifier[] = 'NOTIFY_ORDER_AFTER_QUERY';
    $attachNotifier[] = 'NOTIFY_ORDER_CART_FINISHED';
    $attachNotifier[] = 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER';
    $attachNotifier[] = 'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS';
    $attachNotifier[] = 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_SHIPPING';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION';
    $attachNotifier[] = 'ORDER_QUERY_ADMIN_COMPLETE';

    $this->attach($this, $attachNotifier);
  }

  //  ZC1.5.5: $this->notify('NOTIFY_ORDER_AFTER_QUERY', array(), $order_id);
  function updateNotifyOrderAfterQuery(&$callingClass, $notifier, $not_set_array, &$order_id) {

    $get_delivery_date = 'SELECT order_delivery_date FROM ' . TABLE_ORDERS . '  WHERE orders_id = :order_id:';
    $get_delivery_date = $GLOBALS['db']->bindVars($get_delivery_date, ':order_id:', $order_id, 'integer');
    $delivery_date = $GLOBALS['db']->Execute($get_delivery_date);

    $callingClass->info['order_delivery_date'] = $delivery_date->fields['order_delivery_date'];
  }
  
  // ZC 1.5.5: $this->notify('NOTIFY_ORDER_CART_FINISHED');
  // This point was chosen to have just one delivery date for the entire order.
  //  To support a different delivery date by product, would want to either cycle through all of the product in this
  //    function or use the notifier 'NOTIFY_ORDER_CART_ADD_PRODUCT_LIST' to work with each product as it comes along.
  function updateNotifyOrderCartFinished(&$callingClass, $notifier) {
    

    if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
      $regional_display = $this->display_delivery_date($callingClass);

      if (!$regional_display && isset($_SESSION['order_delivery_date'])) {
        unset($_SESSION['order_delivery_date']);
      }

      if (!zen_not_null($this->order_delivery_date) && defined('MIN_DISPLAY_DELIVERY_DATE') && MIN_DISPLAY_DELIVERY_DATE > 0 && $regional_display)
      {
        $GLOBALS['messageStack']->add_session('checkout_shipping', ERROR_PLEASE_CHOOSE_DELIVERY_DATE, 'error');

        unset($_SESSION['order_delivery_date']);

        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
      }
    }

    $callingClass->info['order_delivery_date'] = isset($_SESSION['order_delivery_date']) ? $_SESSION['order_delivery_date'] : null;
    $GLOBALS['order_delivery_date'] = $this->order_delivery_date;

    // set the global variable to display the delivery date (or not) based on the destination of the delivery.
    $GLOBALS['display_delivery_date'] = (defined('ORDER_DELIVERY_DATE_DISPLAY_ALWAYS') && ORDER_DELIVERY_DATE_DISPLAY_ALWAYS === 'true' || $this->display_delivery_date($callingClass));
  }

  //  ZC 1.5.5: $this->notify('NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER', array_merge(array('orders_id' => $insert_id, 'shipping_weight' => $_SESSION['cart']->weight), $sql_data_array), $insert_id);
  function updateNotifyOrderDuringCreateAddedOrderHeader(&$callingClass, $notifier, $data_array, &$insert_id) {

    $orders_id = $data_array['orders_id'];

    // prepare to be able to process information from the order class.
    if (!class_exists('order')) {
      require_once(DIR_WS_CLASSES . 'order.php');
    }
    // Collect information associated with the order_id.
    $order = new order($orders_id);

    // if the delivery date is not to be collected, then don't store the delivery date with the order.
    if (!$this->display_delivery_date($order)) {
     return;
    }
    
    // prepare the data to be incorporated into the order.
    $sql_data_array = array('order_delivery_date'  => $callingClass->info['order_delivery_date']);

    zen_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = ' . (int)$orders_id);
  }

  // ZC 1.5.5: $this->notify('NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS', array(), $email_order, $html_msg);
  function updateNotifyOrderEmailBeforeProducts(&$callingClass, $notifier, $empty_array, &$email_order, &$html_msg) {

    // This adds the EMAIL_TEXT_DELIVERY_DATE information to the end of $email_order as it is when provided to this
    //   function.  If it is desired to place the text somewhere "further up" in the text, then it may be possible through
    //   this function, or the includes/classes/orders.php file may need editing.  A few ways that it could be 
    //   done in here is to reference the use of \n\n or \n, to search for specific text assuming that it is uniquely
    //   used within the total string.
    $email_order .= EMAIL_TEXT_DELIVERY_DATE . ' '  . zen_db_output($callingClass->info['order_delivery_date']) . "\n\n";

    $html_msg['EMAIL_TEXT_DELIVERY_DATE']  = EMAIL_TEXT_DELIVERY_DATE . ' ' . zen_db_output($callingClass->info['order_delivery_date']);
  }

  // ZC155:   $zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_SHIPPING');
  // NOTIFY_HEADER_START_CHECKOUT_SHIPPING
  function updateNotifyHeaderStartCheckoutShipping(&$callingClass, $notifier) {

      // BEGIN Order Delivery Date
    if (isset($_SESSION['order_delivery_date'])) {
      $this->order_delivery_date = $_SESSION['order_delivery_date'];
    }

    // If progressing to checkout then clean up the entry for moving forwards.
    if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
      // If there was something in the date entry form, then set it to the session for further processing.
      if (zen_not_null($_POST['order_delivery_date'])) {
        $_SESSION['order_delivery_date'] = zen_db_prepare_input($_POST['order_delivery_date']);
      }

      // If there is a date to be processed, then store it internally to continue processing in this observer class.
      $this->order_delivery_date = (isset($_SESSION['order_delivery_date'])) ? $_SESSION['order_delivery_date'] : null;
    }
  }

  // ZC 1.5.5  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SHIPPING');
  function updateNotifyHeaderEndCheckoutShipping(&$callingClass, $notifier) {

    $GLOBALS['display_order_delivery_date'] = isset($GLOBALS['$display_delivery_date']) 
                                            ?
                                              $GLOBALS['display_delivery_date'] 
                                            :
                                              true;

    $GLOBALS['order_delivery_date_state_text'] = (defined('MIN_DISPLAY_DELIVERY_DATE')
                                               && MIN_DISPLAY_DELIVERY_DATE > 0
                                               && (method_exists($this, 'display_delivery_date')
                                                    ? $this->display_delivery_date($GLOBALS['order'])
                                                    : true
                                                  )
                                             )
                                             ? TABLE_HEADING_DELIVERY_DATE_IS_REQUIRED
                                             : TABLE_HEADING_DELIVERY_DATE_IS_OPTIONAL;

  }

  // ZC 1.5.5  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION');
  function updateNotifyHeaderEndCheckoutConfirmation(&$callingClass, $notifier) {
    $GLOBALS['display_order_delivery_date'] = $this->display_delivery_date($GLOBALS['order']);

    $GLOBALS['order_delivery_date_text'] = zen_not_null($GLOBALS['order']->info['order_delivery_date']) ? zen_date_long($GLOBALS['order']->info['order_delivery_date']) : NONE_SELECTED;
  }

  // ZC 1.5.5: $this->notify('ORDER_QUERY_ADMIN_COMPLETE', array('orders_id' => $order_id));
  function updateOrderQueryAdminComplete(&$callingClass, $notifier, $paramsArray) {

    $order_id = $paramsArray['orders_id'];

    // Need a test for presence of field/plugin? Prefer something already in memory rather than asking the DB.
    $order = $GLOBALS['db']->Execute("SELECT order_delivery_date 
                           from " . TABLE_ORDERS . "
                           where orders_id = " . (int)$order_id);

    $callingClass->info = array_merge($callingClass->info, array('order_delivery_date' => $order->fields['order_delivery_date']));
  }

  /**
  * Function to support display of the delivery date based on known internally collected order information.
  **/
  function display_delivery_date($order = NULL) {
    // if this  function is called, but there is no ORDER_DELIVERY_DATE_LOCATION  defined, then allow the delivery date to be displayed.
    if (!defined('ORDER_DELIVERY_DATE_LOCATION')) return true;
    //  If the location to be sent to is not defined, then address information  will not be available for the $order class to determine
    //  the destination, indicate to display the delivery date.
    if (!isset($_SESSION['sendto'])) return true;

    if (!isset($order)) {
      // This area may need additional assignments in order to generate the appropriate information to be handled below
      //   if $order has not previously been fully populated.
      if (!class_exists('order')) {
        require_once(DIR_WS_CLASSES . 'order.php');
      }
      $order = new order;
    }

    $pass = false;

    switch (ORDER_DELIVERY_DATE_LOCATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'both':
        $pass = true;
        break;
    }

    // If everything in the order is virtual, then there should not be a delivery date.
    return $pass && $order->content_type !== 'virtual';
  }

  function update(&$callingClass, $notifier, $paramsArray) {
    if ($notifier == 'NOTIFY_ORDER_AFTER_QUERY') {
//      $this->updateNotifyOrderAfterQuery($callingClass, $notifier, $paramsArray, $order_id);
    }
    if ($notifier == 'NOTIFY_ORDER_CART_FINISHED') {
      $this->updateNotifyOrderCartFinished($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER') {
      $insert_id = $paramsArray['orders_id'];
      $this->updateNotifyOrderDuringCreateAddedOrderHeader($callingClass, $notifier, $paramsArray, $insert_id);
    }
    if ($notifier == 'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS') {
      $email_order = null; // Need to figure out how this would work for ZC 1.5.1 if at all.
      $html_msg = null; // Need to figure out how this would work for ZC 1.5.1 if at all.
      $this->updateNotifyOrderEmailBeforeProducts($callingClass, $notifier, $paramsArray, $email_order, $html_msg);
    }
    if ($notifier == 'NOTIFY_HEADER_END_CHECKOUT_SHIPPING') {
      $this->updateNotifyHeaderEndCheckoutShipping($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION') {
      $this->updateNotifyHeaderEndCheckoutConfirmation($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING') {
      $this->updateNotifyHeaderStartCheckoutShipping($callingClass, $notifier);
    }
    if ($notifier == 'ORDER_QUERY_ADMIN_COMPLETE') {
      $this->updateOrderQueryAdminComplete($callingClass, $notifier, $paramsArray);
    }
    
  }
}
