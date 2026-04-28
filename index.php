<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учёт расходов - Оксана Владимировна</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-tab {
            padding: 15px 30px;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .nav-tab:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .nav-tab.active {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Карточки карт */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card-block {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-block:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        }

        .card-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.3em;
            color: #333;
            font-weight: 600;
        }

        .card-actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-delete {
            background: #ffebee;
            color: #c62828;
        }

        .btn-icon:hover {
            transform: scale(1.1);
        }

        .card-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8f9fa;
        }

        .stat-label {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.2em;
            font-weight: bold;
        }

        .stat-in { color: #4caf50; }
        .stat-out { color: #f44336; }
        .stat-balance { color: #2196f3; }

        .transactions-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }

        .transaction-item:hover {
            background: #f5f5f5;
            border-radius: 8px;
        }

        .transaction-info {
            flex: 1;
        }

        .transaction-category {
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        .transaction-date {
            font-size: 0.8em;
            color: #999;
            margin-top: 2px;
        }

        .transaction-amount {
            font-weight: bold;
            font-size: 1.1em;
        }

        .amount-in { color: #4caf50; }
        .amount-out { color: #f44336; }

        .add-btn {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        /* Наличные */
        .cash-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .cash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .cash-balance-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 40px;
            border-radius: 15px;
            text-align: center;
        }

        .cash-balance-label {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .cash-balance-value {
            font-size: 2.5em;
            font-weight: bold;
        }

        .cash-actions {
            display: flex;
            gap: 15px;
        }

        .btn-primary {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .cash-table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #e0e0e0;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-in {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-out {
            background: #ffebee;
            color: #c62828;
        }

        /* Модальное окно */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn-secondary {
            padding: 12px 25px;
            background: #f5f5f5;
            color: #555;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .btn-add-card {
            background: white;
            border: 3px dashed #667eea;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #667eea;
            font-size: 1.1em;
        }

        .btn-add-card:hover {
            background: #f8f9ff;
            transform: translateY(-5px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .summary-value {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }

        .summary-label {
            color: #666;
            font-size: 0.9em;
        }

        .modal-content.modal-lg {
            max-width: 900px;
        }

        .details-table-container {
            margin-top: 20px;
            overflow-x: auto;
        }

        .card-clickable {
            cursor: pointer;
        }

        .card-clickable:hover {
            border: 2px solid #667eea;
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .card-stats {
                grid-template-columns: 1fr;
            }

            .cash-header {
                flex-direction: column;
                text-align: center;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
<base target="_blank">
</head>
<body>
    <div class="container">
        <header>
            <h1>💰 Учёт расходов</h1>
            <p>Оксана Владимировна</p>
        </header>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('cards')">🏺 Антиквариат</button>
            <button class="nav-tab" onclick="showSection('eurolesa-cards')">💳 Евролеса (Карты)</button>
            <button class="nav-tab" onclick="showSection('eurolesa-cash')">💵 Евролеса (Наличка)</button>
            <a href="arenda.php" class="nav-tab" style="text-decoration: none; display: flex; align-items: center;">🏗️ Аренда</a>
            <a href="sales.php" class="nav-tab" style="text-decoration: none; display: flex; align-items: center;">💰 Продажа лесов</a>
        </div>

        <!-- Раздел Антиквариата -->
        <div id="cards-section" class="section active">
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-label">Всего на картах (Антиквариат)</div>
                    <div class="summary-value" id="total-balance" style="color: #2196f3;">0 ₽</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Общий приход</div>
                    <div class="summary-value" id="total-in" style="color: #4caf50;">0 ₽</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Общий расход</div>
                    <div class="summary-value" id="total-out" style="color: #f44336;">0 ₽</div>
                </div>
            </div>

            <div class="cards-grid" id="cards-container">
                <!-- Карточки будут здесь -->
            </div>

            <div class="btn-add-card" onclick="openCardModal('cards')">
                <div style="font-size: 3em; margin-bottom: 10px;">+</div>
                <div>Добавить новую карту (Антиквариат)</div>
            </div>
        </div>

        <!-- Раздел Евролеса (Карты) -->
        <div id="eurolesa-cards-section" class="section">
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-label">Всего на картах (Евролеса)</div>
                    <div class="summary-value" id="total-balance-eurolesa" style="color: #2196f3;">0 ₽</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Общий приход</div>
                    <div class="summary-value" id="total-in-eurolesa" style="color: #4caf50;">0 ₽</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Общий расход</div>
                    <div class="summary-value" id="total-out-eurolesa" style="color: #f44336;">0 ₽</div>
                </div>
            </div>

            <div class="cards-grid" id="eurolesa-cards-container">
                <!-- Карточки будут здесь -->
            </div>

            <div class="btn-add-card" onclick="openCardModal('eurolesa-cards')">
                <div style="font-size: 3em; margin-bottom: 10px;">+</div>
                <div>Добавить новую карту (Евролеса)</div>
            </div>
        </div>

        <!-- Раздел Евролеса (Наличка) -->
        <div id="eurolesa-cash-section" class="section">
            <div class="cash-section">
                <div class="cash-header">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; width: 100%; margin-bottom: 20px;">
                        <div class="cash-balance-box">
                            <div class="cash-balance-label">Евролеса (Наличка)</div>
                            <div class="cash-balance-value" id="cash-balance">0 ₽</div>
                        </div>
                    </div>
                    <div class="cash-actions">
                        <button class="btn-primary" onclick="openCashModal('in')">➕ Приход (Нал)</button>
                        <button class="btn-primary" onclick="openCashModal('out')">➖ Расход (Нал)</button>
                    </div>
                </div>

                <div class="cash-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Тип</th>
                                <th>Сумма</th>
                                <th>Кому/Куда</th>
                                <th>Описание</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="cash-table-body">
                            <!-- Строки будут здесь -->
                        </tbody>
                    </table>
                </div>

                <div id="cash-empty" class="empty-state" style="display: none;">
                    <div class="empty-state-icon">📭</div>
                    <h3>Нет операций</h3>
                    <p>Добавьте первую операцию с наличными</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для карты -->
    <div class="modal" id="card-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="card-modal-title">Добавить карту</h2>
            </div>
            <form id="card-form">
                <input type="hidden" id="card-id">
                <input type="hidden" id="card-section-input" value="cards">
                <div class="form-group">
                    <label>Название карты</label>
                    <input type="text" id="card-name" placeholder="Например: Сбербанк, Тинькофф..." required>
                </div>
                <div class="form-group">
                    <label>Начальный баланс (₽)</label>
                    <input type="number" id="card-balance" placeholder="0" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('card-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для операции по карте -->
    <div class="modal" id="transaction-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Новая операция</h2>
            </div>
            <form id="transaction-form">
                <input type="hidden" id="transaction-card-id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Тип</label>
                        <select id="transaction-type" required>
                            <option value="in">Приход (+)</option>
                            <option value="out">Расход (-)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Сумма (₽)</label>
                        <input type="number" id="transaction-amount" placeholder="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Категория/Назначение</label>
                    <input type="text" id="transaction-category" placeholder="Например: Продукты, Зарплата..." required>
                </div>
                <div class="form-group">
                    <label>Дата</label>
                    <input type="date" id="transaction-date" required>
                </div>
                <div class="form-group">
                    <label>Описание (необязательно)</label>
                    <input type="text" id="transaction-desc" placeholder="Дополнительные детали...">
                </div>
                <div class="form-group" id="is-cash-transfer-group" style="display: none;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" id="transaction-is-cash-transfer" style="width: auto;">
                        <span>Расход из переведенных наличных</span>
                    </label>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('transaction-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для наличных -->
    <div class="modal" id="cash-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="cash-modal-title">Приход наличных</h2>
            </div>
            <form id="cash-form">
                <input type="hidden" id="cash-type">
                <div class="form-row">
                    <div class="form-group">
                        <label>Сумма (₽)</label>
                        <input type="number" id="cash-amount" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label>Дата</label>
                        <input type="date" id="cash-date" required>
                    </div>
                </div>
                <div class="form-group" id="cash-card-group" style="display: none;">
                    <label>Выберите карту для перевода</label>
                    <select id="cash-transfer-card-id">
                        <option value="">Выберите карту...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="cash-recipient-label">От кого / Источник</label>
                    <input type="text" id="cash-recipient" placeholder="Например: Зарплата, Иванов..." required>
                </div>
                <div class="form-group">
                    <label>Описание (необязательно)</label>
                    <input type="text" id="cash-desc" placeholder="Дополнительные детали...">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('cash-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для деталей карты -->
    <div class="modal" id="card-details-modal">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h2 id="card-details-title">Детали карты</h2>
            </div>
            <div class="details-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Категория</th>
                            <th>Тип</th>
                            <th>Сумма</th>
                            <th>Описание</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody id="card-details-table-body">
                        <!-- Строки будут здесь -->
                    </tbody>
                </table>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('card-details-modal')">Закрыть</button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = 'api.php';
        let cards = [];
        let cashTransactions = [];
        let currentSection = 'cards';

        document.addEventListener('DOMContentLoaded', async function() {
            await loadData();
            renderCards();
            renderCash();
            updateSummary();
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transaction-date').value = today;
            document.getElementById('cash-date').value = today;
        });

        async function loadData() {
            try {
                const cardsRes = await fetch(`${API_URL}?action=get_cards`);
                cards = await cardsRes.json();
                const cashRes = await fetch(`${API_URL}?action=get_cash_transactions`);
                cashTransactions = await cashRes.json();
            } catch (err) {
                console.error('Ошибка загрузки:', err);
            }
        }

        function showSection(section) {
            currentSection = section;
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));

            const tabIndex = section === 'cards' ? 0 : (section === 'eurolesa-cards' ? 1 : 2);
            document.getElementById(`${section}-section`).classList.add('active');
            document.querySelectorAll('.nav-tab')[tabIndex].classList.add('active');
            
            renderCards();
            updateSummary();
            updateCashBalance();
        }

        function renderCards() {
            const antiqueContainer = document.getElementById('cards-container');
            const eurolesaContainer = document.getElementById('eurolesa-cards-container');
            antiqueContainer.innerHTML = '';
            eurolesaContainer.innerHTML = '';

            cards.forEach(card => {
                const cardEl = document.createElement('div');
                cardEl.className = 'card-block card-clickable';
                cardEl.onclick = () => openCardDetails(card.id);

                const totalIn = card.transactions.filter(t => t.type === 'in').reduce((sum, t) => sum + t.amount, 0);
                const totalOut = card.transactions.filter(t => t.type === 'out').reduce((sum, t) => sum + t.amount, 0);
                const balance = card.initialBalance + totalIn - totalOut;
                const recentTransactions = card.transactions.slice(-3).reverse();

                cardEl.innerHTML = `
                    <div class="card-header">
                        <div class="card-title">${card.name}</div>
                        <div class="card-actions">
                            <button class="btn-icon btn-edit" onclick="event.stopPropagation(); editCard('${card.id}')">✏️</button>
                            <button class="btn-icon btn-delete" onclick="event.stopPropagation(); deleteCard('${card.id}')">🗑️</button>
                        </div>
                    </div>
                    <div class="card-stats">
                        <div class="stat-box">
                            <div class="stat-label">Приход</div>
                            <div class="stat-value stat-in">+${formatMoney(totalIn)}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Расход</div>
                            <div class="stat-value stat-out">-${formatMoney(totalOut)}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Остаток</div>
                            <div class="stat-value stat-balance">${formatMoney(balance)}</div>
                        </div>
                    </div>
                    <div style="font-size: 0.9em; color: #666; margin-bottom: 10px;">Последние операции:</div>
                    <div class="transactions-list">
                        ${recentTransactions.length > 0 ? recentTransactions.map(t => `
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-category">${t.category}</div>
                                    <div class="transaction-date">${formatDate(t.date)}</div>
                                </div>
                                <div class="transaction-amount ${t.type === 'in' ? 'amount-in' : 'amount-out'}">
                                    ${t.type === 'in' ? '+' : '-'}${formatMoney(t.amount)}
                                </div>
                            </div>
                        `).join('') : '<div style="color: #999; text-align: center; padding: 20px;">Нет операций</div>'}
                    </div>
                    <button class="add-btn" onclick="event.stopPropagation(); openTransactionModal('${card.id}')">➕ Добавить операцию</button>
                `;

                if (card.section === 'eurolesa-cards') {
                    eurolesaContainer.appendChild(cardEl);
                } else {
                    antiqueContainer.appendChild(cardEl);
                }
            });
        }

        function updateSummary() {
            let antique = { balance: 0, in: 0, out: 0 };
            let eurolesa = { balance: 0, in: 0, out: 0 };

            cards.forEach(card => {
                const cardIn = card.transactions.filter(t => t.type === 'in').reduce((sum, t) => sum + t.amount, 0);
                const cardOut = card.transactions.filter(t => t.type === 'out').reduce((sum, t) => sum + t.amount, 0);
                const balance = card.initialBalance + cardIn - cardOut;

                if (card.section === 'eurolesa-cards') {
                    eurolesa.balance += balance; eurolesa.in += cardIn; eurolesa.out += cardOut;
                } else {
                    antique.balance += balance; antique.in += cardIn; antique.out += cardOut;
                }
            });

            document.getElementById('total-balance').textContent = formatMoney(antique.balance);
            document.getElementById('total-in').textContent = '+' + formatMoney(antique.in);
            document.getElementById('total-out').textContent = '-' + formatMoney(antique.out);
            document.getElementById('total-balance-eurolesa').textContent = formatMoney(eurolesa.balance);
            document.getElementById('total-in-eurolesa').textContent = '+' + formatMoney(eurolesa.in);
            document.getElementById('total-out-eurolesa').textContent = '-' + formatMoney(eurolesa.out);
        }

        function renderCash() {
            const tbody = document.getElementById('cash-table-body');
            const emptyState = document.getElementById('cash-empty');
            if (cashTransactions.length === 0) {
                tbody.innerHTML = ''; emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
                tbody.innerHTML = cashTransactions.slice().reverse().map(t => `
                    <tr>
                        <td>${formatDate(t.date)}</td>
                        <td><span class="badge ${t.type === 'in' ? 'badge-in' : 'badge-out'}">${t.type === 'in' ? 'Приход' : 'Расход'}</span></td>
                        <td style="font-weight: bold; color: ${t.type === 'in' ? '#4caf50' : '#f44336'}">${t.type === 'in' ? '+' : '-'}${formatMoney(t.amount)}</td>
                        <td>${t.recipient}</td>
                        <td>${t.description || '-'}</td>
                        <td><button class="btn-icon btn-delete" onclick="deleteCashTransaction('${t.id}')">🗑️</button></td>
                    </tr>
                `).join('');
            }
            updateCashBalance();
        }

        function updateCashBalance() {
            const balance = cashTransactions.reduce((sum, t) => t.type === 'in' ? sum + t.amount : sum - t.amount, 0);
            document.getElementById('cash-balance').textContent = formatMoney(balance);
        }

        function openCardModal(section = 'cards') {
            document.getElementById('card-modal-title').textContent = `Добавить карту (${section === 'cards' ? 'Антиквариат' : 'Евролеса'})`;
            document.getElementById('card-form').reset();
            document.getElementById('card-id').value = '';
            document.getElementById('card-section-input').value = section;
            document.getElementById('card-modal').classList.add('active');
        }

        function editCard(id) {
            const card = cards.find(c => c.id === id);
            if (!card) return;
            document.getElementById('card-modal-title').textContent = `Редактировать карту (${card.section === 'cards' ? 'Антиквариат' : 'Евролеса'})`;
            document.getElementById('card-id').value = card.id;
            document.getElementById('card-name').value = card.name;
            document.getElementById('card-balance').value = card.initialBalance;
            document.getElementById('card-section-input').value = card.section;
            document.getElementById('card-modal').classList.add('active');
        }

        function openTransactionModal(cardId) {
            const card = cards.find(c => c.id === cardId);
            document.getElementById('transaction-card-id').value = cardId;
            document.getElementById('transaction-form').reset();
            document.getElementById('transaction-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('transaction-category').value = card && card.section === 'eurolesa-cards' ? 'Доход Евролеса' : 'Продажа антиквариата';
            document.getElementById('transaction-modal').classList.add('active');
        }

        function openCashModal(type) {
            document.getElementById('cash-type').value = type;
            document.getElementById('cash-modal-title').textContent = type === 'in' ? 'Приход (Евролеса)' : 'Расход (Евролеса)';
            document.getElementById('cash-form').reset();
            document.getElementById('cash-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('cash-modal').classList.add('active');
        }

        function openCardDetails(cardId) {
            const card = cards.find(c => c.id === cardId);
            if (!card) return;
            document.getElementById('card-details-title').textContent = `История: ${card.name}`;
            const tbody = document.getElementById('card-details-table-body');
            tbody.innerHTML = card.transactions.length === 0 ? '<tr><td colspan="6" style="text-align:center;padding:20px">Нет операций</td></tr>' :
                card.transactions.slice().reverse().map(t => `
                    <tr>
                        <td>${formatDate(t.date)}</td>
                        <td>${t.category}</td>
                        <td><span class="badge ${t.type === 'in' ? 'badge-in' : 'badge-out'}">${t.type === 'in' ? 'Приход' : 'Расход'}</span></td>
                        <td style="font-weight: bold; color: ${t.type === 'in' ? '#4caf50' : '#f44336'}">${t.type === 'in' ? '+' : '-'}${formatMoney(t.amount)}</td>
                        <td>${t.description || '-'}</td>
                        <td><button class="btn-icon btn-delete" onclick="deleteCardTransaction('${t.id}', '${card.id}')">🗑️</button></td>
                    </tr>
                `).join('');
            document.getElementById('card-details-modal').classList.add('active');
        }

        async function deleteCard(id) {
            if (confirm('Удалить карту?')) {
                await fetch(`${API_URL}?action=delete_card&id=${id}`, { method: 'DELETE' });
                await loadData(); renderCards(); updateSummary();
            }
        }

        async function deleteCardTransaction(id, cardId) {
            if (confirm('Удалить операцию?')) {
                await fetch(`${API_URL}?action=delete_card_transaction&id=${id}`, { method: 'DELETE' });
                await loadData(); openCardDetails(cardId); renderCards(); updateSummary();
            }
        }

        async function deleteCashTransaction(id) {
            if (confirm('Удалить операцию?')) {
                await fetch(`${API_URL}?action=delete_cash_transaction&id=${id}`, { method: 'DELETE' });
                await loadData(); renderCash();
            }
        }

        document.getElementById('card-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: document.getElementById('card-id').value || Date.now().toString(),
                name: document.getElementById('card-name').value,
                initialBalance: parseFloat(document.getElementById('card-balance').value),
                section: document.getElementById('card-section-input').value
            };
            await fetch(`${API_URL}?action=save_card`, { method: 'POST', body: JSON.stringify(data) });
            await loadData(); renderCards(); updateSummary(); closeModal('card-modal');
        });

        document.getElementById('transaction-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: Date.now().toString(),
                cardId: document.getElementById('transaction-card-id').value,
                type: document.getElementById('transaction-type').value,
                amount: parseFloat(document.getElementById('transaction-amount').value),
                category: document.getElementById('transaction-category').value,
                date: document.getElementById('transaction-date').value,
                description: document.getElementById('transaction-desc').value
            };
            await fetch(`${API_URL}?action=add_card_transaction`, { method: 'POST', body: JSON.stringify(data) });
            await loadData(); renderCards(); updateSummary(); closeModal('transaction-modal');
        });

        document.getElementById('cash-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: Date.now().toString(),
                type: document.getElementById('cash-type').value,
                amount: parseFloat(document.getElementById('cash-amount').value),
                recipient: document.getElementById('cash-recipient').value,
                date: document.getElementById('cash-date').value,
                description: document.getElementById('cash-desc').value
            };
            await fetch(`${API_URL}?action=add_cash_transaction`, { method: 'POST', body: JSON.stringify(data) });
            await loadData(); renderCash(); closeModal('cash-modal');
        });

        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        function formatMoney(n) { return (n || 0).toLocaleString('ru-RU') + ' ₽'; }
        function formatDate(s) { return new Date(s).toLocaleDateString('ru-RU'); }
        window.onclick = e => { if (e.target.classList.contains('modal')) e.target.classList.remove('active'); }
    </script>
</body>
</html>