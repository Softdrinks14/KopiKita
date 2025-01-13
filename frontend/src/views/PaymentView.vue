<template>
  <div class="payment-container">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="payment-card">
            <div class="payment-header">
              <h2>
                <i class="fas fa-credit-card me-2"></i>
                Pembayaran
              </h2>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
              <h3>Ringkasan Pesanan</h3>
              <div class="mb-5">
                <div>
                  <label for="customerName" class="form-label">Customer Name</label>
                  <input type="text" class="form-control" id="customerName" placeholder="name@example.com"
                    v-model="customerName" />
                </div>
                <div>
                  <label for="tableNo" class="form-label">Table No</label>
                  <input type="text" class="form-control" id="tableNo" v-model="tableNo" />
                </div>
              </div>
              <div class="order-items">
                <div v-for="(item, index) in cart" :key="index" class="order-item">
                  <div class="item-details">
                    <span class="item-name">{{ item.name }}</span>
                    <span class="item-quantity">x{{ item.quantity }}</span>
                  </div>
                  <span class="item-price">Rp {{ formatPrice(item.price * item.quantity) }}</span>
                </div>
              </div>

              <div class="price-summary">
                <div class="summary-row">
                  <span>Subtotal</span>
                  <span>Rp {{ formatPrice(subtotal) }}</span>
                </div>
                <div class="summary-row total">
                  <span>Total</span>
                  <span>Rp {{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="payment-method">
              <h3>Metode Pembayaran</h3>
              <div class="payment-options">
                <div v-for="method in paymentMethods" :key="method.type" class="payment-option"
                  :class="{ active: paymentType === method.type }" @click="setPaymentMethod(method.type)">
                  <i :class="method.icon"></i>
                  <span>{{ method.name }}</span>
                </div>
              </div>

              <!-- Cash Payment Input -->
              <div v-if="paymentType === 'tunai'" class="cash-payment mt-4">
                <div class="form-group">
                  <label>Jumlah Uang Diterima</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" v-model.number="cashReceived" class="form-control"
                      placeholder="Masukkan jumlah uang" />
                  </div>
                </div>
                <div class="change-amount" v-if="changeAmount >= 0">
                  <span>Kembalian:</span>
                  <span class="change">Rp {{ formatPrice(changeAmount) }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="payment-actions">
              <button class="btn-cancel" @click="goBack">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
              </button>
              <button class="btn-confirm" @click="confirmPayment" :disabled="!canConfirmPayment">
                <i class="fas fa-check me-2"></i>
                Konfirmasi Pembayaran
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      paymentType: "tunai",
      cashReceived: null,
      customerName: "",
      tableNo: "",
      paymentMethods: [
        { type: "tunai", name: "Tunai", icon: "fas fa-money-bill-wave" },
        { type: "qris", name: "QRIS", icon: "fas fa-qrcode" },
        { type: "kartu", name: "Kartu Debit/Kredit", icon: "fas fa-credit-card" },
        { type: "ewallet", name: "E-Wallet", icon: "fas fa-wallet" },
      ],
      cart: [],
      subtotal: 0,
      total: 0,
      qty: 0
    };
  },

  mounted() {
    const orderData = localStorage.getItem('orderData');
    if (orderData) {
      const parsedData = JSON.parse(orderData);
      this.cart = parsedData.items;
      this.subtotal = parsedData.subtotal;
      this.total = parsedData.total;
      this.quantity = parsedData.quantity;
    }
  },

  computed: {
    changeAmount() {
      if (!this.cashReceived || this.cashReceived < 0) return -1;
      return this.cashReceived - this.total;
    },
    canConfirmPayment() {
      if (!this.customerName || !this.tableNo) return false;
      if (this.paymentType === "tunai") {
        return this.cashReceived >= this.total;
      }
      return true;
    }
  },

  methods: {
    formatPrice(price) {
      if (!price || isNaN(price)) return "0";
      return price.toLocaleString("id-ID");
    },

    goBack() {
      this.$router.push({ name: "home" });
    },

    setPaymentMethod(type) {
      this.paymentType = type;
      this.cashReceived = null;
    },

    async confirmPayment() {
      try {
        // Format items to include both id and quantity
        const formattedItems = this.cart.map(item => ({
          id: item.id,
          qty: item.quantity
        }));

        const paymentData = {
          customer_name: this.customerName,
          table_no: this.tableNo,
          items: formattedItems,  // Now includes both id and quantity
          status: 'ordered'
        };

        console.log('Sending payment data:', paymentData);

        const response = await axios.post('http://127.0.0.1:8000/api/order', paymentData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.data) {
          localStorage.removeItem('orderData');
          alert('Pembayaran berhasil!');
          this.$router.push({ name: 'home' });
        }
      } catch (error) {
        console.error('Error processing payment:', error);
        alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
      }
    }
  },
};
</script>


<style scoped>
.payment-container {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 20px;
}

.payment-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.payment-header {
  background: #6F4E37;
  color: white;
  padding: 20px;
}

.payment-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.order-summary,
.payment-method {
  padding: 20px;
  border-bottom: 1px solid #eee;
}

h3 {
  color: #2c3e50;
  font-size: 1.2rem;
  margin-bottom: 20px;
}

.order-items {
  max-height: 300px;
  overflow-y: auto;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.item-details {
  display: flex;
  gap: 10px;
}

.item-quantity {
  color: #666;
}

.item-price {
  font-weight: 500;
}

.price-summary {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #eee;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.summary-row.total {
  font-weight: bold;
  font-size: 1.2rem;
  color: #2c3e50;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #eee;
}

.payment-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.payment-option {
  padding: 15px;
  border: 2px solid #eee;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s;
}

.payment-option:hover {
  border-color: #6F4E37;
}

.payment-option.active {
  border-color: #6F4E37;
  background: rgba(111, 78, 55, 0.1);
}

.payment-option i {
  font-size: 1.2rem;
  color: #6F4E37;
}

.cash-payment {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
}

.change-amount {
  margin-top: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.1rem;
}

.change {
  font-weight: bold;
  color: #2c3e50;
}

.payment-actions {
  padding: 20px;
  display: flex;
  gap: 15px;
}

.btn-cancel,
.btn-confirm {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-cancel {
  background: #f8f9fa;
  color: #2c3e50;
}

.btn-confirm {
  background: #6F4E37;
  color: white;
  flex-grow: 1;
}

.btn-confirm:hover {
  background: #5a3f2d;
}

.btn-confirm:disabled {
  background: #ccc;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .payment-options {
    grid-template-columns: 1fr;
  }

  .payment-actions {
    flex-direction: column;
  }
}
</style>