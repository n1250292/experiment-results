CREATE TABLE cars (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maker VARCHAR(80) NOT NULL,
  name VARCHAR(120) NOT NULL,
  grade VARCHAR(120) NOT NULL,
  price BIGINT UNSIGNED NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  image_url VARCHAR(500) NULL,
  short_description VARCHAR(255) NULL,
  description TEXT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cars_published_price (is_published, price),
  INDEX idx_cars_maker (maker)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(30) NOT NULL UNIQUE,
  customer_name VARCHAR(100) NOT NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  postal_code VARCHAR(10) NULL,
  address VARCHAR(255) NOT NULL,
  note TEXT NULL,
  total_price BIGINT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT '新規受付',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_orders_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  car_id INT UNSIGNED NULL,
  car_name VARCHAR(320) NOT NULL,
  unit_price BIGINT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_order_items_car (car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 表示確認用のサンプル車両。価格・画像・仕様は仮データです。
INSERT INTO cars (maker,name,grade,price,stock,image_url,short_description,description,is_published) VALUES
('サンプル自動車','シティ','G','2190000',3,'','街乗りに使いやすいコンパクトカー。','燃費や装備などの仕様はサンプルです。実際の販売車両情報に差し替えてください。',1),
('サンプル自動車','ツーリング','X','2980000',2,'','快適性と荷室スペースを両立。','グレード・価格・仕様はサンプルです。正式な見積もりは販売店へお問い合わせください。',1),
('サンプルモーターズ','ファミリーSUV','Z','3450000',0,'','家族で使いやすいSUV。','車名・価格・仕様はすべて仮データです。',1);
