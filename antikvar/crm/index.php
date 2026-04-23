<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Antikvar CRM</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    :root{
      --bg1:#0f172a; --bg2:#111827;
      --card:#0b1220cc;
      --muted:#94a3b8;
      --text:#e5e7eb;
      --accent:#60a5fa;
      --accent2:#a78bfa;
      --good:#34d399;
      --bad:#fb7185;
      --border:#1f2937;
      --shadow: 0 20px 80px rgba(0,0,0,.35);
      --radius: 16px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      min-height:100vh;
      background: radial-gradient(1200px 700px at 20% 10%, rgba(96,165,250,.20), transparent 60%),
                  radial-gradient(900px 600px at 80% 20%, rgba(167,139,250,.16), transparent 55%),
                  linear-gradient(180deg, var(--bg1), var(--bg2));
    }
    .wrap{max-width:1400px; margin:0 auto; padding:22px;}
    header{display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px;}
    .brand{display:flex; gap:12px; align-items:center;}
    .logo{
      width:44px; height:44px; border-radius:12px;
      background: linear-gradient(135deg, rgba(96,165,250,.85), rgba(167,139,250,.8));
      box-shadow: var(--shadow);
    }
    h1{font-size:20px; margin:0;}
    .subtitle{color:var(--muted); font-size:13px; margin-top:2px;}
    .top-actions{display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:flex-end;}
    .btn{
      border:1px solid var(--border);
      background: rgba(2,6,23,.35);
      color:var(--text);
      padding:10px 12px;
      border-radius:12px;
      cursor:pointer;
      transition:.15s ease;
    }
    .btn:hover{transform:translateY(-1px); border-color:#334155;}
    .btn.primary{
      background: linear-gradient(135deg, rgba(96,165,250,.95), rgba(167,139,250,.85));
      border:0;
    }
    .pill{
      padding:9px 12px;
      border-radius:999px;
      border:1px solid var(--border);
      color:var(--muted);
      font-size:12px;
      background: rgba(2,6,23,.35);
    }

    .grid{
      display:grid;
      grid-template-columns: 260px 1fr;
      gap:16px;
    }
    nav{
      position:sticky;
      top:16px;
      align-self:start;
      background: rgba(2,6,23,.35);
      border:1px solid var(--border);
      border-radius: var(--radius);
      padding:12px;
      box-shadow: var(--shadow);
    }
    .nav-item{
      width:100%;
      text-align:left;
      padding:10px 10px;
      border-radius:12px;
      border:1px solid transparent;
      background: transparent;
      color:var(--text);
      cursor:pointer;
      display:flex;
      justify-content:space-between;
      gap:8px;
      margin-bottom:8px;
    }
    .nav-item small{color:var(--muted)}
    .nav-item.active{
      border-color:#334155;
      background: rgba(96,165,250,.12);
    }

    main{min-height:600px;}
    .card{
      background: rgba(2,6,23,.35);
      border:1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding:14px;
      margin-bottom:16px;
    }
    .card h2{margin:0 0 10px; font-size:16px;}
    .muted{color:var(--muted)}
    .kpi{display:grid; grid-template-columns: repeat(4, minmax(160px, 1fr)); gap:12px;}
    .kpi .box{
      padding:14px;
      border:1px solid var(--border);
      border-radius:14px;
      background: rgba(2,6,23,.25);
    }
    .kpi .label{color:var(--muted); font-size:12px;}
    .kpi .value{font-size:22px; margin-top:6px; font-weight:700;}
    .row{display:flex; gap:10px; flex-wrap:wrap; align-items:center;}
    input, select{
      background: rgba(2,6,23,.45);
      color: var(--text);
      border: 1px solid var(--border);
      padding: 10px 12px;
      border-radius: 12px;
      outline:none;
    }
    table{width:100%; border-collapse:collapse;}
    th, td{padding:10px; border-bottom:1px solid var(--border); vertical-align:top;}
    th{color:var(--muted); font-weight:600; text-align:left; font-size:12px;}
    .badge{display:inline-flex; padding:4px 10px; border-radius:999px; font-size:12px; border:1px solid var(--border); background:rgba(2,6,23,.25);}
    .badge.good{border-color: rgba(52,211,153,.35); color: var(--good);}
    .badge.warn{border-color: rgba(96,165,250,.35); color: var(--accent);}
    .badge.bad{border-color: rgba(251,113,133,.35); color: var(--bad);}
    .two-col{display:grid; grid-template-columns: 1fr 1fr; gap:16px;}
    .hidden{display:none;}
    .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;}

    @media (max-width: 980px){
      .grid{grid-template-columns:1fr;}
      nav{position:relative;}
      .kpi{grid-template-columns: repeat(2, minmax(160px, 1fr));}
      .two-col{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div class="brand">
        <div class="logo"></div>
        <div>
          <h1>Antikvar CRM</h1>
          <div class="subtitle">Meshok seller API • заказы • финансы • товары • клиенты</div>
        </div>
      </div>
      <div class="top-actions">
        <span class="pill" id="pill-sync">Sync: —</span>
        <button class="btn" onclick="loadAll()">Обновить</button>
        <button class="btn primary" onclick="syncNow()">Синхронизация сейчас</button>
      </div>
    </header>

    <div class="grid">
      <nav>
        <button class="nav-item active" data-page="dashboard" onclick="showPage('dashboard')">📊 Дашборд <small>KPIs</small></button>
        <button class="nav-item" data-page="orders" onclick="showPage('orders')">📦 Заказы <small>поиск/фильтры</small></button>
        <button class="nav-item" data-page="products" onclick="showPage('products')">🏷️ Лоты <small>список</small></button>
        <button class="nav-item" data-page="clients" onclick="showPage('clients')">👤 Клиенты <small>история</small></button>
        <button class="nav-item" data-page="notifications" onclick="showPage('notifications')">🔔 Уведомления <small>новые</small></button>
        <button class="nav-item" data-page="settings" onclick="showPage('settings')">⚙️ Настройки <small>API ключ</small></button>
        <div class="muted" style="font-size:12px; margin-top:10px; line-height:1.35">
          Путь: <span class="mono">antikvar/crm/</span><br/>
          API: <span class="mono">antikvar/crm/api.php</span>
        </div>
      </nav>

      <main>
        <!-- Dashboard -->
        <section id="page-dashboard">
          <div class="card">
            <h2>KPIs</h2>
            <div class="row" style="margin-bottom:12px;">
              <input type="date" id="dash-from" />
              <input type="date" id="dash-to" />
              <select id="dash-period">
                <option value="day">День</option>
                <option value="week">Неделя</option>
                <option value="month">Месяц</option>
              </select>
              <button class="btn" onclick="loadDashboard()">Применить</button>
            </div>
            <div class="kpi">
              <div class="box">
                <div class="label">Выручка</div>
                <div class="value" id="kpi-revenue">—</div>
              </div>
              <div class="box">
                <div class="label">Заказов</div>
                <div class="value" id="kpi-orders">—</div>
              </div>
              <div class="box">
                <div class="label">Средний чек</div>
                <div class="value" id="kpi-avg">—</div>
              </div>
              <div class="box">
                <div class="label">Топ-лот по доходу</div>
                <div class="value" id="kpi-top">—</div>
              </div>
            </div>
          </div>

          <div class="two-col">
            <div class="card">
              <h2>График продаж</h2>
              <canvas id="sales-chart" height="120"></canvas>
            </div>
            <div class="card">
              <h2>Топ товаров</h2>
              <div class="muted" style="margin-bottom:8px;">Доход (из `order_items`)</div>
              <div style="overflow:auto; max-height:360px;">
                <table>
                  <thead>
                    <tr><th>Лот</th><th>Шт</th><th>Выручка</th></tr>
                  </thead>
                  <tbody id="top-products-body"></tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- Orders -->
        <section id="page-orders" class="hidden">
          <div class="card">
            <h2>Заказы</h2>
            <div class="row" style="margin-bottom:12px;">
              <input id="orders-q" placeholder="ID заказа или пользователь" />
              <select id="orders-status">
                <option value="">Все статусы</option>
                <option value="new">Новый</option>
                <option value="paid">Оплачен</option>
                <option value="shipped">Отправлен</option>
                <option value="completed">Завершен</option>
              </select>
              <input type="date" id="orders-from" />
              <input type="date" id="orders-to" />
              <button class="btn" onclick="loadOrders()">Найти</button>
            </div>
            <div style="overflow:auto;">
              <table>
                <thead>
                  <tr>
                    <th>ID</th><th>Статус</th><th>Покупатель</th><th>Сумма</th><th>Создан</th><th></th>
                  </tr>
                </thead>
                <tbody id="orders-body"></tbody>
              </table>
            </div>
          </div>

          <div class="card hidden" id="order-details-card">
            <h2>Детали заказа <span class="mono" id="order-details-id"></span></h2>
            <div class="row" style="margin-bottom:10px;">
              <select id="order-status-update">
                <option value="new">Новый</option>
                <option value="paid">Оплачен</option>
                <option value="shipped">Отправлен</option>
                <option value="completed">Завершен</option>
              </select>
              <button class="btn primary" onclick="updateOrderStatus()">Сохранить статус</button>
              <span class="muted" id="order-details-meta"></span>
            </div>
            <div style="overflow:auto;">
              <table>
                <thead><tr><th>Лот</th><th>Кол-во</th><th>Цена</th><th>Статус лота</th></tr></thead>
                <tbody id="order-items-body"></tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Products -->
        <section id="page-products" class="hidden">
          <div class="card">
            <h2>Лоты</h2>
            <div class="row" style="margin-bottom:12px;">
              <input id="products-q" placeholder="Поиск: название/артикул" />
              <select id="products-status">
                <option value="">Все статусы</option>
                <option value="listed">На продаже</option>
                <option value="finished">Завершены</option>
                <option value="draft">Черновик</option>
                <option value="deleted">Удален</option>
              </select>
              <button class="btn" onclick="loadProducts()">Найти</button>
            </div>
            <div style="overflow:auto; max-height:640px;">
              <table>
                <thead>
                  <tr>
                    <th>ID</th><th>Артикул</th><th>Название</th><th>Тип</th><th>Статус</th><th>Цена</th><th>Продано</th><th>Обновлено</th>
                  </tr>
                </thead>
                <tbody id="products-body"></tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Clients -->
        <section id="page-clients" class="hidden">
          <div class="card">
            <h2>Клиенты (покупатели)</h2>
            <div class="muted" style="margin-bottom:10px;">Сейчас строится на основе `orders.buyer_username`. Можно расширить при наличии метода API по заказам/покупателям.</div>
            <div style="overflow:auto; max-height:640px;">
              <table>
                <thead><tr><th>Покупатель</th><th>Заказов</th><th>Сумма</th><th>Последний заказ</th></tr></thead>
                <tbody id="clients-body"></tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Notifications -->
        <section id="page-notifications" class="hidden">
          <div class="card">
            <h2>Уведомления</h2>
            <div class="row" style="margin-bottom:12px;">
              <button class="btn" onclick="loadNotifications()">Обновить</button>
              <button class="btn" onclick="markAllRead()">Пометить все прочитанным</button>
              <span class="muted" id="notif-meta"></span>
            </div>
            <div style="overflow:auto; max-height:640px;">
              <table>
                <thead><tr><th>ID</th><th>Тип</th><th>Сообщение</th><th>Создано</th><th>Прочитано</th></tr></thead>
                <tbody id="notifications-body"></tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Settings -->
        <section id="page-settings" class="hidden">
          <div class="card">
            <h2>Настройки</h2>
            <div class="row" style="margin-bottom:12px;">
              <input id="set-api-key" placeholder="Meshok API key (Bearer)" style="min-width:340px;" />
              <input id="set-interval" type="number" min="1" placeholder="Синк, минут" style="width:160px;" />
              <button class="btn primary" onclick="saveSettings()">Сохранить</button>
            </div>
            <div class="muted">
              - API ключ хранится в таблице <span class="mono">antikvar_settings</span> (ключ <span class="mono">meshok_api_key</span>).<br/>
              - Cron: запланируйте запуск <span class="mono">antikvar/crm/cron/sync.php</span> раз в N минут.
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    const API = 'api.php';
    let salesChart = null;
    let lastNotifications = [];
    let currentOrderId = null;

    function fmtMoney(v){ return (Number(v)||0).toLocaleString('ru-RU', {maximumFractionDigits:2}) + ' ₽'; }
    function fmtDateTime(s){
      if(!s) return '—';
      const d = new Date(s);
      return isNaN(d) ? s : d.toLocaleString('ru-RU');
    }
    function statusBadge(status){
      const map = { new:['Новый','warn'], paid:['Оплачен','good'], shipped:['Отправлен','warn'], completed:['Завершен','good'] };
      const x = map[status] || [status,''];
      return `<span class="badge ${x[1]}">${x[0]}</span>`;
    }

    function showPage(page){
      document.querySelectorAll('nav .nav-item').forEach(b => b.classList.toggle('active', b.dataset.page===page));
      document.querySelectorAll('main section[id^="page-"]').forEach(s => s.classList.add('hidden'));
      document.getElementById('page-' + page).classList.remove('hidden');
      if(page==='dashboard') loadDashboard();
      if(page==='orders') loadOrders();
      if(page==='products') loadProducts();
      if(page==='clients') loadClients();
      if(page==='notifications') loadNotifications();
      if(page==='settings') loadSettings();
    }

    async function apiGet(action, params={}){
      const u = new URL(API, window.location.href);
      u.searchParams.set('action', action);
      Object.entries(params).forEach(([k,v]) => v!==undefined && v!==null && u.searchParams.set(k, v));
      const r = await fetch(u.toString());
      return await readJsonOrThrow(r);
    }

    async function apiPost(action, data){
      const u = new URL(API, window.location.href);
      u.searchParams.set('action', action);
      const r = await fetch(u.toString(), {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data||{})});
      return await readJsonOrThrow(r);
    }

    async function readJsonOrThrow(response){
      const ct = (response.headers.get('content-type') || '').toLowerCase();
      const text = await response.text();
      if(!ct.includes('application/json')){
        throw new Error(`API вернул не-JSON (HTTP ${response.status}). Ответ: ` + text.slice(0, 800));
      }
      try{
        return JSON.parse(text || '{}');
      }catch(e){
        throw new Error(`Не удалось распарсить JSON (HTTP ${response.status}). Тело: ` + text.slice(0, 800));
      }
    }

    async function loadAll(){
      await loadSettingsPill();
      await loadDashboard();
      await loadNotifications();
    }

    async function loadSettingsPill(){
      const s = await apiGet('settings_get');
      const last = s.last_sync_at ? new Date(Number(s.last_sync_at)*1000).toLocaleString('ru-RU') : '—';
      document.getElementById('pill-sync').textContent = 'Sync: ' + last;
    }

    async function syncNow(){
      try{
        const res = await apiPost('sync_now', {});
        if(res.error){ alert(res.error); return; }
        await loadSettingsPill();
        await loadDashboard();
        await loadOrders();
        await loadProducts();
        await loadNotifications();
      }catch(e){
        alert(String(e && e.message ? e.message : e));
      }
    }

    async function loadDashboard(){
      const from = document.getElementById('dash-from').value;
      const to = document.getElementById('dash-to').value;
      const period = document.getElementById('dash-period').value || 'day';

      const sum = await apiGet('analytics_summary', {from, to});
      document.getElementById('kpi-revenue').textContent = fmtMoney(sum.revenue);
      document.getElementById('kpi-orders').textContent = (sum.orders_count ?? 0).toLocaleString('ru-RU');
      document.getElementById('kpi-avg').textContent = fmtMoney(sum.avg_check);

      const top = await apiGet('analytics_top_products');
      const topBody = document.getElementById('top-products-body');
      topBody.innerHTML = (top.top||[]).slice(0,15).map(r => `
        <tr>
          <td title="${escapeHtml(r.name||'')}">${escapeHtml(trunc(r.name||'', 48))}</td>
          <td>${Number(r.qty||0).toLocaleString('ru-RU')}</td>
          <td style="font-weight:700">${fmtMoney(r.revenue||0)}</td>
        </tr>
      `).join('') || `<tr><td colspan="3" class="muted">Нет данных</td></tr>`;

      const best = (top.top||[])[0];
      document.getElementById('kpi-top').textContent = best ? trunc(best.name||'', 26) : '—';

      const series = await apiGet('analytics_sales_series', {period});
      renderSalesChart(series.series||[]);
    }

    function renderSalesChart(series){
      const labels = series.map(x => String(x.label));
      const data = series.map(x => Number(x.value||0));
      const ctx = document.getElementById('sales-chart');
      if(salesChart) salesChart.destroy();
      salesChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Выручка', data, borderColor: '#60a5fa', backgroundColor: 'rgba(96,165,250,.15)', tension: .25, fill: true }] },
        options: {
          responsive: true,
          plugins: { legend: { labels: { color: '#cbd5e1' } } },
          scales: {
            x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.12)' } },
            y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.12)' } },
          }
        }
      });
    }

    async function loadOrders(){
      const q = document.getElementById('orders-q').value || '';
      const status = document.getElementById('orders-status').value || '';
      const from = document.getElementById('orders-from').value || '';
      const to = document.getElementById('orders-to').value || '';
      const res = await apiGet('orders_list', {q, status, from, to});
      const body = document.getElementById('orders-body');
      body.innerHTML = (res.orders||[]).map(o => `
        <tr>
          <td class="mono">${o.meshok_order_id}</td>
          <td>${statusBadge(o.status)}</td>
          <td>${escapeHtml(o.buyer_username || '—')}</td>
          <td style="font-weight:700">${fmtMoney(o.total_amount||0)}</td>
          <td>${fmtDateTime(o.created_at)}</td>
          <td><button class="btn" onclick="openOrder(${o.meshok_order_id})">Открыть</button></td>
        </tr>
      `).join('') || `<tr><td colspan="6" class="muted">Нет заказов</td></tr>`;
    }

    async function openOrder(id){
      const res = await apiGet('order_get', {id});
      if(res.error){ alert(res.error); return; }
      currentOrderId = id;
      document.getElementById('order-details-card').classList.remove('hidden');
      document.getElementById('order-details-id').textContent = '#' + id;
      document.getElementById('order-status-update').value = res.order.status;
      document.getElementById('order-details-meta').textContent =
        `Сумма: ${fmtMoney(res.order.total_amount)} • Создан: ${fmtDateTime(res.order.created_at)}`;

      document.getElementById('order-items-body').innerHTML = (res.items||[]).map(i => `
        <tr>
          <td>
            <div style="font-weight:700">${escapeHtml(i.name||'')}</div>
            <div class="muted mono">Lot #${i.product_id}${i.internal_id ? (' • ' + escapeHtml(i.internal_id)) : ''}</div>
          </td>
          <td>${Number(i.quantity||1).toLocaleString('ru-RU')}</td>
          <td>${i.price==null ? '—' : fmtMoney(i.price)}</td>
          <td><span class="badge">${escapeHtml(i.status||'')}</span></td>
        </tr>
      `).join('') || `<tr><td colspan="4" class="muted">Нет позиций</td></tr>`;
      window.scrollTo({top: document.getElementById('order-details-card').offsetTop - 10, behavior:'smooth'});
    }

    async function updateOrderStatus(){
      if(!currentOrderId) return;
      const status = document.getElementById('order-status-update').value;
      const res = await apiPost('order_update_status', {id: currentOrderId, status});
      if(res.error){ alert(res.error); return; }
      await loadOrders();
      await openOrder(currentOrderId);
    }

    async function loadProducts(){
      const q = document.getElementById('products-q').value || '';
      const status = document.getElementById('products-status').value || '';
      const res = await apiGet('products_list', {q, status});
      const body = document.getElementById('products-body');
      body.innerHTML = (res.products||[]).map(p => `
        <tr>
          <td class="mono">${p.meshok_item_id}</td>
          <td class="mono muted">${escapeHtml(p.internal_id||'—')}</td>
          <td title="${escapeHtml(p.name||'')}">${escapeHtml(trunc(p.name||'', 60))}</td>
          <td><span class="badge">${escapeHtml(p.sale_type||'')}</span></td>
          <td><span class="badge">${escapeHtml(p.status||'')}</span></td>
          <td>${p.sale_type==='Auction' ? (p.current_price!=null ? fmtMoney(p.current_price) : '—') : (p.price!=null ? fmtMoney(p.price) : '—')}</td>
          <td>${p.sold==null ? '—' : Number(p.sold).toLocaleString('ru-RU')}</td>
          <td>${fmtDateTime(p.updated_at)}</td>
        </tr>
      `).join('') || `<tr><td colspan="8" class="muted">Нет лотов</td></tr>`;
    }

    async function loadClients(){
      const res = await apiGet('users_list');
      const body = document.getElementById('clients-body');
      body.innerHTML = (res.users||[]).map(u => `
        <tr>
          <td>${escapeHtml(u.username||'—')}</td>
          <td>${Number(u.orders_count||0).toLocaleString('ru-RU')}</td>
          <td style="font-weight:700">${fmtMoney(u.total_amount||0)}</td>
          <td>${fmtDateTime(u.last_order_at)}</td>
        </tr>
      `).join('') || `<tr><td colspan="4" class="muted">Нет данных</td></tr>`;
    }

    async function loadNotifications(){
      const res = await apiGet('notifications_list');
      lastNotifications = res.notifications || [];
      document.getElementById('notif-meta').textContent = `Показано: ${lastNotifications.length}`;
      const body = document.getElementById('notifications-body');
      body.innerHTML = lastNotifications.map(n => `
        <tr>
          <td class="mono">${n.id}</td>
          <td><span class="badge">${escapeHtml(n.type||'')}</span></td>
          <td>${escapeHtml(n.message||'')}</td>
          <td>${fmtDateTime(n.created_at)}</td>
          <td>${n.read_at ? fmtDateTime(n.read_at) : '<span class="badge warn">не прочитано</span>'}</td>
        </tr>
      `).join('') || `<tr><td colspan="5" class="muted">Нет уведомлений</td></tr>`;
    }

    async function markAllRead(){
      const ids = lastNotifications.filter(n => !n.read_at).map(n => Number(n.id));
      if(!ids.length) return;
      const res = await apiPost('notifications_mark_read', {ids});
      if(res.error){ alert(res.error); return; }
      await loadNotifications();
    }

    async function loadSettings(){
      const s = await apiGet('settings_get');
      document.getElementById('set-api-key').value = '';
      document.getElementById('set-api-key').placeholder = s.meshok_api_key_set ? 'Meshok API key уже сохранен (введите новый чтобы заменить)' : 'Meshok API key (Bearer)';
      document.getElementById('set-interval').value = s.sync_interval_minutes || 15;
    }

    async function saveSettings(){
      const apiKey = document.getElementById('set-api-key').value.trim();
      const syncInterval = Number(document.getElementById('set-interval').value || 15);
      const payload = { sync_interval_minutes: syncInterval };
      if(apiKey) payload.meshok_api_key = apiKey;
      const res = await apiPost('settings_set', payload);
      if(res.error){ alert(res.error); return; }
      alert('Сохранено');
      await loadSettingsPill();
      await loadSettings();
    }

    function trunc(s, n){ s=String(s||''); return s.length>n ? (s.slice(0,n-1)+'…') : s; }
    function escapeHtml(s){
      return String(s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;");
    }

    // init defaults
    (function(){
      const today = new Date();
      const to = today.toISOString().slice(0,10);
      const fromDt = new Date(today.getTime() - 30*24*3600*1000);
      const from = fromDt.toISOString().slice(0,10);
      document.getElementById('dash-from').value = from;
      document.getElementById('dash-to').value = to;
      loadAll();
    })();
  </script>
</body>
</html>

