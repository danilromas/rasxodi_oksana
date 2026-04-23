-- Antikvar CRM tables (Meshok integration + CRM UI)
-- Safe to run multiple times.

CREATE TABLE IF NOT EXISTS antikvar_settings (
  `key` VARCHAR(64) PRIMARY KEY,
  `value` TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_sync_state (
  `key` VARCHAR(64) PRIMARY KEY,
  `value` VARCHAR(255) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(128) NOT NULL,
  display_name VARCHAR(255) NULL,
  city VARCHAR(128) NULL,
  country VARCHAR(128) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_antikvar_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_products (
  meshok_item_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  internal_id VARCHAR(64) NULL,
  name VARCHAR(512) NOT NULL,
  sale_type ENUM('Sale','Auction') NOT NULL,
  status ENUM('deferred','draft','listed','finished','deleted') NOT NULL,
  currency_id INT NULL,
  quantity INT NULL,
  sold INT NULL,
  price DECIMAL(15,2) NULL,
  current_price DECIMAL(15,2) NULL,
  bids INT NULL,
  end_datetime VARCHAR(32) NULL,
  tz VARCHAR(16) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  raw_json LONGTEXT NULL,
  KEY ix_antikvar_products_status (status),
  KEY ix_antikvar_products_internal_id (internal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_orders (
  meshok_order_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  status ENUM('new','paid','shipped','completed') NOT NULL DEFAULT 'new',
  buyer_username VARCHAR(128) NULL,
  buyer_user_id BIGINT UNSIGNED NULL,
  currency_id INT NULL,
  total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  raw_json LONGTEXT NULL,
  KEY ix_antikvar_orders_status (status),
  KEY ix_antikvar_orders_buyer (buyer_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_order_items (
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(15,2) NULL,
  PRIMARY KEY (order_id, product_id),
  CONSTRAINT fk_antikvar_order_items_order FOREIGN KEY (order_id) REFERENCES antikvar_orders(meshok_order_id) ON DELETE CASCADE,
  CONSTRAINT fk_antikvar_order_items_product FOREIGN KEY (product_id) REFERENCES antikvar_products(meshok_item_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_transactions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NULL,
  type ENUM('sale','refund','fee') NOT NULL DEFAULT 'sale',
  amount DECIMAL(15,2) NOT NULL,
  currency_id INT NULL,
  occurred_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_antikvar_transactions_order (order_id),
  CONSTRAINT fk_antikvar_transactions_order FOREIGN KEY (order_id) REFERENCES antikvar_orders(meshok_order_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS antikvar_notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(64) NOT NULL,
  message VARCHAR(1000) NOT NULL,
  entity_type VARCHAR(64) NULL,
  entity_id VARCHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL,
  KEY ix_antikvar_notifications_read (read_at),
  KEY ix_antikvar_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

