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
    max-width: 500px;
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
    z-index: 2;
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
}

.form-control:focus + .form-label,
.form-control:not(:placeholder-shown) + .form-label {
    top: 0;
    transform: translateY(-50%) scale(0.8);
    color: #8b0000;
    background: #121212;
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
    transform: translateY(-2.5rem) scale(0.8);
}

.phone-input:focus-within {
    border-color: #8b0000;
    box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.2);
}

.password-input {
    position: relative;
    margin-bottom: 0.5rem;
}

.password-input .form-control {
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

.password-input .form-label {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    transition: all 0.3s ease;
    pointer-events: none;
    background: transparent;
    padding: 0 0.5rem;
}

.password-input .form-control:focus + .form-label,
.password-input .form-control:not(:placeholder-shown) + .form-label {
    top: 0;
    transform: translateY(-50%) scale(0.8);
    color: #8b0000;
    background: #121212;
}

.password-requirements-container {
    margin-top: 1rem;
}

.password-requirements {
    font-size: 0.9rem;
    color: #666;
    padding: 1rem;
    background: rgba(139, 0, 0, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(139, 0, 0, 0.2);
    margin: 0;
}

.password-requirements li {
    margin: 0.5rem 0;
    transition: color 0.3s ease;
    list-style-type: none;
    position: relative;
    padding-left: 1.5rem;
}

.password-requirements li:before {
    content: "×";
    position: absolute;
    left: 0;
    color: #8b0000;
    font-weight: bold;
}

.password-requirements li.met {
    color: #4CAF50;
}

.password-requirements li.met:before {
    content: "✓";
    color: #4CAF50;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 576px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="auth-container">
    <h1 class="auth-title">Регистрация</h1>
    
    <div class="error-message"></div>
    
    <form id="registerForm" novalidate>
        <div class="form-grid">
            <div class="form-group">
                <input type="text" class="form-control" id="name" placeholder=" " required>
                <label class="form-label" for="name">Ваше ФИО</label>
            </div>
            
            <div class="form-group">
                <input type="email" class="form-control" id="email" placeholder=" " required>
                <label class="form-label" for="email">Email</label>
            </div>
        </div>
        
        <div class="form-group">
            <div class="phone-input">
                <span class="phone-prefix">+7</span>
                <input type="tel" class="form-control" id="phone" placeholder=" " pattern="[0-9]{10}" required>
                <label class="form-label" for="phone">Номер телефона</label>
            </div>
        </div>
        
        <div class="form-group">
            <input type="text" class="form-control" id="address" placeholder=" ">
            <label class="form-label" for="address">Адрес (необязательно)</label>
        </div>
        
        <div class="form-group">
            <div class="password-input">
                <input type="password" class="form-control" id="password" placeholder=" " required>
                <label class="form-label" for="password">Пароль</label>
            </div>
            <div class="password-requirements-container">
                <ul class="password-requirements">
                    <li id="length">Минимум 8 символов</li>
                    <li id="letter">Хотя бы одна буква</li>
                    <li id="number">Хотя бы одна цифра</li>
                    <li id="special">Хотя бы один специальный символ (!@#$%^&*)</li>
                </ul>
            </div>
        </div>
        
        <div class="form-group">
            <input type="password" class="form-control" id="confirmPassword" placeholder=" " required>
            <label class="form-label" for="confirmPassword">Подтвердите пароль</label>
        </div>
        
        <button type="submit" class="auth-btn">Зарегистрироваться</button>
    </form>
    
    <div class="auth-links">
        <a href="login.php" class="auth-link">Уже есть аккаунт? Войдите</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const errorMessage = document.querySelector('.error-message');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    // Маска для телефона
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        e.target.value = value;
    });
    
    // Проверка требований к паролю
    const requirements = {
        length: value => value.length >= 8,
        letter: value => /[a-zA-Z]/.test(value),
        number: value => /[0-9]/.test(value),
        special: value => /[!@#$%^&*]/.test(value)
    };
    
    passwordInput.addEventListener('input', function() {
        const value = this.value;
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById(req);
            if (requirements[req](value)) {
                element.classList.add('met');
            } else {
                element.classList.remove('met');
            }
        });
    });
    
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = '+7' + phoneInput.value;
        const address = document.getElementById('address').value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Валидация
        if (!name) {
            showError('Введите ваше имя');
            return;
        }
        
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('Введите корректный email');
            return;
        }
        
        if (phoneInput.value.length !== 10) {
            showError('Введите корректный номер телефона');
            return;
        }
        
        if (!Object.keys(requirements).every(req => requirements[req](password))) {
            showError('Пароль не соответствует требованиям');
            return;
        }
        
        if (password !== confirmPassword) {
            showError('Пароли не совпадают');
            return;
        }
        
        // Добавляем класс загрузки
        registerForm.classList.add('loading');
        
        try {
            const response = await fetch('auth_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'register',
                    name: name,
                    email: email,
                    phone: phone,
                    address: address,
                    password: password
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Анимация успешной регистрации
                registerForm.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 500);
            } else {
                showError(data.message || 'Ошибка при регистрации');
            }
        } catch (error) {
            showError('Произошла ошибка при регистрации');
            console.error('Error:', error);
        } finally {
            registerForm.classList.remove('loading');
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
