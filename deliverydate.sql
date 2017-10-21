ALTER TABLE orders ADD order_delivery_date date AFTER ip_address;

INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Require Order Delivery Date?', 'MIN_DISPLAY_DELIVERY_DATE', 1, 'Any number over zero will make the delivery date a required field in checkout', 2, 18, now());