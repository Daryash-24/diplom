// управление корзиной через localStorage
class Cart {
    constructor() {
        this.items = this.load();
        this.updateCounter();
    }

    load() {
        const stored = localStorage.getItem('whieda_cart');
        return stored ? JSON.parse(stored) : [];
    }

    save() {
        localStorage.setItem('whieda_cart', JSON.stringify(this.items));
        this.updateCounter();
    }

    add(item) {
        const existingIndex = this.items.findIndex(i =>
            i.id === item.id && 
            i.size === item.size && 
            i.color === item.color
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

const cart = new Cart();