<?php /* Template Name: Корзина */ ?>
<?php get_header(); ?>

<div class="cart-page">
    <h1>Корзина</h1>
    
    <div id="cart-items"></div>
    <div id="cart-total"></div>

    <!-- Форма быстрого заказа -->
    <div id="checkout-form" class="checkout-form">
        <h2>Оформить заказ</h2>
        <p>Заполните данные, и менеджер свяжется с вами для подтверждения заказа.</p>
        
        <form id="order-form">
            <?php wp_nonce_field('send_order_nonce', 'security'); // ← Добавили защиту ?>

            <div class="form-group">
                <label for="customer_name">Имя (необязательно)</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Ваше имя">
            </div>
            
            <div class="form-group">
                <label for="customer_phone">Телефон <span class="required">*</span></label>
                <input type="tel" id="customer_phone" name="customer_phone" placeholder="+7 (___) ___-__-__">
            </div>
            
            <div class="form-group">
                <label for="customer_email">Email <span class="required">*</span></label>
                <input type="email" id="customer_email" name="customer_email" placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label for="customer_comment">Комментарий к заказу (необязательно)</label>
                <textarea id="customer_comment" name="customer_comment" rows="3" placeholder="Укажите удобное время для звонка или дополнительные пожелания"></textarea>
            </div>
            
            <button type="submit" id="submit-order" class="submit-order-btn">Отправить заказ менеджеру</button>
        </form>
    </div>

    <!-- Сообщение об успехе -->
    <div id="success-message" class="success-message" style="display: none;">
        <h2>Ваш заказ в обработке</h2>
        <p>Менеджер свяжется с вами в ближайшее время для подтверждения деталей заказа.</p>
        <button onclick="location.reload()" class="btn-back">Вернуться в каталог</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof cart === 'undefined') {
        console.error('cart.js не загружен');
        document.getElementById('cart-items').innerHTML = '<p>Ошибка загрузки корзины. Обновите страницу.</p>';
        return;
    }

    function renderCart() {
        const items = cart.getItems();
        const container = document.getElementById('cart-items');
        
        if (!items || items.length === 0) {
            container.innerHTML = '<p>Корзина пуста.</p>';
            document.getElementById('cart-total').innerHTML = '';
            document.getElementById('checkout-form').style.display = 'none';
            return;
        }

        let html = '<ul class="cart-list">';
        
        items.forEach(function(item, index) {
            let paramsHtml = '';
            if (item.optionsString) {
                paramsHtml = '<br><small>Параметры: ' + item.optionsString + '</small>';
            } else if (item.size || item.color) {
                paramsHtml = '<br><small>Размер: ' + (item.size || '—') + ' | Цвет: ' + (item.color || '—') + '</small>';
            }

            html += `<li class="cart-item">
                <div class="cart-item-image">
                    ${item.thumb ? `<img src="${item.thumb}" alt="${item.title}">` : ''}
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.title || 'Без названия'}</div>
                    <div class="cart-item-price">${parseFloat(item.price) || 0} руб.</div>
                    ${paramsHtml}
                </div>
                <div class="cart-item-actions">
                    <div class="cart-item-quantity">
                        <button class="qty-minus" data-index="${index}">-</button>
                        <span>${item.quantity || 1}</span>
                        <button class="qty-plus" data-index="${index}">+</button>
                    </div>
                    <button class="remove-item" data-index="${index}">Удалить</button>
                </div>
            </li>`;
        });

        html += '</ul>';
        container.innerHTML = html;

        const total = cart.getTotal();
        document.getElementById('cart-total').innerHTML = `<p><strong>Итого: ${total} руб.</strong></p>`;

        // Обработчики
        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (!isNaN(idx)) {
                    cart.updateQuantity(idx, -1);
                    renderCart();
                }
            });
        });

        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (!isNaN(idx)) {
                    cart.updateQuantity(idx, 1);
                    renderCart();
                }
            });
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                if (!isNaN(idx)) {
                    cart.remove(idx);
                    renderCart();
                }
            });
        });
    }

    renderCart();

    // Отправка заказа
    const orderForm = document.getElementById('order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('customer_phone').value.trim();
            const email = document.getElementById('customer_email').value.trim();

            if (!phone && !email) {
                alert('Пожалуйста, укажите телефон или email для связи');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'send_order');
            formData.append('cart_items', JSON.stringify(cart.getItems()));

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('checkout-form').style.display = 'none';
                    document.getElementById('success-message').style.display = 'block';
                    cart.clear();
                } else {
                    alert('Ошибка: ' + (data.data || 'Не удалось отправить заказ'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка соединения. Попробуйте ещё раз.');
            });
        });
    }
});
</script>

<?php get_footer(); ?>