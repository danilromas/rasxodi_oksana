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
                            <option value="lestnicy">Лестницы</option>
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
                            <option value="lestnicy">Лестницы</option>
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

        function renderRentals() {
            const tbody = document.getElementById('rentals-body');
            const calcDate = new Date(document.getElementById('calc-date').value);
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
                ramnye: 0, vyshka: 0, lestnicy: 0,
                daily: { ramnye: 0, vyshka: 0, lestnicy: 0, total: 0 }, 
                m2: 0 
            };

            tbody.innerHTML = filtered.map(r => {
                const startDate = new Date(r.date_start);
                const diffTime = Math.max(0, calcDate - startDate);
                const daysUsed = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const sumRent = daysUsed * r.daily_rate;
                const remainder = parseFloat(r.deposit) + parseFloat(r.paid_rent) - sumRent;
                
                // Расчет оставшихся дней аренды на основе ОПЛАЧЕННОЙ суммы (paid_rent)
                const totalPaidDays = r.daily_rate > 0 ? Math.floor(parseFloat(r.paid_rent) / parseFloat(r.daily_rate)) : 0;
                const daysRemaining = totalPaidDays - daysUsed;
                
                let rowClass = '';
                let statusBadge = '';
                
                if (daysRemaining <= 0) {
                    rowClass = 'debtor';
                    statusBadge = '<span class="badge-warning">Срок истек</span>';
                } else if (daysRemaining <= 3) {
                    rowClass = 'warning';
                    statusBadge = `<span class="badge-warning">Осталось: ${daysRemaining} дн.</span>`;
                }

                totals[r.type] += remainder;
                totals.daily[r.type] += parseFloat(r.daily_rate);
                totals.daily.total += parseFloat(r.daily_rate);
                totals.m2 += parseFloat(r.square_meters || 0);

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
                        <td>${daysUsed}</td>
                        <td>${formatMoney(r.daily_rate)}</td>
                        <td>${formatMoney(sumRent)}</td>
                        <td>${formatMoney(r.deposit)}</td>
                        <td>${formatMoney(r.paid_rent)}</td>
                        <td style="font-weight:bold">${formatMoney(remainder)}</td>
                        <td>${r.square_meters || 0}</td>
                        <td>${r.phone || ''}</td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <button class="btn-primary" onclick="toggleDebtor(${r.id})" title="${r.is_debtor ? 'Убрать из должников' : 'В должники'}">
                                    ${r.is_debtor ? '👤✅' : '👤❌'}
                                </button>
                                <button class="btn-success" onclick="closeRental(${r.id})" title="Закрыть">✅</button>
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
                        Рамные: ${formatMoney(totals.ramnye)} | Вышки: ${formatMoney(totals.vyshka)} | 
                        Лестницы: ${formatMoney(totals.lestnicy)}
                    </td>
                    <td colspan="4">Общее к возврату: ${formatMoney(totals.ramnye + totals.vyshka + totals.lestnicy)}</td>
                </tr>
                <tr class="summary-row">
                    <td colspan="5">Аренда в сутки:</td>
                    <td colspan="4">
                        Рамные: ${formatMoney(totals.daily.ramnye)} | Вышки: ${formatMoney(totals.daily.vyshka)} | 
                        Лестницы: ${formatMoney(totals.daily.lestnicy)}
                    </td>
                    <td colspan="4">Общая аренда в сутки: ${formatMoney(totals.daily.total)} | Итого м²: ${totals.m2.toFixed(2)}</td>
                </tr>
            `;
        }

        function renderDebtors() {
            const tbody = document.getElementById('debtors-body');
            const calcDate = new Date(document.getElementById('calc-date').value);
            
            // Фильтруем ТОЛЬКО тех, кто отмечен как должник вручную
            const debtors = rentals.filter(r => r.status === 'active' && r.is_debtor);

            tbody.innerHTML = debtors.map(r => {
                const startDate = new Date(r.date_start);
                const diffTime = Math.max(0, calcDate - startDate);
                const days = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const sumRent = days * r.daily_rate;
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
                        <td>${days}</td>
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
                const startDate = new Date(r.date_start);
                const endDate = new Date(r.date_end);
                const diffTime = Math.max(0, endDate - startDate);
                const daysUsed = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const sumRent = daysUsed * r.daily_rate;
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
            const periodEnd = new Date(endStr);
            periodEnd.setHours(23, 59, 59, 999);

            const totalProfit = rentals.reduce((sum, r) => {
                const rentalStart = new Date(r.date_start);
                // Для активных аренд считаем до сегодня, для закрытых - до date_end
                const rentalEnd = r.status === 'closed' && r.date_end ? new Date(r.date_end) : new Date();
                
                const daysInPeriod = getDaysInPeriod(rentalStart, rentalEnd, periodStart, periodEnd);
                const earnedInPeriod = daysInPeriod * parseFloat(r.daily_rate || 0);
                
                return sum + earnedInPeriod;
            }, 0);

            document.getElementById('total-profit').textContent = formatMoney(totalProfit);
            renderCharts();
        }

        function getDaysInPeriod(rentalStart, rentalEnd, periodStart, periodEnd) {
            const start = new Date(Math.max(rentalStart, periodStart));
            const end = new Date(Math.min(rentalEnd, periodEnd));
            
            if (start > end) return 0;
            
            // Сбрасываем время для точного расчета дней
            start.setHours(0, 0, 0, 0);
            end.setHours(0, 0, 0, 0);
            
            const diffTime = Math.max(0, end - start);
            return Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
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
                const monthEnd = new Date(d.getFullYear(), d.getMonth() + 1, 0, 23, 59, 59, 999);
                
                let monthProfit = 0;
                rentals.forEach(r => {
                    const rentalStart = new Date(r.date_start);
                    const rentalEnd = r.status === 'closed' && r.date_end ? new Date(r.date_end) : new Date();
                    
                    const daysInMonth = getDaysInPeriod(rentalStart, rentalEnd, monthStart, monthEnd);
                    monthProfit += daysInMonth * parseFloat(r.daily_rate || 0);
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
            const typeCounts = { ramnye: 0, vyshka: 0, lestnicy: 0 };
            rentals.forEach(r => {
                if (typeCounts[r.type] !== undefined) typeCounts[r.type]++;
            });

            if (typesChart) typesChart.destroy();
            const ctx2 = document.getElementById('typesChart').getContext('2d');
            typesChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Рамные', 'Вышка', 'Лестницы'],
                    datasets: [{
                        data: [typeCounts.ramnye, typeCounts.vyshka, typeCounts.lestnicy],
                        backgroundColor: [
                            'rgba(30, 60, 114, 0.8)',
                            'rgba(42, 82, 152, 0.8)',
                            'rgba(108, 117, 125, 0.8)'
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
                vyshka: 'Вышка-тура',
                lestnicy: 'Лестницы'
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
            const types = { ramnye: 'Рамные', vyshka: 'Вышка', lestnicy: 'Лестницы' };
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

        async function closeRental(id) {
            const r = rentals.find(rent => rent.id == id);
            if (!r) return;

            const calcDate = new Date();
            const startDate = new Date(r.date_start);
            const diffTime = Math.max(0, calcDate - startDate);
            const daysUsed = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const sumRentUsed = daysUsed * r.daily_rate;
            
            // Расчет возврата: Залог + (Оплачено - Использовано)
            const unusedRent = Math.max(0, parseFloat(r.paid_rent) - sumRentUsed);
            const refundTotal = parseFloat(r.deposit) + unusedRent;

            const message = `Закрытие аренды для ${r.client_name}:\n\n` +
                            `Использовано дней: ${daysUsed}\n` +
                            `Стоимость аренды: ${formatMoney(sumRentUsed)}\n` +
                            `Остаток от оплаты: ${formatMoney(unusedRent)}\n` +
                            `Залог к возврату: ${formatMoney(r.deposit)}\n\n` +
                            `ИТОГО К ВОЗВРАТУ КЛИЕНТУ: ${formatMoney(refundTotal)}\n\n` +
                            `Вы уверены, что хотите закрыть эту аренду?`;

            if (confirm(message)) {
                await fetch(`${API_URL}?action=close_rental`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                loadRentals();
            }
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