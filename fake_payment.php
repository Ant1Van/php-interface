<?php
$order_id = intval($_GET['order_id'] ?? 0);
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата заказа</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background: #19191b; color: #fff; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .pay-box { background: #232323; width: 420px; max-width: 97vw; border-radius: 18px; padding: 2.3rem 2.1rem 2.1rem 2.1rem; box-shadow: 0 6px 36px #000c; }
        .pay-title { font-size: 2rem; margin-bottom: 1.3rem; text-align:center;}
        .pay-desc { color: #bfbfbf; margin-bottom: 2rem; font-size: 1.08rem; text-align: center;}
        .form-group { margin-bottom: 1.1rem; }
        .form-label { display:block; color: #bbb; margin-bottom: 0.4rem; }
        .pay-input {
            width: 100%; padding: 0.85rem 1rem; border: 1.5px solid #282828; border-radius: 7px;
            background: #222124; color: #fff; font-size: 1.03rem; transition: border .17s;
        }
        .pay-input:focus { border-color: #8b0000; }
        .pay-btn {
            width: 100%; background: #8b0000; color: #fff; padding: 1.03rem 0; font-size: 1.14rem;
            border: none; border-radius: 8px; cursor: pointer; margin-top: .8rem; font-weight: 600; letter-spacing: 1px;
            transition: background .16s, transform .14s;
        }
        .pay-btn:hover { background: #b80000; transform: translateY(-2px) scale(1.01);}
        .pay-loader { display: none; margin: 1.5rem 0; text-align: center;}
        .pay-progress-bg { width: 100%; height: 16px; background: #292929; border-radius: 8px; overflow: hidden; }
        .pay-progress-bar { height: 100%; background: linear-gradient(90deg,#8b0000,#e24d4d); width:0; transition: width .15s;}
        .pay-success { display:none; margin-top: 2rem; color:#30e398; font-size:1.25rem; text-align: center; }
    </style>
</head>
<body>
<div class="pay-box">
    <div class="pay-title">Оплата заказа</div>
    <div class="pay-desc">
        Введите данные карты для оплаты.<br>
        <b>Сумма к оплате:</b>
        <span style="color:#e05b5b; font-size:1.23rem;">
            <?= $amount > 0 ? number_format($amount, 0, '', ' ') . ' ₽' : 'Не указано' ?>
        </span>
    </div>
    <form id="pay-form" autocomplete="off">
        <div class="form-group">
            <label class="form-label">Номер карты</label>
            <input type="text" class="pay-input" maxlength="19" placeholder="0000 0000 0000 0000" required pattern="\d{4} \d{4} \d{4} \d{4}">
        </div>
        <div class="form-group" style="display:flex; gap: 1rem;">
            <div style="flex:2">
                <label class="form-label">Срок действия</label>
                <input type="text" class="pay-input" maxlength="5" placeholder="12/28" required pattern="\d{2}/\d{2}">
            </div>
            <div style="flex:1">
                <label class="form-label">CVV</label>
                <input type="password" class="pay-input" maxlength="3" placeholder="123" required pattern="\d{3}">
            </div>
        </div>
        <button class="pay-btn" type="submit">Оплатить</button>
        <div class="pay-loader" id="loader">
            <div class="pay-progress-bg"><div class="pay-progress-bar" id="progress"></div></div>
            <div style="margin-top:8px; font-size:.98rem;">Платёж обрабатывается...</div>
        </div>
        <div class="pay-success" id="successMsg">
            <span>Платёж успешно проведён!</span>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическая маска номера карты
    const cardInput = document.querySelector('.pay-input[placeholder="0000 0000 0000 0000"]');
    cardInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,16);
        v = v.replace(/(.{4})/g, '$1 ').trim();
        this.value = v;
    });
    // Маска для даты
    const dateInput = document.querySelector('.pay-input[placeholder="12/28"]');
    dateInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,4);
        if(v.length > 2) v = v.slice(0,2)+'/'+v.slice(2);
        this.value = v;
    });

    // Обработка оплаты
    document.getElementById('pay-form').onsubmit = function(e){
        e.preventDefault();
        // Блокируем
        this.querySelectorAll('input,button').forEach(el => el.disabled=true);
        document.getElementById('loader').style.display = 'block';
        let bar = document.getElementById('progress');
        let step = 0, interval = setInterval(()=>{
            step += Math.floor(Math.random()*9)+4; // чуть рандома
            bar.style.width = Math.min(step,100)+'%';
            if(step >= 100){
                clearInterval(interval);
                setTimeout(()=>{
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('successMsg').style.display = 'block';
                    setTimeout(()=>{
                        // Перевод на успешную оплату — обязательно order_id!
                        window.location.href = "checkout_success.php?paid=1";
                    }, 1200);
                }, 700);
            }
        }, 100);
    };
});
</script>
</body>
</html>
