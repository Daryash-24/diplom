<?php /* Template Name: Корзина */ ?>
<?php get_header(); ?>

<div class="cart-page">
    <h1>Корзина</h1>
    <div id="cart-items"></div>
    <div id="cart-total"></div>
    <div id="checkout-button" class="checkout-btn">Перейти к оформлению</div>
    <div id="contacts-info" style="display:none;">
        <h2>Контакты для оформления заказа</h2>
        <p>Для завершения заказа свяжитесь с нашим менеджером:</p>
        <ul>
            <li>📧 Email: <a href="mailto:manager@whieda.com">manager@whieda.com</a></li>
            <li>📞 Телефон: +7 (123) 456-78-90</li>
            <li>💬 Telegram: <a href="https://t.me/whieda_manager" target="_blank">@whieda_manager</a></li>
            <li>📱 ВКонтакте: <a href="https://vk.com/whieda" target="_blank">vk.com/whieda</a></li>
        </ul>
    </div>
</div>

<script>
function renderCart() {
    const items = cart.getItems();
    const container = document.getElementById('cart-items');
    if (items.length === 0) {
        container.innerHTML = '<p>Корзина пуста.</p>';
        document.getElementById('cart-total').innerHTML = '';
        return;
    }
    let html = '<ul class="cart-list">';
    items.forEach((item, index) => {
        html += `
            <li class="cart-item">
                <div class="item-info">
                    <strong>${item.title}</strong><br>
                    Цена: ${item.price} руб.<br>
                    Размер: ${item.size || 'не выбран'}<br>
                    Цвет: ${item.color || 'не выбран'}<br>
                    Количество: 
                    <button class="qty-minus" data-index="${index}">-</button>
                    ${item.quantity}
                    <button class="qty-plus" data-index="${index}">+</button>
                </div>
                <button class="remove-item" data-index="${index}">Удалить</button>
            </li>
        `;
    });
    html += '</ul>';
    container.innerHTML = html;
    const total = cart.getTotal();
    document.getElementById('cart-total').innerHTML = `<p>Итого: ${total} руб.</p>`;

    // обработчики кнопок
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', e => {
            const idx = parseInt(btn.dataset.index);
            cart.updateQuantity(idx, -1);
            renderCart();
        });
    });
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', e => {
            const idx = parseInt(btn.dataset.index);
            cart.updateQuantity(idx, 1);
            renderCart();
        });
    });
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', e => {
            const idx = parseInt(btn.dataset.index);
            cart.remove(idx);
            renderCart();
        });
    });
}

document.getElementById('checkout-button').addEventListener('click', function() {
    document.getElementById('contacts-info').style.display = 'block';
});

renderCart();
</script>

<?php get_footer(); ?>