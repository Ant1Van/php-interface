<?php
session_start();
include 'header.php';
?>

<style>
body {
    height: 100vh;
    overflow: hidden;
}

.auth-container {
    max-width: 400px;
    margin: 120px auto 40px;
    padding: 2rem;
    background: #121212;
    border: 1px solid #333;
    border-radius: 15px;
    color: #e0e0e0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.5s ease;
    position: relative;
    overflow: visible;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-title {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.form-group {
    margin-bottom: 2rem;
    position: relative;
}

.form-control {
    width: 100%;
    padding: 1rem;
    background: #1a1a1a;
    border: 1px solid #333;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
    height: 48px;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #8b0000;
    box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.2);
    outline: none;
}

.form-label {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    transition: all 0.3s ease;
    pointer-events: none;
    background: transparent;
    padding: 0 0.5rem;
    z-index: 1;
}

.form-control:focus + .form-label,
.form-control:not(:placeholder-shown) + .form-label {
    transform: translateY(-1.5rem) scale(0.8);
    color: #8b0000;
    background: #121212;
    padding: 0 0.5rem;
    z-index: 2;
}

.remember-me {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
    margin-right: 1rem;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #1a1a1a;
    border: 1px solid #333;
    transition: .4s;
    border-radius: 30px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 3px;
    background-color: #666;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #8b0000;
    border-color: #8b0000;
}

input:checked + .slider:before {
    transform: translateX(30px);
    background-color: #fff;
}

.auth-btn {
    width: 100%;
    padding: 1rem;
    background: #8b0000;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 1rem;
    position: relative;
    overflow: hidden;
}

.auth-btn:hover {
    background: #6b0000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
}

.auth-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
}

.auth-btn:hover::after {
    animation: ripple 1s ease-out;
}

@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 0.5;
    }
    100% {
        transform: scale(20, 20);
        opacity: 0;
    }
}

.auth-links {
    text-align: center;
    margin-top: 1.5rem;
}

.auth-link {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.auth-link:hover {
    color: #8b0000;
}

.error-message {
    background: rgba(255, 0, 0, 0.1);
    border: 1px solid #8b0000;
    color: #ff4444;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: none;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.loading .auth-btn {
    pointer-events: none;
    opacity: 0.7;
}

.loading .auth-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.phone-input {
    position: relative;
    display: flex;
    align-items: center;
    background: #1a1a1a;
    border: 1px solid #333;
    border-radius: 8px;
    padding-left: 1rem;
}

.phone-input .form-control {
    border: none;
    background: transparent;
    margin-left: 0.5rem;
    padding-left: 0;
}

.phone-input .form-control:focus {
    border: none;
    box-shadow: none;
}

.phone-prefix {
    color: #fff;
    font-size: 1rem;
}

.phone-input .form-label {
    left: 3.5rem;
}

.phone-input .form-control:focus + .form-label,
.phone-input .form-control:not(:placeholder-shown) + .form-label {
    left: 3.5rem;
    top: -1.5rem;
    transform: scale(0.8);
    background: transparent;
    color: #8b0000;
}

.phone-input:focus-within {
    border-color: #8b0000;
    box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.2);
}
</style>

<div class="auth-container">
    <h1 class="auth-title">Вход</h1>
    
    <div class="error-message"></div>
    
    <form id="loginForm" novalidate>
        <div class="form-group">
            <div class="phone-input">
                <span class="phone-prefix">+7</span>
                <input type="tel" class="form-control" id="phone" placeholder=" " pattern="[0-9]{10}" required>
                <label class="form-label" for="phone">Номер телефона</label>
            </div>
        </div>
        
        <div class="form-group">
            <input type="password" class="form-control" id="password" placeholder=" " required>
            <label class="form-label" for="password">Пароль</label>
        </div>
        
        <div class="remember-me">
            <label class="switch">
                <input type="checkbox" id="rememberMe">
                <span class="slider"></span>
            </label>
            <span>Запомнить меня</span>
        </div>
        
        <button type="submit" class="auth-btn">Войти</button>
    </form>
    
    <div class="auth-links">
        <a href="register.php" class="auth-link">Нет аккаунта? Зарегистрируйтесь</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.querySelector('.error-message');
    const phoneInput = document.getElementById('phone');
    
    // Маска для телефона
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        e.target.value = value;
    });

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = '+7' + phoneInput.value;
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('rememberMe').checked;
        
        // Валидация
        if (phoneInput.value.length !== 10) {
            showError('Введите корректный номер телефона');
            return;
        }
        
        if (!password) {
            showError('Введите пароль');
            return;
        }
        
        // Добавляем класс загрузки
        loginForm.classList.add('loading');
        
        try {
            const response = await fetch('auth_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'login',
                    phone: phone,
                    password: password,
                    remember_me: rememberMe
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Анимация успешного входа
                loginForm.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 500);
            } else {
                showError(data.message || 'Неверный номер телефона или пароль');
            }
        } catch (error) {
            showError('Произошла ошибка при входе');
            console.error('Error:', error);
        } finally {
            loginForm.classList.remove('loading');
        }
    });
    
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        
        // Удаляем и добавляем класс для повторной анимации
        errorMessage.classList.remove('shake');
        void errorMessage.offsetWidth; // Форсируем перерисовку
        errorMessage.classList.add('shake');
        
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.style.display = 'none';
                errorMessage.style.opacity = '1';
            }, 300);
        }, 3000);
    }
});
</script>

<?php include 'footer.php'; ?>
