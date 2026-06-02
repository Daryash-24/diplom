// управление корзиной через localStorage
class Cart {
    constructor() {
        this.items = this.load();
        this.updateCounter();
    }

    load() {
        const stored = localStorage.getItem('whieda_cart');
        let items = stored ? JSON.parse(stored) : [];
        items = items.map(item => {
            if (!item.optionsKey) {
                if (item.options && Object.keys(item.options).length > 0) {
                    item.optionsKey = JSON.stringify(item.options);
                } else if (item.size || item.color) {
                    item.optionsKey = (item.size || '') + '|' + (item.color || '');
                } else {
                    item.optionsKey = '';
                }
            }
            return item;
        });
        return items;
    }

    save() {
        localStorage.setItem('whieda_cart', JSON.stringify(this.items));
        this.updateCounter();
    }

    add(item) {
    if (!item.optionsKey) {
        // Если передан объект options, создаём ключ
        if (item.options && Object.keys(item.options).length > 0) {
            item.optionsKey = JSON.stringify(item.options);
        } else if (item.size || item.color) {
            // Старый способ (size/color)
            item.optionsKey = (item.size || '') + '|' + (item.color || '');
        } else {
            item.optionsKey = '';
        }
    }

    // Ищем товар с таким же id и такими же опциями
    const existingIndex = this.items.findIndex(i =>
        i.id === item.id && i.optionsKey === item.optionsKey
    );

    if (existingIndex !== -1) {
        this.items[existingIndex].quantity += 1;
    } else {
        item.quantity = 1;
        this.items.push(item);
    }
    this.save();
}

    remove(index) {
        this.items.splice(index, 1);
        this.save();
    }

    updateQuantity(index, delta) {
        if (index < 0 || index >= this.items.length) return;
        
        const newQty = this.items[index].quantity + delta;
        if (newQty <= 0) {
            this.remove(index);
        } else {
            this.items[index].quantity = newQty;
            this.save();
        }
    }

    getTotal() {
        return this.items.reduce((sum, item) => sum + (parseFloat(item.price) || 0) * (item.quantity || 1), 0);
    }

    updateCounter() {
        const counter = document.querySelector('.cart-counter');
        if (counter) {
            const totalItems = this.items.reduce((sum, item) => sum + (item.quantity || 1), 0);
            counter.textContent = totalItems;
            counter.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
    }

    getItems() {
        return [...this.items]; // возвращаем копию
    }

    clear() {
        this.items = [];
        localStorage.removeItem('whieda_cart');
        this.updateCounter();
        console.log('Корзина очищена');
    }
}

// Функция показа уведомления (глобальная)
window.showNotification = function(message, type = 'success') {
    const container = document.getElementById('notification-toast');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.textContent = message;

    if (type === 'error') {
        toast.style.borderLeftColor = '#e3348e';
    } else {
        toast.style.borderLeftColor = '#658a34';
    }

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 2000);
};

const cart = new Cart();