CREATE TABLE trader_category(
    category_id INT PRIMARY KEY,
    category_type VARCHAR(15) NOT NULL
);

CREATE TABLE users(
    user_id INT PRIMARY KEY,
    user_role VARCHAR(10) NOT NULL,
    user_phone_number VARCHAR(20) NOT NULL,
    user_name VARCHAR(50) NOT NULL,
    user_email VARCHAR(50) NOT NULL UNIQUE,
    user_password VARCHAR(100) NOT NULL,
    verified VARCHAR(5) DEFAULT 'TRUE',
    category_id INT REFERENCES trader_category(category_id)
);

CREATE TABLE shop(
    shop_id INT PRIMARY KEY,
    shop_name VARCHAR(50) NOT NULL,
    user_id INT REFERENCES users(user_id),
    permissions VARCHAR(100) NOT NULL,
    aminlook VARCHAR(10) DEFAULT 'admin'
);

CREATE TABLE product(
    product_id INT PRIMARY KEY,
    shop_id INT NOT NULL,
    product_name VARCHAR(50) NOT NULL,
    product_description VARCHAR(255) NOT NULL,
    min_order INT DEFAULT 1,
    max_order INT,
    allergy_information VARCHAR(255) NOT NULL,
    stock INT NOT NULL,
    product_image VARCHAR(200) NOT NULL,
    disabled VARCHAR(5) DEFAULT 'FALSE',
    discount FLOAT DEFAULT 0,
    product_price FLOAT NOT NULL,
    permissions VARCHAR(100) NOT NULL,
    aminlook VARCHAR(10) DEFAULT 'admin'
);

ALTER TABLE product ADD CONSTRAINT product_shop_fk FOREIGN KEY (shop_id) REFERENCES shop(shop_id) ON DELETE CASCADE;

CREATE TABLE comments(
    comment_id INT PRIMARY KEY,
    product_id INT REFERENCES product(product_id),
    user_id INT REFERENCES users(user_id),
    comment_content VARCHAR(255) NOT NULL
);

CREATE TABLE rating(
    rating_id INT PRIMARY KEY,
    product_id INT REFERENCES product(product_id),
    user_id INT REFERENCES users(user_id),
    rating_star INT NOT NULL
);

CREATE TABLE cart(
    cart_id INT PRIMARY KEY,
    user_id INT REFERENCES users(user_id)
);

CREATE TABLE cart_details(
    cart_id INT REFERENCES cart(cart_id),
    product_id INT NOT NULL,
    product_quantity INT NOT NULL
);

ALTER TABLE product ADD CONSTRAINT cart_product_fk FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;


CREATE TABLE coupon(
    coupon_id INT PRIMARY KEY,
    coupon_code VARCHAR(50) NOT NULL,
    discount_percent FLOAT NOT NULL
);

CREATE TABLE collection_slot(
    slot_id INT PRIMARY KEY,
    collection_day VARCHAR(15) NOT NULL,
    collection_time VARCHAR(15) NOT NULL
);

CREATE TABLE orders(
    order_id INT PRIMARY KEY,
    order_date DATE default CURRENT_DATE,
    delivered VARCHAR(5) DEFAULT 'FALSE',
    slot_id INT REFERENCES collection_slot(slot_id),
    cart_id INT REFERENCES cart(cart_id)
);

CREATE TABLE order_details(
    order_id INT REFERENCES orders(order_id),
    product_id INT NOT NULL,
    product_quantity INT NOT NULL
);

ALTER TABLE order_details ADD CONSTRAINT order_product_fk FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;


-- ALTER TABLE product DROP CONSTRAINT SYS_C0011479;
-- ALTER TABLE product ADD CONSTRAINT shop_fk FOREIGN KEY (shop_id) REFERENCES shop(shop_id) ON DELETE CASCADE;

-- ALTER TABLE order_details DROP CONSTRAINT SYS_C0011504;
-- ALTER TABLE order_details ADD CONSTRAINT product_fk FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;

-- ALTER TABLE cart_details DROP CONSTRAINT SYS_C0011492; 
-- ALTER TABLE cart_details ADD CONSTRAINT cart_product_fk FOREIGN KEY (product_id) REFERENCES product(product_Id) ON DELETE CASCADE;

-- ALTER TABLE comments DROP CONSTRAINT SYS_C0011482;
-- ALTER TABLE comments ADD CONSTRAINT comment_product_fk FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;

-- ALTER TABLE rating DROP CONSTRAINT SYS_C0011486;
-- ALTER TABLE rating ADD CONSTRAINT rating_product_fk FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;
