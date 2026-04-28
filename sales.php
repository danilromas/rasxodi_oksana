<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Продажа оборудования - Оксана Владимировна</title>
    <style>
        /* Общие стили */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        header { text-align: center; margin-bottom: 30px; color: white; }
        header h1 { font-size: 2.5em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }

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

        .btn-primary {
            padding: 10px 20px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4); }
        .btn-secondary { background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; }

        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(5px);
        }
        .modal.active { display: flex; }
        .modal-content {
            background: white; padding: 30px; border-radius: 20px; width: 95%; max-width: 600px;
            max-height: 90vh; overflow-y: auto;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .back-link { position: absolute; top: 20px; left: 20px; color: white; text-decoration: none; font-size: 1.1em; }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Главная</a>
    <div class="container">
        <header>
            <h1>💰 Продажа оборудования</h1>
            <p style="color: white; opacity: 0.8;">Учёт проданных строительных лесов и комплектующих</p>
        </header>

        <div class="card-block">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Список продаж</h2>
                <button class="btn-primary" onclick="openSaleModal()">➕ Новая продажа</button>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="sales-table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Оборудование</th>
                            <th>Сумма</th>
                            <th>Оплачено</th>
                            <th>Остаток</th>
                            <th>Телефон</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody id="sales-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Модальное окно продажи -->
    <div class="modal" id="sale-modal">
        <div class="modal-content">
            <h2 id="sale-modal-title" style="margin-bottom: 20px;">Новая продажа</h2>
            <form id="sale-form">
                <input type="hidden" id="sale-id">
                <div class="form-group">
                    <label>Клиент</label>
                    <input type="text" id="sale-client" required>
                </div>
                <div class="form-group">
                    <label>Что продано (список через запятую)</label>
                    <textarea id="sale-items" rows="3" required placeholder="Например: 10 рам, 20 диагоналей..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Общая сумма (₽)</label>
                        <input type="number" id="sale-total" value="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Оплачено (₽)</label>
                        <input type="number" id="sale-paid" value="0" step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Дата продажи</label>
                        <input type="date" id="sale-date" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="text" id="sale-phone">
                    </div>
                </div>
                <div class="form-group">
                    <label>Комментарий</label>
                    <textarea id="sale-comment" rows="2"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeModal('sale-modal')">Отмена</button>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = 'api_sales.php';
        let sales = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadSales();
        });

        async function loadSales() {
            try {
                const res = await fetch(`${API_URL}?action=get_sales`);
                sales = await res.json();
                renderSales();
            } catch (err) { console.error(err); }
        }

        function renderSales() {
            const tbody = document.getElementById('sales-body');
            tbody.innerHTML = sales.map(s => {
                const remainder = s.total_amount - s.paid_amount;
                const rowClass = remainder > 0 ? 'debtor' : '';
                
                return `
                    <tr class="${rowClass}">
                        <td>${formatDate(s.date_sale)}</td>
                        <td>${s.client_name}</td>
                        <td style="max-width: 300px; white-space: pre-wrap;">${s.items}</td>
                        <td>${formatMoney(s.total_amount)}</td>
                        <td>${formatMoney(s.paid_amount)}</td>
                        <td style="font-weight:bold">${formatMoney(remainder)}</td>
                        <td>${s.phone || ''}</td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <button class="btn-secondary" onclick="editSale(${s.id})">✏️</button>
                                <button class="btn-danger" onclick="deleteSale(${s.id})">🗑️</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(amount);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('ru-RU');
        }

        function openSaleModal(id = null) {
            const modal = document.getElementById('sale-modal');
            const form = document.getElementById('sale-form');
            form.reset();
            document.getElementById('sale-id').value = '';
            document.getElementById('sale-modal-title').textContent = 'Новая продажа';
            document.getElementById('sale-date').value = new Date().toISOString().split('T')[0];
            
            if (id) {
                const s = sales.find(sale => sale.id == id);
                if (s) {
                    document.getElementById('sale-id').value = s.id;
                    document.getElementById('sale-client').value = s.client_name;
                    document.getElementById('sale-items').value = s.items;
                    document.getElementById('sale-total').value = s.total_amount;
                    document.getElementById('sale-paid').value = s.paid_amount;
                    document.getElementById('sale-date').value = s.date_sale;
                    document.getElementById('sale-phone').value = s.phone;
                    document.getElementById('sale-comment').value = s.comment;
                    document.getElementById('sale-modal-title').textContent = 'Редактировать продажу';
                }
            }
            modal.classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.getElementById('sale-form').onsubmit = async (e) => {
            e.preventDefault();
            const data = {
                id: document.getElementById('sale-id').value,
                client_name: document.getElementById('sale-client').value,
                items: document.getElementById('sale-items').value,
                total_amount: document.getElementById('sale-total').value,
                paid_amount: document.getElementById('sale-paid').value,
                date_sale: document.getElementById('sale-date').value,
                phone: document.getElementById('sale-phone').value,
                comment: document.getElementById('sale-comment').value
            };

            await fetch(`${API_URL}?action=save_sale`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            closeModal('sale-modal');
            loadSales();
        };

        async function deleteSale(id) {
            if (confirm('Вы уверены, что хотите удалить эту запись?')) {
                await fetch(`${API_URL}?action=delete_sale&id=${id}`, { method: 'DELETE' });
                loadSales();
            }
        }

        function editSale(id) {
            openSaleModal(id);
        }
    </script>
</body>
</html>