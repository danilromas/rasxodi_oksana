## Antikvar CRM (Meshok)

### Что это (RU)
Независимый модуль CRM в папке `antikvar/crm/`, не связан с арендой/учётом расходов. Работает на вашем текущем стеке **PHP + MySQL (PDO)** и использует **Meshok Seller API v2** (см. `antikvar/api.md`) для синхронизации лотов/продаж.

### What it is (EN)
An independent CRM module under `antikvar/crm/`, not tied to your rental/expenses app. Runs on your current **PHP + MySQL (PDO)** stack and uses **Meshok Seller API v2** (see `antikvar/api.md`) to sync lots/sales.

---

### Структура папок / Folder structure
```
antikvar/crm/
  index.php              # UI: Dashboard / Orders / Products / Clients / Notifications / Settings
  api.php                # JSON API router (?action=...)
  cron/
    sync.php             # cron entry (runs sync)
  lib/
    bootstrap.php        # loads root db.php + ensures schema
    MeshokClient.php     # Meshok bearer client (POST JSON)
    SettingsRepo.php     # settings access
    AntikvarSyncService.php
  sql/
    init.sql             # tables for antikvar CRM
```

---

### Установка / Install
1. Откройте `antikvar/crm/index.php` в браузере.
2. Перейдите в “Настройки” и задайте **Meshok API key**.
3. Нажмите “Синхронизация сейчас”.

Схема БД создаётся автоматически при первом вызове `antikvar/crm/api.php` (через `SHOW TABLES LIKE ...` + запуск `sql/init.sql`).

---

### Cron (сервис синхронизации) / Sync service
Запускайте `antikvar/crm/cron/sync.php` по расписанию (например, каждые 10–15 минут):

```bash
php antikvar/crm/cron/sync.php
```

---

### Таблицы БД / DB tables
Основные:
- `antikvar_orders` + `antikvar_order_items`
- `antikvar_products`
- `antikvar_transactions`
- `antikvar_users` (пока зарезервировано; UI клиентов строится из `orders.buyer_username`)
- `antikvar_notifications`
- `antikvar_settings` (ключи: `meshok_api_key`, `sync_interval_minutes`, `last_sync_at`)

---

### Примеры backend endpoints / Example backend endpoints
Все через `antikvar/crm/api.php?action=...`

- **Settings**
  - `GET ?action=settings_get`
  - `POST ?action=settings_set` body: `{ "meshok_api_key": "...", "sync_interval_minutes": 15 }`
- **Sync**
  - `POST ?action=sync_now`
- **Orders**
  - `GET ?action=orders_list&q=...&status=...&from=YYYY-MM-DD&to=YYYY-MM-DD`
  - `GET ?action=order_get&id=123`
  - `POST ?action=order_update_status` body: `{ "id": 123, "status": "paid" }`
- **Products**
  - `GET ?action=products_list&q=...&status=listed`
- **Analytics**
  - `GET ?action=analytics_summary&from=...&to=...`
  - `GET ?action=analytics_sales_series&period=day|week|month`
  - `GET ?action=analytics_top_products`
- **Notifications**
  - `GET ?action=notifications_list&since_id=...`
  - `POST ?action=notifications_mark_read` body: `{ "ids": [1,2,3] }`

---

### Интеграция Meshok / Meshok integration notes
Используются методы из `antikvar/api.md`:
- `getItemList` (лоты на продаже)
- `getFinishedItemList` (завершённые торги)
- `getSoldFinishedItemList` (проданные завершённые, содержит `orderId`)
- `getItemInfo` (детали лота, цена/тип/статус)

Ограничение: в текущей спецификации (файл `api.md`) **нет “order API” с покупателем/адресом/оплатой** — поэтому CRM создаёт “заказы” из `orderId` (как идентификатор сделки) и собирает суммы из лотов.

