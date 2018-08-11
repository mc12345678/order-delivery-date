
DELETE FROM configuration WHERE configuration_key = 'MIN_DISPLAY_DELIVERY_DATE';

# Uncomment the below ALTER line by removing the # symbol
# This will allow removing the column which will also delete the
#  data that was maintained with each entry.
# ALTER TABLE orders DROP COLUMN order_delivery_date;