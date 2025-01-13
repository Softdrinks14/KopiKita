<template>
  <NavBar :name="loggedInUser" />
  <div class="pos-container">
    <div class="container-fluid py-4">
      <div class="row">
        <!-- Kategori Menu -->
        <div class="col-md-2 mb-3">
          <div class="categories-wrapper">
            <button v-for="category in categories" :key="category" class="category-btn mb-2"
              :class="{ active: selectedCategory === category }" @click="selectedCategory = category">
              <i :class="getCategoryIcon(category)" class="me-2"></i>
              {{ category }}
            </button>
          </div>
          <!-- <p></p> -->
          <!-- Tambahkan section ini setelah categories-wrapper div -->
          <!-- <div class="col-md-2 mb-3">
            <div class="admin-divider">
              <button class="category-btn mb-2" @click="openAddModal">
                <i class="fas fa-plus me-2"></i>
                Add Item
              </button>
              <button class="category-btn mb-2" @click="openEditModal">
                <i class="fas fa-edit me-2"></i>
                Edit Item
              </button>
              <button class="category-btn mb-2" @click="openDeleteModal">
                <i class="fas fa-trash me-2"></i>
                Delete Item
              </button>
            </div>
          </div> -->
          <!-- <div class="categories-wrapper">
            <button class="category-btn mb-2" to="/admin-item" :disabled="!confirmCRUDItem">
              <i class="fas fa-plus me-2"></i>
              Add Item
            </button>
          </div> -->
        </div>

        <!-- Menu Items Grid -->
        <div class="col-md-7">
          <div class="menu-section">
            <div class="row g-4">
              <div class="col-md-4" v-for="(item, index) in filteredItems" :key="index">
                <div class="menu-card" @click="addToCart(item)">
                  <div class="menu-img-wrapper">
                    <div class="menu-badge" v-if="item.popular">Popular</div>
                    <img v-if="item.image" :src="item.image" :alt="item.name">
                  </div>
                  <div class="menu-card-body">
                    <h5 class="menu-title">{{ item.name }}</h5>
                    <p class="menu-price">Rp {{ formatPrice(item.price) }}</p>
                  </div>
                  <div class="menu-card-overlay">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add to Order</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sesi  -->
        <div class="col-md-3">
          <div class="cart-section">
            <div class="cart-header">
              <h3><i class="fas fa-shopping-cart me-2"></i> Current Order</h3>
              <button class="btn-clear" @click="clearCart" v-if="cart.length">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>

            <div class="cart-items" v-if="cart.length">
              <div v-for="(item, index) in cart" :key="index" class="cart-item">
                <div class="cart-item-details">
                  <h6>{{ item.name }}</h6>
                  <p>Rp {{ formatPrice(item.price) }}</p>
                </div>
                <div class="cart-item-quantity">
                  <button @click="decrementQuantity(index)" class="btn-quantity">-</button>
                  <span>{{ item.quantity }}</span>
                  <button @click="incrementQuantity(index)" class="btn-quantity">+</button>
                </div>
              </div>
            </div>

            <div class="cart-empty" v-else>
              <i class="fas fa-shopping-basket"></i>
              <p>No items in cart</p>
            </div>

            <div class="cart-summary" v-if="cart.length">
              <div class="summary-item">
                <span>Subtotal</span>
                <span>Rp {{ formatPrice(subtotal) }}</span>
              </div>
              <div class="summary-item total">
                <span>Total</span>
                <span>Rp {{ formatPrice(total) }}</span>
              </div>
            </div>

            <button class="btn-checkout" @click="goToPayment" :disabled="!cart.length">
              <i class="fas fa-cash-register me-2"></i>
              Proceed to Payment
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import router from '@/router';
import NavBar from '@/components/NavBar.vue';
import axios from 'axios';

