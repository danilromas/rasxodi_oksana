<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аренда и Склад - Оксана Владимировна</title>
    <style>
        /* Общие стили из index.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1600px; margin: 0 auto; }
        header { text-align: center; margin-bottom: 30px; color: white; }
        header h1 { font-size: 2.5em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }

        .nav-tabs { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
        .nav-tab {
            padding: 15px 30px; background: rgba(255,255,255,0.2); border: none; border-radius: 50px;
            color: white; font-size: 16px; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);
        }
        .nav-tab:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .nav-tab.active { background: white; color: #1e3c72; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }

        .section { display: none; animation: fadeIn 0.5s ease; }
        .section.active { display: block; }

        .earnings-summary {
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .earnings-box { text-align: center; }
        .earnings-value { font-size: 1.8em; font-weight: bold; color: #28a745; }
        .earnings-label { font-size: 0.9em; color: #666; }
        
        /* Стили для графиков */
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            min-height: 450px;
            display: flex;
            flex-direction: column;
        }
        .chart-card h3 { margin-bottom: 20px; text-align: center; color: #333; font-size: 1.2em; }
        .chart-wrapper {
            flex: 1;
            position: relative;
            min-height: 350px;
        }
        canvas { width: 100% !important; height: 100% !important; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Стили таблиц и блоков */
        .card-block {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1); margin-bottom: 30px;
        }
        .data-table-container { overflow-x: auto; margin-top: 20px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.9em; }
        .data-table th {
            background: #f8f9fa; padding: 12px 8px; text-align: left;
            font-weight: 600; color: #555; border-bottom: 2px solid #e0e0e0;
        }
        .data-table td { padding: 10px 8px; border-bottom: 1px solid #eee; }
        .data-table tr:hover { background: #f8f9fa; }
        .data-table tr.debtor { background: #fff5f5; }
        .data-table tr.debtor td { color: #c62828; }
        .data-table tr.warning { background: #fff9db; }
        .data-table tr.warning td { color: #856404; }

        .badge-warning { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.8em; }

        /* Итоговые строки */
        .summary-row { background: #f1f3f5 !important; font-weight: bold; }
        .summary-row td { border-top: 2px solid #dee2e6; }

        /* Кнопки */
        .btn-primary {
            padding: 10px 20px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4); }
        .btn-success { background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }
        .btn-secondary { background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }

        /* Модальные окна */
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(5px);
        }
        .modal.active { display: flex; }
        .modal-content {
            background: white; padding: 30px; border-radius: 20px; width: 95%; max-width: 800px;
            max-height: 90vh; overflow-y: auto;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        /* Склад комплектующих */
        .inventory-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .inventory-card { background: #f8f9fa; padding: 15px; border-radius: 12px; border: 1px solid #eee; }
        .inventory-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .inventory-item input { width: 60px; padding: 5px; text-align: center; }

        .back-link { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; font-size: 1.1em; }

        /* Тултип комментария */
        #comment-tooltip {
            position: fixed;
            display: none;
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 2000;
            max-width: 300px;
            pointer-events: none;
            border: 1px solid #eee;
            animation: tooltipFadeIn 0.2s ease;
        }
        #comment-tooltip::before {
            content: '💬';
            display: block;
            margin-bottom: 5px;
            font-size: 1.2em;
        }
        #comment-tooltip-text {
            color: #333;
            font-size: 0.95em;
            line-height: 1.4;
            white-space: pre-wrap;
        }
        @keyframes tooltipFadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <a href="index.php" class="back-link">← Главная</a>
    <div class="container">
        <header>
            <h1>🏗️ Аренда и Склад</h1>
            <div style="margin-top: 10px; color: rgba(255,255,255,0.8);">
                Расчетная дата: <input type="date" id="calc-date" onchange="loadRentals()" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
            </div>
        </header>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('rentals')">📦 Склад (Аренды)</button>
            <button class="nav-tab" onclick="showSection('debtors')">⚠️ Должники</button>
            <button class="nav-tab" onclick="showSection('completed')">✅ Завершенные</button>
            <button class="nav-tab" onclick="showSection('inventory')">🛠️ Остатки на складе</button>
        </div>

        <!-- Раздел Аренды -->
        <div id="rentals-section" class="section active">
            <div class="card-block">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Активные аренды</h2>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="rental-search" placeholder="Поиск (имя, договор, тел)..." oninput="filterRentals()" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; width: 250px;">
                        <select id="rental-filter-type" onchange="filterRentals()" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            <option value="all">Все типы</option>
                            <option value="ramnye">Рамные</option>
                            <option value="vyshka">Вышка</option>
                        </select>
                        <button class="btn-primary" onclick="openRentalModal()">➕ Новая аренда</button>
                    </div>
                </div>

                <div class="data-table-container">
                    <table class="data-table" id="rentals-table">
                        <thead>
                            <tr>
                                <th>Тип</th>
                                <th>Клиент</th>
                                <th>Договор/Акт</th>
                                <th>Дата начала</th>
                                <th>Дней</th>
                                <th>Цена/день</th>
                                <th>Сумма аренды</th>
                                <th>Залог</th>
                                <th>Оплачено</th>
                                <th>Остаток</th>
                                <th>м²</th>
                                <th>Телефон</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="rentals-body"></tbody>
                        <tfoot id="rentals-footer"></tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Раздел Должники -->
        <div id="debtors-section" class="section">
            <div class="card-block">
                <h2>Аренды с отрицательным остатком</h2>
                <div class="data-table-container">
                    <table class="data-table" id="debtors-table">
                        <thead>
                            <tr>
                                <th>Тип</th>
                                <th>Клиент</th>
                                <th>Договор/Акт</th>
                                <th>Дней</th>
                                <th>Сумма аренды</th>
                                <th>Залог</th>
                                <th>Оплачено</th>
                                <th>Остаток</th>
                                <th>Телефон</th>
                                <th>Дата звонка</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="debtors-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Раздел Завершенные -->
        <div id="completed-section" class="section">
            <div class="earnings-summary">
                <div class="earnings-box">
                    <div class="earnings-label">Период расчета прибыли</div>
                    <div style="display:flex; gap:10px; margin-top:5px;">
                        <input type="date" id="profit-start" onchange="calculateProfit()" style="padding:5px; border-radius:5px; border:1px solid #ddd;">
                        <span style="color:#666">до</span>
                        <input type="date" id="profit-end" onchange="calculateProfit()" style="padding:5px; border-radius:5px; border:1px solid #ddd;">
                    </div>
                </div>
                <div class="earnings-box">
                    <div class="earnings-label">Всего заработано (по оплатам)</div>
                    <div class="earnings-value" id="total-profit">0 ₽</div>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <h3>Прибыль по месяцам</h3>
                    <div class="chart-wrapper">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Количество аренд по типам</h3>
                    <div class="chart-wrapper">
                        <canvas id="typesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card-block">
                <h2>Завершенные аренды</h2>
                <div class="data-table-container">
                    <table class="data-table" id="completed-table">
                        <thead>
                            <tr>
                                <th>Тип</th>
                                <th>Клиент</th>
                                <th>Договор/Акт</th>
                                <th>Дата начала</th>
                                <th>Дата завершения</th>
                                <th>Сумма аренды</th>
                                <th>Оплачено</th>
                                <th>Телефон</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="completed-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Раздел Инвентарь -->
        <div id="inventory-section" class="section">
            <div class="card-block">
                <h2>Остатки комплектующих</h2>
                <div class="inventory-grid" id="inventory-container"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно аренды -->
    <div class="modal" id="rental-modal">
        <div class="modal-content">
            <h2 id="rental-modal-title" style="margin-bottom: 20px;">Новая аренда</h2>
            <form id="rental-form">
                <input type="hidden" id="rental-id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Тип оборудования</label>
                        <select id="rental-type" required onchange="toggleFields()">
                            <option value="ramnye">Рамные леса</option>
                            <option value="vyshka">Вышка-тура</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Клиент</label>
                        <input type="text" id="rental-client" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Договор</label>
                        <input type="text" id="rental-dogovor">
                    </div>
                    <div class="form-group">
                        <label>Акт</label>
                        <input type="text" id="rental-akt">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата начала</label>
                        <input type="date" id="rental-date-start" required>
                    </div>
                    <div class="form-group">
                        <label>Цена за сутки (₽)</label>
                        <input type="number" id="rental-daily-rate" value="0" step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Залог (₽)</label>
                        <input type="number" id="rental-deposit" value="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Оплачено за аренду (₽)</label>
                        <input type="number" id="rental-paid-rent" value="0" step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" id="field-square-meters">
                        <label>Квадратные метры (м²)</label>
                        <input type="number" id="rental-square-meters" value="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="text" id="rental-phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата последнего платежа</label>
                        <input type="date" id="rental-last-payment">
                    </div>
                    <div class="form-group">
                        <label>Дата звонка</label>
                        <input type="date" id="rental-call-date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Комментарий</label>
                    <textarea id="rental-comment" rows="2"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeModal('rental-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно завершения аренды -->
    <div class="modal" id="close-rental-modal">
        <div class="modal-content" style="max-width: 500px;">
            <h2 style="margin-bottom: 20px;">Завершить аренду</h2>
            <div id="close-rental-info" style="margin-bottom: 20px; line-height: 1.6; background: #f8f9fa; padding: 15px; border-radius: 10px;"></div>
            <form id="close-rental-form">
                <input type="hidden" id="close-rental-id">
                <div class="form-group">
                    <label>Дата завершения</label>
                    <input type="date" id="close-rental-date" required onchange="updateCloseInfo()">
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeModal('close-rental-modal')">Отмена</button>
                    <button type="submit" class="btn-success">Завершить и закрыть</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно добора/изменения -->
    <div class="modal" id="adjustment-modal">
        <div class="modal-content" style="max-width: 600px;">
            <h2 style="margin-bottom: 20px;">Добор лесов / Изменение условий</h2>
            <div id="adjustment-info" style="margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 10px; font-size: 0.9em;"></div>
            
            <form id="adjustment-form">
                <input type="hidden" id="adj-rental-id">
                <div class="form-group">
                    <label>Дата изменения</label>
                    <input type="date" id="adj-date" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Новая цена за сутки (₽)</label>
                        <input type="number" id="adj-daily-rate" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Новые м² (всего)</label>
                        <input type="number" id="adj-square-meters" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Комментарий (что изменилось)</label>
                    <textarea id="adj-comment" rows="2" placeholder="Например: Добор 50м2"></textarea>
                </div>
                
                <div style="margin-top: 20px;">
                    <h4 style="margin-bottom: 10px;">История изменений:</h4>
                    <div id="adjustment-history" style="max-height: 150px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 10px;"></div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeModal('adjustment-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Тултип для комментариев -->
    <div id="comment-tooltip">
        <div id="comment-tooltip-text"></div>
    </div>

    <script>
        const API_URL = 'api_arenda.php';
        let rentals = [];
        let inventory = [];
        let earningsChart = null;
        let typesChart = null;

        function showComment(event, comment) {
            if (!comment || comment.trim() === '') return;
            const tooltip = document.getElementById('comment-tooltip');
            const text = document.getElementById('comment-tooltip-text');
            text.textContent = comment;
            tooltip.style.display = 'block';
            
            // Позиционируем рядом с курсором
            const x = event.clientX + 15;
            const y = event.clientY + 15;
            
            // Проверка границ экрана
            const width = tooltip.offsetWidth;
            const height = tooltip.offsetHeight;
            const finalX = (x + width > window.innerWidth) ? x - width - 30 : x;
            const finalY = (y + height > window.innerHeight) ? y - height - 30 : y;
            
            tooltip.style.left = finalX + 'px';
            tooltip.style.top = finalY + 'px';
        }

        function hideComment() {
            document.getElementById('comment-tooltip').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('calc-date').value = today;
            document.getElementById('profit-start').value = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
            document.getElementById('profit-end').value = today;
            loadData();
        });

        async function loadData() {
            await Promise.all([loadRentals(), loadInventory()]);
        }

        async function loadRentals() {
            try {
                const res = await fetch(`${API_URL}?action=get_rentals`);
                rentals = await res.json();
                renderRentals();
                renderDebtors();
                renderCompleted();
                calculateProfit();
            } catch (err) { console.error(err); }
        }

        async function loadInventory() {
            try {
                const res = await fetch(`${API_URL}?action=get_inventory`);
                inventory = await res.json();
                renderInventory();
            } catch (err) { console.error(err); }
        }

        function showSection(section) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.getElementById(`${section}-section`).classList.add('active');
            
            // Находим кнопку таба по тексту или по атрибуту onclick
            const tabs = document.querySelectorAll('.nav-tab');
            tabs.forEach(t => {
                if (t.getAttribute('onclick').includes(`'${section}'`)) {
                    t.classList.add('active');
                }
            });
        }

        function calculateRentalStats(r, targetDateStr) {
            const targetDate = new Date(targetDateStr);
            targetDate.setHours(0, 0, 0, 0);
            
            const startDate = new Date(r.date_start);
            startDate.setHours(0, 0, 0, 0);
            
            if (targetDate < startDate) return { totalCost: 0, currentRate: 0, currentM2: 0, daysUsed: 0 };

            // Сортируем корректировки по дате
            const adjs = (r.adjustments || []).map(a => ({
                ...a,
                date: new Date(a.date_change)
            })).sort((a, b) => a.date - b.date);

            let totalCost = 0;
            let currentRate = parseFloat(r.daily_rate || 0);
            let currentM2 = parseFloat(r.square_meters || 0);
            let currentDate = new Date(startDate);

            for (const adj of adjs) {
                if (adj.date > targetDate) break;
                if (adj.date <= startDate) {
                    // Если корректировка в день начала или раньше, просто обновляем начальные значения
                    currentRate = parseFloat(adj.new_daily_rate);
                    currentM2 = parseFloat(adj.new_square_meters);
                    continue;
                }

                // Считаем дни до этой корректировки
                const days = Math.floor((adj.date - currentDate) / (1000 * 60 * 60 * 24));
                totalCost += days * currentRate;
                
                currentRate = parseFloat(adj.new_daily_rate);
                currentM2 = parseFloat(adj.new_square_meters);
                currentDate = new Date(adj.date);
            }

            // Считаем оставшиеся дни до целевой даты (включая целевую дату)
            const remainingDays = Math.floor((targetDate - currentDate) / (1000 * 60 * 60 * 24)) + 1;
            totalCost += remainingDays * currentRate;

            const totalDays = Math.floor((targetDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

            return {
                totalCost,
                currentRate,
                currentM2,
                daysUsed: totalDays
            };
        }

        function renderRentals() {
            const tbody = document.getElementById('rentals-body');
            const calcDateStr = document.getElementById('calc-date').value;
            const search = document.getElementById('rental-search').value.toLowerCase();
            const filterType = document.getElementById('rental-filter-type').value;

            let filtered = rentals.filter(r => r.status === 'active');
            
            if (search) {
                filtered = filtered.filter(r => 
                    r.client_name.toLowerCase().includes(search) || 
                    (r.dogovor && r.dogovor.toLowerCase().includes(search)) || 
                    (r.phone && r.phone.includes(search))
                );
            }
            if (filterType !== 'all') {
                filtered = filtered.filter(r => r.type === filterType);
            }

            let totals = { 
                ramnye: 0, vyshka: 0,
                daily: { ramnye: 0, vyshka: 0, total: 0 }, 
                m2: 0 
            };

            tbody.innerHTML = filtered.map(r => {
                const stats = calculateRentalStats(r, calcDateStr);
                const sumRent = stats.totalCost;
                const remainder = parseFloat(r.deposit) + parseFloat(r.paid_rent) - sumRent;
                const paidRentBalance = parseFloat(r.paid_rent) - sumRent;
                
                // Отдельно считаем:
                // 1) сколько дней до начала расходования залога
                // 2) сколько дней до полного ухода в долг
                const daysUntilDeposit = stats.currentRate > 0 ? Math.ceil(paidRentBalance / stats.currentRate) : Infinity;
                const daysUntilDebt = stats.currentRate > 0 ? Math.ceil(remainder / stats.currentRate) : Infinity;
                
                let rowClass = '';
                let statusBadge = '';
                
                if (remainder < 0) {
                    rowClass = 'debtor';
                    statusBadge = '<span class="badge-warning">Долг</span>';
                } else if (paidRentBalance < 0) {
                    rowClass = 'debtor';
                    statusBadge = daysUntilDebt <= 3
                        ? `<span class="badge-warning">До долга: ${Math.max(0, daysUntilDebt)} дн.</span>`
                        : '<span class="badge-warning">Тратится залог</span>';
                } else if (daysUntilDeposit <= 3) {
                    rowClass = 'warning';
                    statusBadge = `<span class="badge-warning">До залога: ${Math.max(0, daysUntilDeposit)} дн.</span>`;
                }

                totals[r.type] += remainder;
                totals.daily[r.type] += stats.currentRate;
                totals.daily.total += stats.currentRate;
                totals.m2 += stats.currentM2;

                const commentEscaped = r.comment ? r.comment.replace(/'/g, "\\'").replace(/"/g, "&quot;").replace(/\n/g, "\\n").replace(/\r/g, "") : '';

                return `
                    <tr class="${rowClass}" 
                        onmouseenter="showComment(event, '${commentEscaped}')" 
                        onmouseleave="hideComment()"
                        style="${r.comment ? 'cursor: help;' : ''}">
                        <td>${getTypeLabel(r.type)}</td>
                        <td>${r.client_name} ${statusBadge}</td>
                        <td>${r.dogovor || ''} ${r.akt ? '/ ' + r.akt : ''}</td>
                        <td>${formatDate(r.date_start)}</td>
                        <td>${stats.daysUsed}</td>
                        <td>${formatMoney(stats.currentRate)}</td>
                        <td>${formatMoney(sumRent)}</td>
                        <td>${formatMoney(r.deposit)}</td>
                        <td>${formatMoney(r.paid_rent)}</td>
                        <td style="font-weight:bold">${formatMoney(remainder)}</td>
                        <td>${stats.currentM2.toFixed(2)}</td>
                        <td>${r.phone || ''}</td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <button class="btn-primary" onclick="toggleDebtor(${r.id})" title="${r.is_debtor ? 'Убрать из должников' : 'В должники'}">
                                    ${r.is_debtor ? '👤✅' : '👤❌'}
                                </button>
                                <button class="btn-success" onclick="closeRental(${r.id})" title="Закрыть">✅</button>
                                <button class="btn-secondary" onclick="openAdjustmentModal(${r.id})" title="Добор / Изменение">🏗️+</button>
                                <button class="btn-secondary" onclick="editRental(${r.id})" title="Редактировать">✏️</button>
                                <button class="btn-danger" onclick="deleteRental(${r.id})" title="Удалить">🗑️</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            renderFooter(totals);
        }

        function renderFooter(totals) {
            const tfoot = document.getElementById('rentals-footer');
            tfoot.innerHTML = `
                <tr class="summary-row">
                    <td colspan="5">К возврату:</td>
                    <td colspan="4">
                        Рамные: ${formatMoney(totals.ramnye)} | Вышки: ${formatMoney(totals.vyshka)}
                    </td>
                    <td colspan="4">Общее к возврату: ${formatMoney(totals.ramnye + totals.vyshka)}</td>
                </tr>
                <tr class="summary-row">
                    <td colspan="5">Аренда в сутки:</td>
                    <td colspan="4">
                        Рамные: ${formatMoney(totals.daily.ramnye)} | Вышки: ${formatMoney(totals.daily.vyshka)}
                    </td>
                    <td colspan="4">Общая аренда в сутки: ${formatMoney(totals.daily.total)} | Итого м²: ${totals.m2.toFixed(2)}</td>
                </tr>
            `;
        }

        function renderDebtors() {
            const tbody = document.getElementById('debtors-body');
            const calcDateStr = document.getElementById('calc-date').value;
            
            // Фильтруем ТОЛЬКО тех, кто отмечен как должник вручную
            const debtors = rentals.filter(r => r.status === 'active' && r.is_debtor);

            tbody.innerHTML = debtors.map(r => {
                const stats = calculateRentalStats(r, calcDateStr);
                const sumRent = stats.totalCost;
                const remainder = parseFloat(r.deposit) + parseFloat(r.paid_rent) - sumRent;
                const commentEscaped = r.comment ? r.comment.replace(/'/g, "\\'").replace(/"/g, "&quot;").replace(/\n/g, "\\n").replace(/\r/g, "") : '';

                return `
                    <tr class="debtor" 
                        onmouseenter="showComment(event, '${commentEscaped}')" 
                        onmouseleave="hideComment()"
                        style="${r.comment ? 'cursor: help;' : ''}">
                        <td>${getTypeLabel(r.type)}</td>
                        <td>${r.client_name}</td>
                        <td>${r.dogovor || ''}</td>
                        <td>${stats.daysUsed}</td>
                        <td>${formatMoney(sumRent)}</td>
                        <td>${formatMoney(r.deposit)}</td>
                        <td>${formatMoney(r.paid_rent)}</td>
                        <td style="font-weight:bold">${formatMoney(remainder)}</td>
                        <td>${r.phone || ''}</td>
                        <td><input type="date" value="${r.call_date || ''}" onchange="updateCallDate(${r.id}, this.value)"></td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <button class="btn-primary" onclick="toggleDebtor(${r.id})" title="Убрать из должников">👤✅</button>
                                <button class="btn-secondary" onclick="editRental(${r.id})">✏️</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderCompleted() {
            const tbody = document.getElementById('completed-body');
            const completed = rentals.filter(r => r.status === 'closed');

            tbody.innerHTML = completed.map(r => {
                const stats = calculateRentalStats(r, r.date_end);
                const sumRent = stats.totalCost;
                const commentEscaped = r.comment ? r.comment.replace(/'/g, "\\'").replace(/"/g, "&quot;").replace(/\n/g, "\\n").replace(/\r/g, "") : '';

                return `
                    <tr onmouseenter="showComment(event, '${commentEscaped}')" 
                        onmouseleave="hideComment()"
                        style="${r.comment ? 'cursor: help;' : ''}">
                        <td>${getTypeLabel(r.type)}</td>
                        <td>${r.client_name}</td>
                        <td>${r.dogovor || ''}</td>
                        <td>${formatDate(r.date_start)}</td>
                        <td>${formatDate(r.date_end)}</td>
                        <td>${formatMoney(sumRent)}</td>
                        <td>${formatMoney(r.paid_rent)}</td>
                        <td>${r.phone || ''}</td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <button class="btn-secondary" onclick="editRental(${r.id})" title="Редактировать">✏️</button>
                                <button class="btn-danger" onclick="deleteRental(${r.id})" title="Удалить">🗑️</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function calculateProfit() {
            const startStr = document.getElementById('profit-start').value;
            const endStr = document.getElementById('profit-end').value;
            if (!startStr || !endStr) return;

            const periodStart = new Date(startStr);
            periodStart.setHours(0, 0, 0, 0);
            const periodEnd = new Date(endStr);
            periodEnd.setHours(23, 59, 59, 999);

            const totalProfit = rentals.reduce((sum, r) => {
                const rentalStart = new Date(r.date_start);
                rentalStart.setHours(0, 0, 0, 0);
                const rentalEnd = r.status === 'closed' && r.date_end ? new Date(r.date_end) : new Date();
                rentalEnd.setHours(0, 0, 0, 0);
                
                const startOverlap = new Date(Math.max(rentalStart, periodStart));
                const endOverlap = new Date(Math.min(rentalEnd, periodEnd));
                
                if (startOverlap > endOverlap) return sum;

                // Считаем общую стоимость на конец периода и вычитаем стоимость на день до начала периода
                const statsEnd = calculateRentalStats(r, endOverlap.toISOString().split('T')[0]);
                
                const dayBeforeStart = new Date(startOverlap);
                dayBeforeStart.setDate(dayBeforeStart.getDate() - 1);
                const statsStart = calculateRentalStats(r, dayBeforeStart.toISOString().split('T')[0]);
                
                const earnedInPeriod = statsEnd.totalCost - statsStart.totalCost;
                return sum + earnedInPeriod;
            }, 0);

            document.getElementById('total-profit').textContent = formatMoney(totalProfit);
            renderCharts();
        }

        function renderCharts() {
            // 1. Прибыль по месяцам (учитываем все аренды пропорционально дням)
            const monthlyData = {};
            const monthNames = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
            
            // Генерируем последние 6 месяцев
            const today = new Date();
            for (let i = 5; i >= 0; i--) {
                const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
                const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                
                // Начало и конец этого месяца для расчета
                const monthStart = new Date(d.getFullYear(), d.getMonth(), 1);
                monthStart.setHours(0, 0, 0, 0);
                const monthEnd = new Date(d.getFullYear(), d.getMonth() + 1, 0);
                monthEnd.setHours(23, 59, 59, 999);
                
                let monthProfit = 0;
                rentals.forEach(r => {
                    const rentalStart = new Date(r.date_start);
                    rentalStart.setHours(0, 0, 0, 0);
                    const rentalEnd = r.status === 'closed' && r.date_end ? new Date(r.date_end) : new Date();
                    rentalEnd.setHours(0, 0, 0, 0);
                    
                    const startOverlap = new Date(Math.max(rentalStart, monthStart));
                    const endOverlap = new Date(Math.min(rentalEnd, monthEnd));
                    
                    if (startOverlap <= endOverlap) {
                        const statsEnd = calculateRentalStats(r, endOverlap.toISOString().split('T')[0]);
                        const dayBeforeStart = new Date(startOverlap);
                        dayBeforeStart.setDate(dayBeforeStart.getDate() - 1);
                        const statsStart = calculateRentalStats(r, dayBeforeStart.toISOString().split('T')[0]);
                        monthProfit += (statsEnd.totalCost - statsStart.totalCost);
                    }
                });

                monthlyData[key] = { 
                    label: `${monthNames[d.getMonth()]} ${d.getFullYear()}`, 
                    value: monthProfit 
                };
            }

            const labels = Object.values(monthlyData).map(d => d.label);
            const values = Object.values(monthlyData).map(d => d.value);

            if (earningsChart) earningsChart.destroy();
            const ctx1 = document.getElementById('earningsChart').getContext('2d');
            earningsChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Заработано (₽)',
                        data: values,
                        backgroundColor: 'rgba(40, 167, 69, 0.6)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Заработано: ${formatMoney(context.raw)}`
                            }
                        }
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            ticks: { callback: (value) => formatMoney(value) }
                        } 
                    }
                }
            });

            // 2. Распределение по типам (все аренды)
            const typeCounts = { ramnye: 0, vyshka: 0 };
            rentals.forEach(r => {
                if (typeCounts[r.type] !== undefined) typeCounts[r.type]++;
            });

            if (typesChart) typesChart.destroy();
            const ctx2 = document.getElementById('typesChart').getContext('2d');
            typesChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Рамные', 'Вышка'],
                    datasets: [{
                        data: [typeCounts.ramnye, typeCounts.vyshka],
                        backgroundColor: [
                            'rgba(30, 60, 114, 0.8)',
                            'rgba(42, 82, 152, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        function renderInventory() {
            const container = document.getElementById('inventory-container');
            const categories = {
                ramnye: 'Рамные леса',
                vyshka: 'Вышка-тура'
            };

            container.innerHTML = Object.entries(categories).map(([cat, label]) => {
                const items = inventory.filter(i => i.category === cat);
                return `
                    <div class="inventory-card">
                        <h3>${label}</h3>
                        <div style="margin-top:10px;">
                            ${items.map(item => `
                                <div class="inventory-item">
                                    <span>${item.name}</span>
                                    <input type="number" value="${item.quantity}" onchange="updateInventory(${item.id}, this.value)">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Вспомогательные функции
        function getTypeLabel(type) {
            const types = { ramnye: 'Рамные', vyshka: 'Вышка' };
            return types[type] || type;
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(amount);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('ru-RU');
        }

        function toggleFields() {
            const type = document.getElementById('rental-type').value;
            document.getElementById('field-square-meters').style.display = type === 'ramnye' ? 'block' : 'none';
        }

        function openRentalModal(id = null) {
            const modal = document.getElementById('rental-modal');
            const form = document.getElementById('rental-form');
            form.reset();
            document.getElementById('rental-id').value = '';
            document.getElementById('rental-modal-title').textContent = 'Новая аренда';
            
            if (id) {
                const r = rentals.find(rent => rent.id == id);
                if (r) {
                    document.getElementById('rental-id').value = r.id;
                    document.getElementById('rental-type').value = r.type;
                    document.getElementById('rental-client').value = r.client_name;
                    document.getElementById('rental-dogovor').value = r.dogovor;
                    document.getElementById('rental-akt').value = r.akt;
                    document.getElementById('rental-date-start').value = r.date_start;
                    document.getElementById('rental-daily-rate').value = r.daily_rate;
                    document.getElementById('rental-deposit').value = r.deposit;
                    document.getElementById('rental-paid-rent').value = r.paid_rent;
                    document.getElementById('rental-square-meters').value = r.square_meters;
                    document.getElementById('rental-phone').value = r.phone;
                    document.getElementById('rental-last-payment').value = r.last_payment_date;
                    document.getElementById('rental-call-date').value = r.call_date;
                    document.getElementById('rental-comment').value = r.comment;
                    
                    const idField = document.getElementById('rental-id');
                    idField.dataset.is_debtor = r.is_debtor || 0;
                    idField.dataset.status = r.status || 'active';
                    idField.dataset.date_end = r.date_end || '';
                    
                    document.getElementById('rental-modal-title').textContent = 'Редактировать аренду';
                }
            } else {
                document.getElementById('rental-date-start').value = new Date().toISOString().split('T')[0];
                const idField = document.getElementById('rental-id');
                idField.dataset.is_debtor = 0;
                idField.dataset.status = 'active';
                idField.dataset.date_end = '';
            }
            toggleFields();
            modal.classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.getElementById('rental-form').onsubmit = async (e) => {
            e.preventDefault();
            const idField = document.getElementById('rental-id');
            const data = {
                id: idField.value,
                type: document.getElementById('rental-type').value,
                client_name: document.getElementById('rental-client').value,
                dogovor: document.getElementById('rental-dogovor').value,
                akt: document.getElementById('rental-akt').value,
                date_start: document.getElementById('rental-date-start').value,
                daily_rate: document.getElementById('rental-daily-rate').value,
                deposit: document.getElementById('rental-deposit').value,
                paid_rent: document.getElementById('rental-paid-rent').value,
                square_meters: document.getElementById('rental-square-meters').value,
                phone: document.getElementById('rental-phone').value,
                last_payment_date: document.getElementById('rental-last-payment').value,
                call_date: document.getElementById('rental-call-date').value,
                comment: document.getElementById('rental-comment').value,
                is_debtor: idField.dataset.is_debtor,
                status: idField.dataset.status,
                date_end: idField.dataset.date_end
            };

            await fetch(`${API_URL}?action=save_rental`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            closeModal('rental-modal');
            loadRentals();
        };

        async function updateInventory(id, quantity) {
            await fetch(`${API_URL}?action=update_inventory`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, quantity })
            });
            loadInventory();
        }

        async function updateCallDate(id, date) {
            const r = rentals.find(rent => rent.id == id);
            if (r) {
                r.call_date = date;
                await fetch(`${API_URL}?action=save_rental`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(r)
                });
            }
        }

        function updateCloseInfo() {
            const id = document.getElementById('close-rental-id').value;
            const closeDateStr = document.getElementById('close-rental-date').value;
            const r = rentals.find(rent => rent.id == id);
            if (!r || !closeDateStr) return;

            const stats = calculateRentalStats(r, closeDateStr);
            const sumRentUsed = stats.totalCost;
            
            const remainder = parseFloat(r.deposit) + parseFloat(r.paid_rent) - sumRentUsed;

            document.getElementById('close-rental-info').innerHTML = `
                <strong>Клиент:</strong> ${r.client_name}<br>
                <strong>Дней аренды:</strong> ${stats.daysUsed}<br>
                <strong>Стоимость аренды:</strong> ${formatMoney(sumRentUsed)}<br>
                <strong>Оплачено за аренду:</strong> ${formatMoney(r.paid_rent)}<br>
                <strong>Залог:</strong> ${formatMoney(r.deposit)}<br><br>
                <strong style="${remainder < 0 ? 'color: #dc3545;' : 'color: #28a745;'} font-size: 1.2em;">
                    ${remainder < 0 ? 'ДОЛГ КЛИЕНТА' : 'К ВОЗВРАТУ'}: ${formatMoney(Math.abs(remainder))}
                </strong>
            `;
        }

        async function openAdjustmentModal(id) {
            const r = rentals.find(rent => rent.id == id);
            if (!r) return;

            document.getElementById('adj-rental-id').value = id;
            document.getElementById('adj-date').value = new Date().toISOString().split('T')[0];
            
            // Текущие показатели из статистики на сегодня
            const today = new Date().toISOString().split('T')[0];
            const stats = calculateRentalStats(r, today);
            
            document.getElementById('adj-daily-rate').value = stats.currentRate;
            document.getElementById('adj-square-meters').value = stats.currentM2;
            document.getElementById('adj-comment').value = '';

            document.getElementById('adjustment-info').innerHTML = `
                <strong>Клиент:</strong> ${r.client_name}<br>
                <strong>Текущие условия:</strong> ${formatMoney(stats.currentRate)}/день, ${stats.currentM2} м²
            `;

            // История
            const historyDiv = document.getElementById('adjustment-history');
            if (r.adjustments && r.adjustments.length > 0) {
                historyDiv.innerHTML = r.adjustments.map(a => `
                    <div style="font-size: 0.85em; margin-bottom: 5px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px;">
                        <strong>${formatDate(a.date_change)}</strong>: ${formatMoney(a.new_daily_rate)}/день, ${a.new_square_meters} м²
                        ${a.comment ? `<br><span style="color: #666;">${a.comment}</span>` : ''}
                        <button class="btn-danger" style="padding: 2px 5px; font-size: 0.8em; margin-left: 10px;" onclick="deleteAdjustment(${a.id})">🗑️</button>
                    </div>
                `).join('');
            } else {
                historyDiv.innerHTML = '<div style="color: #999;">Нет изменений</div>';
            }

            document.getElementById('adjustment-modal').classList.add('active');
        }

        document.getElementById('adjustment-form').onsubmit = async (e) => {
            e.preventDefault();
            const data = {
                rental_id: document.getElementById('adj-rental-id').value,
                date_change: document.getElementById('adj-date').value,
                new_daily_rate: document.getElementById('adj-daily-rate').value,
                new_square_meters: document.getElementById('adj-square-meters').value,
                comment: document.getElementById('adj-comment').value
            };

            await fetch(`${API_URL}?action=save_adjustment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            closeModal('adjustment-modal');
            loadRentals();
        };

        async function deleteAdjustment(id) {
            if (confirm('Удалить это изменение?')) {
                await fetch(`${API_URL}?action=delete_adjustment&id=${id}`, { method: 'DELETE' });
                closeModal('adjustment-modal');
                loadRentals();
            }
        }

        document.getElementById('close-rental-form').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('close-rental-id').value;
            const date_end = document.getElementById('close-rental-date').value;

            if (confirm('Вы уверены, что хотите завершить аренду этой датой?')) {
                await fetch(`${API_URL}?action=close_rental`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, date_end })
                });
                closeModal('close-rental-modal');
                loadRentals();
            }
        };

        async function closeRental(id) {
            const r = rentals.find(rent => rent.id == id);
            if (!r) return;

            document.getElementById('close-rental-id').value = id;
            document.getElementById('close-rental-date').value = new Date().toISOString().split('T')[0];
            updateCloseInfo();
            document.getElementById('close-rental-modal').classList.add('active');
        }

        async function deleteRental(id) {
            if (confirm('Вы уверены, что хотите УДАЛИТЬ эту запись?')) {
                await fetch(`${API_URL}?action=delete_rental&id=${id}`, { method: 'DELETE' });
                loadRentals();
            }
        }

        async function toggleDebtor(id) {
            await fetch(`${API_URL}?action=toggle_debtor`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            loadRentals();
        }

        function filterRentals() {
            renderRentals();
        }

        function editRental(id) {
            openRentalModal(id);
        }
    </script>
</body>
</html>