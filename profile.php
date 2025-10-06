<?php
session_start();
require_once 'db_connect.php';
include 'header.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Получаем данные пользователя из БД
$stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<style>
body {
    height: 100vh;
    overflow: hidden;
}
.profile-container {
    max-width: 800px;
    margin: 120px auto 40px;
    padding: 2rem;
    background: #121212;
    border: 1px solid #333;
    border-radius: 15px;
    color: #e0e0e0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}
.profile-title {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.profile-info {
    display: grid;
    gap: 1.5rem;
}
.info-group {
    display: grid;
    gap: 0.5rem;
}
.info-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.info-value {
    color: #fff;
    font-size: 1.1rem;
    padding: 0.5rem;
    background: #1a1a1a;
    border-radius: 8px;
    border: 1px solid #333;
}
.profile-edit-btn {
    margin-top: 2rem;
    padding: 1rem 2rem;
    background: #8b0000;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.profile-edit-btn:hover {
    background: #6b0000;
    transform: translateY(-2px);
}
#editModal {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(10,10,20,0.7);
    justify-content: center;
    align-items: center;
    animation: modalFadeIn .22s;
}
@keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
#editProfileForm {
    background: #181818;
    border-radius: 16px;
    box-shadow: 0 10px 48px #000c;
    width: 98vw; max-width: 410px;
    padding: 2.3rem 2.2rem 1.5rem 2.2rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    position: relative;
    animation: modalPop .23s;
}
@keyframes modalPop { from { transform: scale(.92); opacity:.7;} to { transform:none; opacity:1;} }
#editProfileForm h2 {
    color: #fff;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 1.3rem 0;
    letter-spacing: 1px;
    text-align: left;
}
.edit-modal-close {
    position: absolute;
    top: 18px; right: 18px;
    font-size: 1.6rem;
    color: #bdbdbd;
    background: none;
    border: none;
    cursor: pointer;
    transition: color .2s;
    z-index: 2;
}
.edit-modal-close:hover { color: #ff4444; }
.edit-group {
    display: flex;
    flex-direction: column;
    position: relative;
    margin-bottom: 2px;
}
.edit-label {
    position: absolute;
    top: 13px; left: 14px;
    font-size: 1rem;
    color: #888;
    pointer-events: none;
    transition: 0.18s;
    background: transparent;
}
.modal-input {
    background: #222;
    border: 1.5px solid #383838;
    color: #fff;
    border-radius: 8px;
    padding: 16px 12px 16px 14px;
    font-size: 1.07rem;
    outline: none;
    transition: border .18s;
}
.modal-input:focus { border-color: #8b0000; }
.modal-input:not(:placeholder-shown) + .edit-label,
.modal-input:focus + .edit-label {
    top: -11px;
    left: 10px;
    background: #181818;
    padding: 0 6px;
    font-size: .92em;
    color: #e05b5b;
}
.edit-modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: .6rem;
}
.edit-modal-actions button,
.edit-profile-btn, .edit-cancel-btn {
    padding: 1rem 0 !important;
    font-size: 1.09rem !important;
    border-radius: 8px;
    height: auto;
    min-height: 0;
    box-sizing: border-box;
    font-family: inherit;
    width: 100%;
    flex: 1 1 0;
}
.edit-profile-btn:hover { background: #700000; transform: translateY(-2px); }
.edit-profile-btn {
    background: #8b0000;
    color: #fff;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: background 0.18s, transform .17s;
}
.profile-edit-btn {
    display: inline-block;
    margin-top: 2rem;
    padding: 1rem 2.3rem;
    background: #8b0000;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 1.08rem;
    cursor: pointer;
    transition: background 0.18s, box-shadow .18s, transform .15s;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    box-shadow: 0 2px 14px #120a0a40;
    min-width: 210px;
}
.profile-edit-btn:hover {
    background: #a80000;
    box-shadow: 0 6px 28px #230d0d45;
    transform: translateY(-2px) scale(1.03);
}
.edit-cancel-btn {
    background: #444;
    color: #fff;
    border: none;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: background 0.18s, transform .17s;
}
.edit-cancel-btn:hover { background: #222; transform: translateY(-2px); }
.error-edit-message {
    color: #ff6464;
    text-align: center;
    font-size: .98rem;
    min-height: 22px;
    margin: 3px 0 0 0;
    padding: 0;
}
@media (max-width: 600px) {
    #editProfileForm { padding: 1.4rem 0.5rem 1.1rem 0.5rem; }
}

/* --- Табуляция --- */
.profile-tabs {
  display: flex;
  gap: 1.3rem;
  margin-bottom: 2.2rem;
}
.tab-btn {
  padding: 0.7rem 2.1rem;
  background: #222;
  color: #fff;
  border: none;
  border-radius: 7px 7px 0 0;
  font-size: 1rem;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.15s;
}
.tab-btn.active {
  background: #8b0000;
  color: #fff;
}
.orders-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1.7rem;
  background: #1a1a1a;
  color: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
}
.orders-table th, .orders-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid #333;
  text-align: left;
}
.orders-table th {
  background: #181818;
  font-weight: bold;
  color: #fff;
}
.orders-table tr:last-child td {
  border-bottom: none;
}
</style>

<div class="profile-container">
    <div class="profile-tabs">
        <button class="tab-btn active" data-tab="profile-info">Профиль</button>
        <button class="tab-btn" data-tab="orders-active">Текущие заказы</button>
        <button class="tab-btn" data-tab="orders-history">История заказов</button>
    </div>

    <!-- Профиль -->
    <div class="tab-content" id="profile-info">
        <h1 class="profile-title">Ваш профиль</h1>
        <div class="profile-info">
            <div class="info-group">
                <div class="info-label">ФИО:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Телефон:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['phone']); ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Адрес:</div>
                <div class="info-value"><?php echo $user['address'] ? htmlspecialchars($user['address']) : 'Не указан'; ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Город:</div>
                <div class="info-value"><?php echo $user['city'] ? htmlspecialchars($user['city']) : 'Не указан'; ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Почтовый индекс:</div>
                <div class="info-value"><?php echo $user['postal_code'] ? htmlspecialchars($user['postal_code']) : 'Не указан'; ?></div>
            </div>
        </div>
        <button class="profile-edit-btn">Редактировать профиль</button>
    </div>

    <!-- Активные заказы -->
    <div class="tab-content" id="orders-active" style="display:none">
        <h2 class="profile-title" style="font-size:1.7rem;margin-bottom:1.1rem;">Текущие заказы</h2>
        <?php
        $res = mysqli_query($conn, "
            SELECT o.*, dt.name AS delivery_name
            FROM orders o
            LEFT JOIN delivery_types dt ON o.delivery_method = dt.id
            WHERE o.customer_id = $user_id AND o.status IN ('created','paid')
            ORDER BY o.order_date DESC
        ");
        if (mysqli_num_rows($res) === 0) {
            echo '<p>У вас нет активных заказов.</p>';
        } else {
            echo '<table class="orders-table">';
            echo '<tr><th>ID</th><th>Дата</th><th>Сумма</th><th>Статус</th><th>Доставка</th></tr>';
            while($row = mysqli_fetch_assoc($res)) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['order_date'].'</td>';
                echo '<td>'.number_format($row['total_amount'],0,'',' ').' ₽</td>';
                echo '<td>'.($row['status']=='created'?'Создан':'Оплачен').'</td>';
                echo '<td>'.htmlspecialchars($row['delivery_name']).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </div>

    <!-- История заказов -->
    <div class="tab-content" id="orders-history" style="display:none">
        <h2 class="profile-title" style="font-size:1.7rem;margin-bottom:1.1rem;">История заказов</h2>
        <?php
        $res = mysqli_query($conn, "
            SELECT o.*, dt.name AS delivery_name
            FROM orders o
            LEFT JOIN delivery_types dt ON o.delivery_method = dt.id
            WHERE o.customer_id = $user_id AND o.status IN ('shipped','cancelled')
            ORDER BY o.order_date DESC
        ");
        if (mysqli_num_rows($res) === 0) {
            echo '<p>История заказов пуста.</p>';
        } else {
            echo '<table class="orders-table">';
            echo '<tr><th>ID</th><th>Дата</th><th>Сумма</th><th>Статус</th><th>Доставка</th></tr>';
            while($row = mysqli_fetch_assoc($res)) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['order_date'].'</td>';
                echo '<td>'.number_format($row['total_amount'],0,'',' ').' ₽</td>';
                echo '<td>'.($row['status']=='shipped'?'Отправлен':'Отменён').'</td>';
                echo '<td>'.htmlspecialchars($row['delivery_name']).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </div>
</div>

<!-- Модальное окно для редактирования -->
<div id="editModal">
  <form id="editProfileForm" autocomplete="off">
    <button type="button" class="edit-modal-close" title="Закрыть">&times;</button>
    <h2>Редактировать профиль</h2>
    <div class="edit-group">
      <input type="text" name="name" class="modal-input" required placeholder=" " />
      <label class="edit-label">ФИО</label>
    </div>
    <div class="edit-group">
      <input type="email" name="email" class="modal-input" required placeholder=" " />
      <label class="edit-label">Email</label>
    </div>
    <div class="edit-group">
      <input type="tel" name="phone" class="modal-input" required pattern="[0-9]{11}" placeholder=" " />
      <label class="edit-label">Телефон</label>
    </div>
    <div class="edit-group">
      <input type="text" name="address" class="modal-input" placeholder=" " />
      <label class="edit-label">Адрес</label>
    </div>
    <div class="edit-group">
        <input type="text" name="city" class="modal-input" placeholder=" " />
        <label class="edit-label">Город</label>
    </div>
    <div class="edit-group">
        <input type="text" name="postal_code" class="modal-input" placeholder=" " />
        <label class="edit-label">Почтовый индекс</label>
    </div>
    <div class="error-edit-message"></div>
    <div class="edit-modal-actions">
        <button type="submit" class="edit-profile-btn">Сохранить</button>
        <button type="button" class="edit-cancel-btn">Отмена</button>
    </div>
  </form>
</div>

<script>
// Вкладки
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.onclick = function() {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).style.display = '';
  };
});

// Открытие модального окна с автозаполнением
document.querySelector('.profile-edit-btn').onclick = function() {
    const modal = document.getElementById('editModal');
    const values = document.querySelectorAll('.info-value');
    modal.querySelector('input[name="name"]').value = values[0].textContent.trim();
    modal.querySelector('input[name="email"]').value = values[1].textContent.trim();
    modal.querySelector('input[name="phone"]').value = values[2].textContent.trim();
    modal.querySelector('input[name="address"]').value = values[3].textContent.trim() === 'Не указан' ? '' : values[3].textContent.trim();
    modal.querySelector('input[name="city"]').value = values[4].textContent.trim() === 'Не указан' ? '' : values[4].textContent.trim();
    modal.querySelector('input[name="postal_code"]').value = values[5].textContent.trim() === 'Не указан' ? '' : values[5].textContent.trim();
    modal.style.display = 'flex';
    setTimeout(() => {
      document.querySelectorAll('.modal-input').forEach(inp => inp.dispatchEvent(new Event('input')));
    }, 100);
};
// Кнопка закрытия и "Отмена"
document.querySelector('.edit-modal-close').onclick =
document.querySelector('.edit-cancel-btn').onclick = function() {
    document.getElementById('editModal').style.display = 'none';
};
// Плавающие лейблы
document.querySelectorAll('.modal-input').forEach(inp => {
    inp.addEventListener('input', function() {
        if (this.value.trim() !== "") {
            this.classList.add('not-empty');
        } else {
            this.classList.remove('not-empty');
        }
    });
});
// Отправка формы
document.getElementById('editProfileForm').onsubmit = async function(e) {
    e.preventDefault();
    const form = e.target;
    const errorMsg = form.querySelector('.error-edit-message');
    errorMsg.textContent = '';
    const formData = {
        action: 'edit_profile',
        name: form.name.value.trim(),
        email: form.email.value.trim(),
        phone: form.phone.value.trim(),
        address: form.address.value.trim(),
        city: form.city.value.trim(),
        postal_code: form.postal_code.value.trim(),
    };
    if (!formData.name || !formData.email || !formData.phone.match(/^[0-9]{11}$/)) {
        errorMsg.textContent = 'Проверьте правильность заполнения полей!';
        return;
    }
    try {
        const res = await fetch('auth_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const data = await res.json();
        if (data.success) {
            // Обновить данные на странице
            const vals = document.querySelectorAll('.info-value');
            vals[0].textContent = formData.name;
            vals[1].textContent = formData.email;
            vals[2].textContent = formData.phone;
            vals[3].textContent = formData.address || 'Не указан';
            vals[4].textContent = formData.city || 'Не указан';
            vals[5].textContent = formData.postal_code || 'Не указан';
            document.getElementById('editModal').style.display = 'none';
        } else {
            errorMsg.textContent = data.message || 'Ошибка при сохранении!';
        }
    } catch (err) {
        errorMsg.textContent = 'Ошибка сервера!';
    }
};
</script>

<?php include 'footer.php'; ?>
