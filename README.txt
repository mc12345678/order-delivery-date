Contribution:  Order Delivery Date
Version:       2.6
Designed For:  Zen Cart v1.3.8a, 1.3.9f, 1.5.1 and 1.5.5 Releases
Forum Page:    http://www.zen-cart.com/forum/showthread.php?t=92762


================= IMPORTANT =====================
Please BACKUP your store AND database before 
attempting any of these changes.
I will not be held responsible!
=================================================

WHAT DOES THIS MODULE DO?
This modification adds a Ship Order Date feature to the checkout process. The end-user is presented with an aesthetic,
fully configurable pop-up calendar for them to select their delivery date.  Changes are also seen in the admin 
side (Invoice, Packing Slip, View Order) and are reflected in emails to the customer that are generated when 
completing an order, and when updating order status via Admin.

========================================================

INSTALL:

a. BACKUP ALL OF YOUR FILES that you will be overwriting as well as your Database before installing!!!

b. Run the sql statement (shipdate.sql) against your database using 
Admin->Tools->Install SQL patches.

c. Copy files to your install.  
   - Be sure to use the directory appropriate for your Zen Cart version (1.3.8, 1.3.9, 1.5.1, 1.5.5, etc.).
   - Make sure to change any folders labeled YOUR_TEMPLATE to the template you are using.

To change blocked out dates, open the following file and read the instructions contained within it: includes/modules/pages/checkout_shipping/jscript_calendar_head.php


d. The sql file adds a new setting under configuration>minimum values. If set to a value greater than 0, delivery date is required. If set to 0, delivery date is not required. Default is 1 - required.

e. There is now an option to not display the Order Delivery Date selector based on the deliver to address.
   the ZC 1.5.5 file: includes\extra_datafiles\order_delivery_date_location.php contains a define that indicates on
   which destination address type the delivery date field should be shown (national to the store's location,
   international, or both). (When not shown it ignores the settings above about required or optional, but when shown
   then it follows the settings of required or optional.)

UPGRADE: 
If you are upgrading from an older version than 2.4, please run the file 
update_to_2.4.sql in Admin->Tools->Install SQL Patches. 

If you are upgrading from a version before 2.5.2, there are core files that no longer require revision to support this
plugin and therefore either now or on your next upgrade the changes previously made to the following files are no longer
required and can be removed.  This does not necessarily mean that the file should be "blindly" replaced with an original
version of the file, but that there should be a review performed to see what changes may have been incorporated by other
plugins.

admin/includes/classes/order.php
includes/classes/order.php
includes/modules/pages/checkout_shipping/header_php.php

========================================================

HISTORY:

01/28/2018 by mc12345678 (http://mc12345678.com) (v2.6) as requested by Audrey at: https://www.zen-cart.com/showthread.php?92762-Order-Delivery-Date-Support-Thread&p=1341081#post1341081
  incorporated a method to display/hide (and therefore support requiring) the order delivery date field based on whether
  the reciepient address is within the store's nation or outside of it. Setting for this is current in the file:
  includes\extra_datafiles\order_delivery_date_location.php
  - Also updated the confirmation_checkout pages again to potentially support
  expected functionality of using PayPal's In Context payment if it has been incorporated into the store.
  - Corrected minor items that would issue notices if site were run in strict mode.

11/13/2017 by mc12345678 (http://mc12345678.com) updated to finally fully work with ZC 1.5.5 with minimal software edits
  and using the ZC observer/notifier system where able.

02/15/2017 by mc12345678 http://mc12345678.com
- repackaged zip file, updated version number to 2.5.1. no code changes.

02/11/2017 by mc12345678 http://mc12345678.com
- made date entry block not readonly, date entry also repopulates based on previous entry.
- updated files for ZC 1.5.5.

12/10/2012 by Delia Wilson Lunsford
- with help from That Software Guy, added delivery date as required or not required, included updated version of mootools js for IE 9 problems.
10/12/2012 by Delia Wilson Lunsford
-updated to version 1.5.1

07/04/2011 by That Software Guy 
- Updated checkout shipping template in 1.3.9 to fix bug.

09/19/2010 by That Software Guy 
- Ported to 1.3.9, fixed bug in 1.3.8 admin/orders.php 

06/07/2008 by MrMeech:
-The update from 5/27/2008 to fix the button completely broke another part of the form.
-Only Changed File: includes/templates/YOUR_TEMPLATE/templates/tpl_checkout_shipping_default.php

05/27/2008 by MrMeech:
-Calendar Button Fix (includes/templates/YOUR_TEMPLATE/templates/tpl_checkout_shipping_default.php)

05/15/2008 (v2) by MrMeech:
-Added a new (and beautiful) Popup Calendar Date Picker to the Checkout Process (Calendar code from: http://electricprism.com/aeron/calendar)
-Code Fix to close PHP tag correctly (includes/languages/english/Your_Template/checkout_process.php) Thank You SteveKim.

-Updated/Changed Files:
   * includes/templates/YOUR_TEMPLATE/templates/tpl_checkout_confirmation_default.php
   * includes/templates/YOUR_TEMPLATE/templates/tpl_checkout_shipping_default.php
   * readme.txt (this file)

-Added Files:
   * includes/templates/YOUR_TEMPLATE/css/checkout_shipping.css
   * includes/templates/YOUR_TEMPLATE/images/calendar/*.*
   * includes/modules/pages/checkout_shipping/jscript_calendar_head.php
   * includes/modules/pages/checkout_shipping/jscript_a-mootools.js
   * includes/modules/pages/checkout_shipping/jscript_b-calendar.js
   * includes/modules/pages/checkout_shipping/mootools.js

03/24/2008 (v1) by MrMeech:
Updated to fix a number of issues in code and add a couple other features (By MrMeech):
- Added File (includes/templates/YOUR_TEMPLATE/templates/tpl_checkout_confirmation_default.php) - Added Delivery Date to display on checkout confirmation page
- Added File (includes/languages/english/YOUR_TEMPLATE/checkout_confirmation) - has Delivery Date define
- Added File (email/email_template_checkout.html) - to show delivery date on email generated during checkout, in addition to when orders are updated via Admin
- (admin/includes/classes/order.php) - Fixed "info" array "delivery_date" to "order_delivery_date" to match format of the rest of the array and unify all code references to the array. Also some code would reference "delivery_date" and some would reference "order_delivery_date", so everything is now "order_delivery_date". 
- (admin/includes/languages/english/orders.php) - Added missing email define
- (admin/invoice.php) - Line 150 - changed to "order_delivery_date" from "delivery_date"
- (admin/orders.php) - Removed extra space in string and updated to correct path (line 108); Updated line 117 to "order_delivery_date" from "delivery_date"; Updated line 414 to reflect similar changes
- (admin/packingslip.php) - Line 147 updated to "order_delivery_date"
- (includes/classes/order.php) - Line 924 - Was missing the HTML variable for HTML email output
- Added contribution info to the comments at the top of every file (except the emails) for easier version tracking down the road.
- Updated this file (readme.txt)

03/13/2008: 
First compiled release of this modification
http://www.zen-cart.com/forum/showthread.php?t=88550

02/13/2008: 
Peter Martin's Post to the forum of the initial code


=======================================================

Things to work on:
 - Adding delivery date to order history on customer's logged in page

Disclaimer
----------
This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
(LICENSE) along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,
USA.

-------

Initial Release compiled by James Betesh (http://www.jamesbetesh.com) from coding Peter Martin's (aka pe7er - http://www.db8.nl/) posted to http://www.zen-cart.com/forum/showthread.php?t=88550 .
