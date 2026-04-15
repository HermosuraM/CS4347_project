-- your code goes here
-- USER table
CREATE TABLE USER (
    user_id INTEGER PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    status TEXT CHECK(status IN ('active','suspended','deleted')) NOT NULL DEFAULT 'active'
);

-- USER_PROFILE table
CREATE TABLE USER_PROFILE (
    profile_id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL UNIQUE,
    first_name TEXT,
    last_name TEXT,
    phone_number TEXT,
    date_of_birth TEXT,
    FOREIGN KEY(user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

-- ADDRESS table
CREATE TABLE ADDRESS (
    address_id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    street TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    zip_code TEXT NOT NULL,
    country TEXT NOT NULL DEFAULT 'USA',
    type TEXT CHECK(type IN ('billing','shipping','both')) NOT NULL DEFAULT 'shipping',
    FOREIGN KEY(user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

-- PRODUCT table
CREATE TABLE PRODUCT (
    product_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    stock_quantity INTEGER NOT NULL DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    is_active INTEGER NOT NULL DEFAULT 1
);

-- CATEGORY table
CREATE TABLE CATEGORY (
    category_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    parent_category_id INTEGER,
    FOREIGN KEY(parent_category_id) REFERENCES CATEGORY(category_id) ON DELETE SET NULL
);

-- PRODUCT_CATEGORY table
CREATE TABLE PRODUCT_CATEGORY (
    product_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY(product_id) REFERENCES PRODUCT(product_id) ON DELETE CASCADE,
    FOREIGN KEY(category_id) REFERENCES CATEGORY(category_id) ON DELETE CASCADE
);

-- PRODUCT_IMAGE table
CREATE TABLE PRODUCT_IMAGE (
    image_id INTEGER PRIMARY KEY,
    product_id INTEGER NOT NULL,
    image_url TEXT NOT NULL,
    alt_text TEXT,
    FOREIGN KEY(product_id) REFERENCES PRODUCT(product_id) ON DELETE CASCADE
);

-- CART table
CREATE TABLE CART (
    cart_id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    status TEXT CHECK(status IN ('active','abandoned','converted')) NOT NULL DEFAULT 'active',
    FOREIGN KEY(user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

-- CART_ITEM table
CREATE TABLE CART_ITEM (
    cart_item_id INTEGER PRIMARY KEY,
    cart_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL CHECK(quantity > 0),
    price_at_time REAL NOT NULL,
    FOREIGN KEY(cart_id) REFERENCES CART(cart_id) ON DELETE CASCADE,
    FOREIGN KEY(product_id) REFERENCES PRODUCT(product_id) ON DELETE RESTRICT
);

-- ORDER table
CREATE TABLE ORDER_TABLE (
    order_id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    order_date TEXT DEFAULT CURRENT_TIMESTAMP,
    status TEXT CHECK(status IN ('pending','completed','canceled','refunded')) NOT NULL DEFAULT 'pending',
    total_amount REAL NOT NULL,
    FOREIGN KEY(user_id) REFERENCES USER(user_id) ON DELETE RESTRICT
);

-- ORDER_ITEM table
CREATE TABLE ORDER_ITEM (
    order_item_id INTEGER PRIMARY KEY,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL CHECK(quantity > 0),
    price_at_purchase REAL NOT NULL,
    FOREIGN KEY(order_id) REFERENCES ORDER_TABLE(order_id) ON DELETE CASCADE,
    FOREIGN KEY(product_id) REFERENCES PRODUCT(product_id) ON DELETE RESTRICT
);

-- PAYMENT table
CREATE TABLE PAYMENT (
    payment_id INTEGER PRIMARY KEY,
    order_id INTEGER NOT NULL,
    payment_method TEXT CHECK(payment_method IN ('credit_card','debit_card','paypal','wallet')) NOT NULL,
    amount REAL NOT NULL,
    payment_status TEXT CHECK(payment_status IN ('pending','success','failed','refunded')) NOT NULL DEFAULT 'pending',
    transaction_date TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(order_id) REFERENCES ORDER_TABLE(order_id) ON DELETE CASCADE
);