export default {
  components: {
    NavBar
  },
  data() {
    return {
      role: 0,
      loggedInUser: '',
      selectedCategory: '',
      categories: [],
      menuItems: [],
      cart: [],
      url: 'http://127.0.0.1:8000/storage/items/',
    };
  },

  async mounted() {
    this.loggedInUser = localStorage.getItem('name');
    if (!this.loggedInUser) {
      router.push({ name: 'home' });
    }
    await this.getItems();
    this.categories = [...new Set(this.menuItems.map(item => item.category))];
    if (this.categories.length > 0) {
      this.selectedCategory = this.categories[0];
    }
  },

  computed: {
    filteredItems() {
      return this.menuItems.filter(item => item.category === this.selectedCategory);
    },
    subtotal() {
      return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    total() {
      return this.subtotal;
    },
    confirmCRUDItem() {
      role = getlocalStorage.getItem('role_id');
      if (this.role === 1) {
        return true;
      }
    }
  },

  methods: {
    async getItems() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/item', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.menuItems = response.data.data.map(item => ({
          ...item,
          image: item.image ? this.url + item.image : '',
        }));
      } catch (error) {
        console.error('Error fetching items:', error);
      }
    },

    getCategoryIcon(category) {
      const icons = {
        'Hot Coffee': 'fas fa-mug-hot',
        'Cold Coffee': 'fas fa-glass-whiskey',
        'Pastries': 'fas fa-bread-slice',
        'Snacks': 'fas fa-cookie'
      };
      return icons[category] || 'fas fa-utensils';
    },

    formatPrice(price) {
      return price.toLocaleString('id-ID');
    },

    addToCart(item) {
      const existingItem = this.cart.find(cartItem => cartItem.id === item.id);
      if (existingItem) {
        existingItem.quantity++;
      } else {
        this.cart.push({ ...item, quantity: 1 });
      }
    },

    incrementQuantity(index) {
      this.cart[index].quantity++;
    },

    decrementQuantity(index) {
      if (this.cart[index].quantity > 1) {
        this.cart[index].quantity--;
      } else {
        this.cart.splice(index, 1);
      }
    },

    clearCart() {
      this.cart = [];
    },

    goToPayment() {
      const orderData = {
        subtotal: this.subtotal,
        total: this.total,
        items: this.cart,
      };

      localStorage.setItem('orderData', JSON.stringify(orderData));

      router.push({
        name: 'payment',
        params: {
          cart: JSON.stringify(this.cart)
        },
        query: {
          subtotal: this.subtotal,
          total: this.total
        }
      });
    }
  }
};
</script>

<style scoped>
.pos-container {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 20px;
}

.categories-wrapper {
  background: white;
  padding: 15px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.category-btn {
  width: 100%;
  padding: 12px;
  border: none;
  background: transparent;
  text-align: left;
  border-radius: 8px;
  transition: all 0.3s;
  color: #555;
}

.category-btn:hover,
.category-btn.active {
  background: #6F4E37;
  color: white;
}

.menu-section {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-title {
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
}

.menu-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  position: relative;
  transition: transform 0.3s;
}

.menu-card:hover {
  transform: translateY(-5px);
}

.menu-img-wrapper {
  position: relative;
  padding-top: 75%;
  background: #f8f9fa;
}

.menu-img-wrapper img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.menu-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #e74c3c;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
}

.menu-card-body {
  padding: 15px;
}

.menu-title {
  font-size: 1rem;
  margin: 0;
  color: #2c3e50;
}

.menu-price {
  color: #6F4E37;
  font-weight: 600;
  margin: 5px 0 0;
}

.menu-card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(111, 78, 55, 0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s;
}

.menu-card:hover .menu-card-overlay {
  opacity: 1;
}

.cart-section {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.cart-items {
  max-height: 400px;
  overflow-y: auto;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.cart-item-quantity {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-quantity {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  border: 1px solid #6F4E37;
  background: transparent;
  color: #6F4E37;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cart-empty {
  text-align: center;
  padding: 40px 0;
  color: #888;
}

.cart-empty i {
  font-size: 3rem;
  margin-bottom: 10px;
}

.cart-summary {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #eee;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.summary-item.total {
  font-weight: bold;
  font-size: 1.2rem;
  color: #2c3e50;
}

.btn-checkout {
  width: 100%;
  padding: 15px;
  background: #6F4E37;
  color: white;
  border: none;
  border-radius: 8px;
  margin-top: 20px;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-checkout:hover {
  background: #5a3f2d;
}

.btn-checkout:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-clear {
  background: none;
  border: none;
  color: #e74c3c;
  cursor: pointer;
}

@media (max-width: 768px) {
  .menu-section {
    margin-top: 20px;
  }

  .cart-section {
    margin-top: 20px;
  }
}
</style>