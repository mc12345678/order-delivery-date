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
    $attachNotifier[] = 'NOTIFY_ADMIN_ORDERS_SEARCH_PARMS';
    $attachNotifier[] = 'NOTIFY_ADMIN_ORDERS_UPDATE_ORDER_START';
    $attachNotifier[] = 'ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT';
    $attachNotifier[] = 'NOTIFY_ADMIN_ORDERS_LIST_EXTRA_COLUMN_HEADING';
    $attachNotifier[] = 'NOTIFY_ADMIN_ORDERS_LIST_EXTRA_COLUMN_DATA';

    $attachNotifier[] = 'NOTIFY_ORDER_AFTER_QUERY';
    $attachNotifier[] = 'NOTIFY_ORDER_CART_FINISHED';
    $attachNotifier[] = 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER';
    $attachNotifier[] = 'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS';
    $attachNotifier[] = 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_SHIPPING';
    $attachNotifier[] = 'NOTIFY_HEADER_START_CHECKOUT_PAYMENT';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_SUCCESS';

    $attachNotifier[] = 'NOTIFY_HEADER_START_CHECKOUT_ONE';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_ONE';
    $attachNotifier[] = 'NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION';
    $attachNotifier[] = 'NOTIFY_HEADER_END_CHECKOUT_ONE_CONFIRMATION';

    $attachNotifier[] = 'ORDER_QUERY_ADMIN_COMPLETE';

    $this->attach($this, $attachNotifier);
  }

  // ZC 1.5.7: $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_SEARCH_PARMS', $keywords, $search, $search_distinct, $new_fields, $new_table, $order_by);
  function updateNotifyAdminOrdersSearchParms(&$callingClass, $notifier, $keywords, &$search, &$search_distinct, &$new_fields, &$new_table, &$order_by) {
    global $sniffer;

    if (!$sniffer->field_exists(TABLE_ORDERS, 'order_delivery_date')) return;

    $new_fields .= ', o.order_delivery_date';
  }

  //  ZC 1.5.7: $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_UPDATE_ORDER_START', $oID);
  //   This has to be used to add to the order message because osh_status_update doesn't support message updating based on oID
  function updateNotifyAdminOrdersUpdateOrderStart(&$callingClass, $notifier, $oID) {
    global $comments, $db, $email_include_message, $sniffer;
    
    // prevent error if the field does not exist in the table by early escaping.
    if (!$sniffer->field_exists(TABLE_ORDERS, 'order_delivery_date')) return;
    
    $sql = "SELECT order_delivery_date FROM " . TABLE_ORDERS . " WHERE orders_id = " . (int)$oID . " LIMIT 1";
    $check_status = $db->Execute($sql);
    
    if ($check_status->EOF) {
      return;
    }

    $this->email_include_message = $email_include_message;
    if (!$this->email_include_message) {
      $this->comments = $comments; // Capture the comments for other use
      $comments = '';
      $email_include_message = true;
    }

    // If want $html_msg type data, then need to incorporate the following in some way:
    //   $html_msg['EMAIL_TEXT_DELIVERY_DATE'] = EMAIL_TEXT_DELIVERY_DATE . ' ' . zen_date_long($check_status->fields['order_delivery_date']);

    $comments = EMAIL_TEXT_DELIVERY_DATE . ' ' . zen_date_long($check_status->fields['order_delivery_date']) . "\n\n" . $comments;
  }

  // ZC 1.5.7: $GLOBALS['zco_notifier']->notify('ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT', array(), $osh_sql);
  function updateZenUpdateOrdersHistoryBeforeInsert(&$callingClass, $notifier, $emptyArray, &$osh_sql) {
    global $sniffer;

    if (!$sniffer->field_exists(TABLE_ORDERS, 'order_delivery_date')) return;

    if (empty($this->email_include_message)) {
      $osh_sql['comments'] = $this->comments; // Restore the content of the comments to what it was before trying to use the code to notify about the delivery date.
    }
  }

  // ZC 1.5.7: $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_LIST_EXTRA_COLUMN_HEADING', array(), $extra_headings);
  function updateNotifyAdminOrdersListExtraColumnHeading(&$callingClass, $notifier, $emptyArray, &$extra_headings) {
    if ($extra_headings === false) {
      $extra_headings = array();
    }
    // Following the direction within the software to validate === false leaves the potential that a previous observer
    //  has unset the variable at which point it will not === false and it will not exist for that other observer...
    //  If this has happened to you, suggest changing the above to if (empty($extra_headings)) {
    if (!isset($extra_headings) || !is_array($extra_headings)) {
      return;
    }
    
    $extra_headings[] = array(
                          'align' => 'center',
                          'text' => TABLE_HEADING_DELIVERY_DATE,
                          );
  }
  
  // ZC 1.5.7: $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_LIST_EXTRA_COLUMN_DATA', (isset($oInfo) ? $oInfo : array()), $orders->fields, $extra_data);
  function updateNotifyAdminOrdersListExtraColumnData(&$callingClass, $notifier, $oInfo_cond, &$orders_fields, &$extra_data) {
    if ($extra_data === false) {
      $extra_data = array();
    }
    // Following the direction within the software to validate === false leaves the potential that a previous observer
    //  has unset the variable at which point it will not === false and it will not exist for that other observer...
    //  If this has happened to you, suggest changing the above to if (empty($extra_headings)) {
    if (!isset($extra_data) || !is_array($extra_data)) {
      return;
    }
    
    $extra_data[] = array(
                          'align' => 'center',
                          'text' => zen_datetime_short($orders_fields['order_delivery_date']),
                          );
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
    
    if (isset($_POST['action']) && ($_POST['action'] == 'process')) {

      $regional_display = $this->display_delivery_date($callingClass);

      // If want to completely remove any selection of a delivery date when the option would become
      //   optional because of sending out-of-state, then uncomment the if statement and unset below.
//      if (!$regional_display && isset($_SESSION['order_delivery_date'])) {
//        unset($_SESSION['order_delivery_date']);
//      }

      if (!zen_not_null($this->order_delivery_date) && defined('MIN_DISPLAY_DELIVERY_DATE') && MIN_DISPLAY_DELIVERY_DATE > 0 && $regional_display)
      {
        global $language_page_directory, $template_dir, $current_page_base, $template;

        require DIR_WS_MODULES . zen_get_module_directory('require_languages.php');

        $GLOBALS['messageStack']->add_session('checkout_shipping', ERROR_PLEASE_CHOOSE_DELIVERY_DATE, 'error');
        // blank out the existing order_delivery_date information when reloading the page to force an entry.
        unset($_SESSION['order_delivery_date']);

        $_SESSION['order_delivery_date_payment_reset'] = true;
        return;
//        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
      }
    }

    $callingClass->info['order_delivery_date'] = isset($_SESSION['order_delivery_date']) ? $_SESSION['order_delivery_date'] : null;
    if (isset($this->order_delivery_date)) {
      $GLOBALS['order_delivery_date'] = $this->order_delivery_date;
    }

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
    $email_order .= EMAIL_TEXT_DELIVERY_DATE . ' '  . zen_date_long(zen_db_output($callingClass->info['order_delivery_date'])) . "\n\n";

    $html_msg['EMAIL_TEXT_DELIVERY_DATE']  = EMAIL_TEXT_DELIVERY_DATE . ' ' . zen_date_long(zen_db_output($callingClass->info['order_delivery_date']));
  }

  // ZC155:   $zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_SHIPPING');
  // NOTIFY_HEADER_START_CHECKOUT_SHIPPING
  function updateNotifyHeaderStartCheckoutShipping(&$callingClass, $notifier) {

      // BEGIN Order Delivery Date
    if (isset($_SESSION['order_delivery_date'])) {
      $this->order_delivery_date = $_SESSION['order_delivery_date'];
    }

    // If progressing to checkout then clean up the entry for moving forwards.
    if ( isset($_POST['action']) && ($_POST['action'] == 'process') || $callingClass == 'NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION' && isset($_POST['order_confirmed']) && ($_POST['order_confirmed'] == '1')) {
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

  // ZC 1.5.7: $zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_PAYMENT');
  function updateNotifyHeaderStartCheckoutPayment(&$callingClass, $notifier) {
    if (!empty($_SESSION['order_delivery_date_payment_reset'])) {
      unset($_SESSION['order_delivery_date_payment_reset']);
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }

  // ZC 1.5.5  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION');
  function updateNotifyHeaderEndCheckoutConfirmation(&$callingClass, $notifier) {
    $GLOBALS['display_order_delivery_date'] = $this->display_delivery_date($GLOBALS['order']);

    $GLOBALS['order_delivery_date_text'] = zen_not_null($GLOBALS['order']->info['order_delivery_date']) ? zen_date_long($GLOBALS['order']->info['order_delivery_date']) : NONE_SELECTED;
  }

  // NOTIFY_HEADER_START_CHECKOUT_ONE
  function updateNotifyHeaderStartCheckoutOne(&$callingClass, $notifier) {

//    trigger_error('checkoutonePOST: ' . print_r($_POST, true), E_USER_WARNING);
    $this->updateNotifyHeaderStartCheckoutShipping($callingClass, $notifier);

  }

  // NOTIFY_HEADER_END_CHECKOUT_ONE
  function updateNotifyHeaderEndCheckoutOne(&$callingClass, $notifier) {
    $this->updateNotifyHeaderEndCheckoutShipping($callingClass, $notifier);
  }

  // NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION
  function updateNotifyHeaderStartCheckoutOneConfirmation(&$callingClass, $notifier) {

//    trigger_error('checkoutonePOST: ' . print_r($_POST, true), E_USER_WARNING);
    $this->updateNotifyHeaderStartCheckoutShipping($callingClass, $notifier);

  }

  // NOTIFY_HEADER_END_CHECKOUT_ONE_CONFIRMATION
  function updateNotifyHeaderEndCheckoutOneConfirmation(&$callingClass, $notifier) {
    $this->updateNotifyHeaderEndCheckoutConfirmation($callingClass, $notifier);
  }

  // NOTIFY_HEADER_END_CHECKOUT_SUCCESS
  function updateNotifyHeaderEndCheckoutSuccess(&$callingClass, $notifier) {
    unset($_SESSION['order_delivery_date']);
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
    // if this function is called, but there is no ORDER_DELIVERY_DATE_LOCATION defined, then allow the delivery date to be displayed.
    if (!defined('ORDER_DELIVERY_DATE_LOCATION')) return true;
    //  If the location to be sent to is not defined, then address information will not be available for the $order class to determine
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
    if ($notifier == 'NOTIFY_HEADER_END_CHECKOUT_SUCCESS') {
      $this->updateNotifyHeaderEndCheckoutSuccess($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_START_CHECKOUT_ONE') {
      $this->updateNotifyHeaderStartCheckoutOneConfirmation($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_END_CHECKOUT_ONE') {
      $this->updateNotifyHeaderEndCheckoutOne($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION') {
      $this->updateNotifyHeaderStartCheckoutOneConfirmation($callingClass, $notifier);
    }
    if ($notifier == 'NOTIFY_HEADER_END_CHECKOUT_ONE_CONFIRMATION') {
      $this->updateNotifyHeaderEndCheckoutOneConfirmation($callingClass, $notifier);
    }
    if ($notifier == 'ORDER_QUERY_ADMIN_COMPLETE') {
      $this->updateOrderQueryAdminComplete($callingClass, $notifier, $paramsArray);
    }
    
  }
}
