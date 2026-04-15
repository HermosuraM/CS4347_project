-- Load USER table (created_at uses DEFAULT, not in CSV)
LOAD DATA INFILE '/var/lib/mysql-files/USER.csv'
INTO TABLE USER
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(user_id, username, email, password_hash, status);

-- Load USER_PROFILE table (profile_id auto-increments)
LOAD DATA INFILE '/var/lib/mysql-files/USER_PROFILE.csv'
INTO TABLE USER_PROFILE
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(profile_id, user_id, first_name, last_name, phone_number, date_of_birth);

-- Load ADDRESS table
LOAD DATA INFILE '/var/lib/mysql-files/ADDRESS.csv'
INTO TABLE ADDRESS
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(address_id, user_id, street, city, state, zip_code, country, type);

-- Load PRODUCT table (created_at uses DEFAULT, not in CSV)
LOAD DATA INFILE '/var/lib/mysql-files/PRODUCT.csv'
INTO TABLE PRODUCT
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(product_id, name, description, price, stock_quantity, is_active);

-- Load CATEGORY table
LOAD DATA INFILE '/var/lib/mysql-files/CATEGORY.csv'
INTO TABLE CATEGORY
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(category_id, name);

-- Load PRODUCT_CATEGORY table
LOAD DATA INFILE '/var/lib/mysql-files/PRODUCT_CATEGORY.csv'
INTO TABLE PRODUCT_CATEGORY
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(product_id, category_id);

-- Load PRODUCT_IMAGE table
LOAD DATA INFILE '/var/lib/mysql-files/PRODUCT_IMAGE.csv'
INTO TABLE PRODUCT_IMAGE
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(image_id, product_id, image_url, alt_text);

-- Load CART table (created_at uses DEFAULT, not in CSV)
LOAD DATA INFILE '/var/lib/mysql-files/CART.csv'
INTO TABLE CART
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(cart_id, user_id, status);

-- Load CART_ITEM table
LOAD DATA INFILE '/var/lib/mysql-files/CART_ITEM.csv'
INTO TABLE CART_ITEM
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(cart_item_id, cart_id, product_id, quantity, price_at_time);

-- Load ORDER_TABLE table (order_date uses DEFAULT, not in CSV)
LOAD DATA INFILE '/var/lib/mysql-files/ORDER_TABLE.csv'
INTO TABLE ORDER_TABLE
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(order_id, user_id, status, total_amount);

-- Load ORDER_ITEM table
LOAD DATA INFILE '/var/lib/mysql-files/ORDER_ITEM.csv'
INTO TABLE ORDER_ITEM
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(order_item_id, order_id, product_id, quantity, price_at_purchase);

-- Load PAYMENT table (transaction_date uses DEFAULT, not in CSV)
LOAD DATA INFILE '/var/lib/mysql-files/PAYMENT.csv'
INTO TABLE PAYMENT
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(payment_id, order_id, payment_method, amount, payment_status);
