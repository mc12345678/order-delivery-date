<?php

/**
 * Autoloader array for Order Delivery Date notification functionality. Makes sure 
 * that features available for Order Delivery Date are 
 * instantiated at the right point of the Zen Cart initsystem.
 * 
 * @package     Order Delivery Date Admin notifications
 * @author      mc12345678
 * @copyright   Copyright 2008-2017 mc12345678 http://mc12345678.com
 * @copyright   Copyright 2003-2017 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://www.zen-cart.com/
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: config.order_delivery_date_admin.php xxxx 2015-10-19 20:31:10Z mc12345678 $
 */


 $autoLoadConfig[0][] = array(
	'autoType' => 'class',
	'loadFile' => 'observers/auto.order_delivery_date_observer.php'
	);
 $autoLoadConfig[175][] = array(
	'autoType' => 'classInstantiate',
	'className' => 'zcObserverOrderDeliveryDateObserver',
	'objectName' => 'orderDeliveryDateObserver'
	); 

