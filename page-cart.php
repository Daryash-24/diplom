<?php /* Template Name: Корзина */ ?>
<?php get_header(); ?>

    <div class="cart-page">
    <h1 id="cart-title">Корзина</h1>
    
    <div id="cart-items"></div>
    <div id="cart-total"></div>

    <!-- Форма быстрого заказа -->
    <div id="checkout-form" class="checkout-form">
        <h2>Оформить заказ</h2>
        <p class="form-subtitle">Менеджер свяжется с вами для подтверждения заказа в ближайшее время</p>
        
        <form id="order-form">
            <?php wp_nonce_field('send_order_nonce', 'security'); ?>

            <div class="form-group">
                <label for="customer_name">Имя (необязательно)</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Ваше имя">
            </div>

            <div class="form-group">
                <label for="customer_email">Email <span class="required">*</span></label>
                <input type="email" id="customer_email" name="customer_email" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="customer_comment">Комментарий к заказу (необязательно)</label>
                <textarea id="customer_comment" name="customer_comment" rows="3" placeholder="Укажите удобное время для звонка или дополнительные пожелания"></textarea>
            </div>

            <!-- Чекбокс согласия -->
            <div class="form-group consent-group">
                <label class="consent-label">
                    <input type="checkbox" id="consent" name="consent" required>
                    <span>Я согласен/а на обработку моих персональных данных в соответствии с 
                        <a href="/privacy-policy" target="_blank">Политикой конфиденциальности</a>
                    </span>
                </label>
            </div>

            <button type="submit" id="submit-order" class="submit-order-btn">
                Отправить заказ менеджеру
            </button>
        </form>
    </div>

    <!-- Сообщение об успехе -->
    <div id="success-message" class="success-message" style="display: none;">
        <div class="success-icon">✓</div>
        <h2>Заказ принят в обработку</h2>
        <p>Менеджер свяжется с вами в ближайшее время для подтверждения деталей заказа.</p>
        <a href="/catalog" class="btn-back">Вернуться в каталог</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    if (typeof cart === 'undefined') {
        console.error('cart.js не загружен');
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

            const imageHtml = item.thumb 
                ? `<img src="${item.thumb}" alt="${item.title || 'Товар'}">` 
                : '<div class="no-image">Нет фото</div>';

            html += `<li class="cart-item">
                <div class="cart-item-image">
                    ${imageHtml}
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

        // Делегирование событий для кнопок в корзине
        container.onclick = function(e) {
            const target = e.target;
            const index = parseInt(target.dataset.index);

            if (isNaN(index)) return;

            if (target.classList.contains('qty-minus')) {
                cart.updateQuantity(index, -1);
                renderCart();
            } else if (target.classList.contains('qty-plus')) {
                cart.updateQuantity(index, 1);
                renderCart();
            } else if (target.classList.contains('remove-item')) {
                cart.remove(index);
                renderCart();
            }
        };
    }

    renderCart();

    // Отправка заказа
    const orderForm = document.getElementById('order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('customer_email').value.trim();
            const consent = document.getElementById('consent').checked;

            if (!email) {
                alert('Пожалуйста, укажите email для связи');
                return;
            }

            if (!consent) {
                alert('Необходимо дать согласие на обработку персональных данных');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'send_order');
            formData.append('cart_items', JSON.stringify(cart.getItems()));

            const submitBtn = document.getElementById('submit-order');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправляем...';

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('checkout-form').style.display = 'none';
                    document.getElementById('success-message').style.display = 'block';
                    
                    document.getElementById('cart-title').style.display = 'none';
                    document.getElementById('cart-items').style.display = 'none';
                    document.getElementById('cart-total').style.display = 'none';

                    cart.clear();
                    renderCart();
                } else {
                    alert('Ошибка: ' + (data.data || 'Не удалось отправить заказ'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке заказа.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
});
</script>

<?php get_footer(); ?>