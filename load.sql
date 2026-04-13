-- your code goes here
-- ------------------------
-- Load USER table
-- ------------------------
LOAD DATA INFILE 'user.csv'
INTO TABLE USER
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load USER_PROFILE table
-- ------------------------
LOAD DATA INFILE 'user_profile.csv'
INTO TABLE USER_PROFILE
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load ADDRESS table
-- ------------------------
LOAD DATA INFILE 'address.csv'
INTO TABLE ADDRESS
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load PRODUCT table
-- ------------------------
LOAD DATA INFILE 'product.csv'
INTO TABLE PRODUCT
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load CATEGORY table
-- ------------------------
LOAD DATA INFILE 'category.csv'
INTO TABLE CATEGORY
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load PRODUCT_CATEGORY table
-- ------------------------
LOAD DATA INFILE 'product_category.csv'
INTO TABLE PRODUCT_CATEGORY
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load PRODUCT_IMAGE table
-- ------------------------
LOAD DATA INFILE 'product_image.csv'
INTO TABLE PRODUCT_IMAGE
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load CART table
-- ------------------------
LOAD DATA INFILE 'cart.csv'
INTO TABLE CART
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load CART_ITEM table
-- ------------------------
LOAD DATA INFILE 'cart_item.csv'
INTO TABLE CART_ITEM
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load ORDER_TABLE table
-- ------------------------
LOAD DATA INFILE 'order_table.csv'
INTO TABLE ORDER_TABLE
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load ORDER_ITEM table
-- ------------------------
LOAD DATA INFILE 'order_item.csv'
INTO TABLE ORDER_ITEM
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- ------------------------
-- Load PAYMENT table
-- ------------------------
LOAD DATA INFILE 'payment.csv'
INTO TABLE PAYMENT
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';