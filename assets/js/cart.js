// управление корзиной через localStorage
class Cart {
    constructor() {
        this.items = this.load(); // загружаем сохраненные товары в свойство items
        this.updateCounter(); // обновление счетчика товаров
    }

    load() {
        const stored = localStorage.getItem('whieda_cart'); // хранение в спец.месте
        return stored ? JSON.parse(stored) : []; // трансформация хранилища в json
    }

    save() {
        localStorage.setItem('whieda_cart', JSON.stringify(this.items)); //превращение текущего списка в строку
        this.updateCounter(); // обновление счетчика
    }

    add(item) {
        // Проверяем, есть ли уже такой товар с такими же параметрами
        const existingIndex = this.items.findIndex(i =>
            i.id === item.id && i.size === item.size && i.color === item.color
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

    // изменение количества + и -
    updateQuantity(index, delta) {
        const newQty = this.items[index].quantity + delta;
        if (newQty <= 0) {
            this.remove(index);
        } else {
            this.items[index].quantity = newQty;
            this.save();
        }
    }
 
    // общая сумма
    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    // обновление счетчика в корзине 
    updateCounter() {
        const counter = document.querySelector('.cart-counter');
        if (counter) {
            const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
            counter.textContent = totalItems;
            counter.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
    }

    // отражение всех позиций для корзины
    getItems() {
        return this.items;
    }

    clear() {
        this.items = [];
        this.save();
    }
}

//создание объекта корзины
const cart = new Cart();