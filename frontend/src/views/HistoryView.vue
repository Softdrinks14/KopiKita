<template>
  <NavBar :name="loggedInUser" />
  <div class="history-container">
    <div class="history-header">
      <div class="header-content">
        <h2>
          <i class="fas fa-history"></i>
          Riwayat Pesanan
        </h2>
        <div class="header-actions">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Cari pesanan..."
            >
          </div>
        </div>
      </div>
    </div>

    <div class="history-content">
      <div class="history-cards">
        <div v-for="order in filteredOrders" 
             :key="order.id"
             class="history-card"
             :class="{'deleted': order.isDeleting}"
        >
          <div class="card-header">
            <div class="transaction-info">
              <span class="transaction-number">#{{ padNumber(order.id) }}</span>
              <span class="customer-info">
                <i class="fas fa-user"></i>
                {{ order.customer_name }}
              </span>
              <span class="table-info">
                <i class="fas fa-chair"></i>
                Meja {{ order.table_no }}
              </span>
            </div>
            <div class="order-status" :class="order.status">
              {{ formatStatus(order.status) }}
            </div>
          </div>

          <div class="card-content">
            <div class="order-datetime">
              <span class="date">
                <i class="far fa-calendar-alt"></i>
                {{ formatDate(order.order_date) }}
              </span>
              <span class="time">
                <i class="far fa-clock"></i>
                {{ formatTime(order.order_time) }}
              </span>
            </div>
            <div class="amount">
              <span class="amount-label">Total Pesanan</span>
              <span class="amount-value">Rp {{ formatPrice(order.total) }}</span>
            </div>
          </div>

          <div class="card-actions">
            <button class="btn-detail" @click="showDetail(order.id)">
              <i class="fas fa-receipt"></i>
              Detail
            </button>
            <button 
              v-if="order.status === 'ordered'"
              class="btn-status btn-done"
              @click="markAsDone(order.id)"
            >
              <i class="fas fa-check"></i>
              Selesai
            </button>
            <button 
              v-if="order.status === 'done'"
              class="btn-status btn-paid"
              @click="markAsPaid(order.id)"
            >
              <i class="fas fa-money-bill"></i>
              Bayar
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" class="empty-state">
        <i class="fas fa-receipt"></i>
        <p>Tidak ada riwayat pesanan</p>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal-overlay" v-if="showDetailModal" @click="closeDetail">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Detail Pesanan #{{ selectedOrder?.id }}</h3>
          <button class="btn-close" @click="closeDetail">×</button>
        </div>
        <div class="modal-body" v-if="orderDetail">
          <div class="order-info">
            <p><strong>Pelanggan:</strong> {{ orderDetail.customer_name }}</p>
            <p><strong>Meja:</strong> {{ orderDetail.table_no }}</p>
            <p><strong>Tanggal:</strong> {{ formatDate(orderDetail.order_date) }}</p>
            <p><strong>Waktu:</strong> {{ formatTime(orderDetail.order_time) }}</p>
          </div>
          <div class="order-items">
            <h4>Item Pesanan</h4>
            <div class="item" v-for="detail in orderDetail.order_detail" :key="detail.id">
              <span class="item-name">{{ detail.item.name }}</span>
              <span class="item-qty">× {{ detail.qty }}</span>
              <span class="item-price">Rp {{ formatPrice(detail.price) }}</span>
            </div>
          </div>
          <div class="order-total">
            <strong>Total:</strong> 
            <span>Rp {{ formatPrice(orderDetail.total) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import axios from 'axios';
import NavBar from '@/components/NavBar.vue';

export default {
  components: {
    NavBar
  },
  setup() {
    const orders = ref([]);
    const searchQuery = ref('');
    const showDetailModal = ref(false);
    const selectedOrder = ref(null);
    const orderDetail = ref(null);

    const fetchOrders = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/order', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });
        orders.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch orders:', error);
      }
    };

    const filteredOrders = computed(() => {
      return orders.value.filter(order => {
        const searchLower = searchQuery.value.toLowerCase();
        return order.customer_name.toLowerCase().includes(searchLower) ||
               order.table_no.toString().includes(searchLower) ||
               order.status.toLowerCase().includes(searchLower);
      });
    });

    const showDetail = async (orderId) => {
      try {
        const response = await axios.get(`http://127.0.0.1/api/order/${orderId}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
        });
        orderDetail.value = response.data.data.id;
        selectedOrder.value = orders.value.find(o => o.id === orderId);
        showDetailModal.value = true;
      } catch (error) {
        console.error('Failed to fetch order details:', error);
      }
    };

    const markAsDone = async (orderId) => {
      try {
        await axios.get(`http://127.0.0.1:8000/api/order/${orderId}/done`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });
        await fetchOrders();
      } catch (error) {
        console.error('Failed to mark order as done:', error);
      }
    };

    const markAsPaid = async (orderId) => {
      try {
        await axios.get(`http://127.0.0.1:8000/api/order/${orderId}/paid`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          }
        });
        await fetchOrders();
      } catch (error) {
        console.error('Failed to mark order as paid:', error);
      }
    };

    // Fetch orders when component mounts
    fetchOrders();

    return {
      orders,
      searchQuery,
      showDetailModal,
      selectedOrder,
      orderDetail,
      filteredOrders,
      showDetail,
      markAsDone,
      markAsPaid
    };
  },
  methods: {
    padNumber(num) {
      return num.toString().padStart(4, '0');
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    formatTime(time) {
      return time.substring(0, 5);
    },
    formatPrice(price) {
      return price.toLocaleString('id-ID');
    },
    formatStatus(status) {
      const statusMap = {
        'ordered': 'Dipesan',
        'done': 'Selesai',
        'paid': 'Dibayar'
      };
      return statusMap[status] || status;
    },
    closeDetail() {
      this.showDetailModal = false;
      this.orderDetail = null;
    }
  }
};
</script>

<style scoped>
.user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
  font-size: 0.9rem;
}

.history-container {
  min-height: 100vh;
  background-color: #f8f9fa;
  padding: 2rem;
}

.history-header {
  background: white;
  padding: 1.5rem 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-content h2 {
  color: #2c3e50;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.search-box {
  position: relative;
  width: 300px;
}

.search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
}

.search-box input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.search-box input:focus {
  outline: none;
  border-color: #6F4E37;
  box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
}

.history-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.history-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s;
}

.history-card.deleted {
  transform: scale(0.9);
  opacity: 0;
}

.history-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  background: #f8f9fa;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.transaction-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.transaction-number {
  font-weight: 600;
  color: #6F4E37;
}

.transaction-date {
  color: #6c757d;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.payment-method {
  font-size: 0.9rem;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.payment-method.tunai {
  background: #e3fcef;
  color: #0c6b3d;
}

.payment-method.qris {
  background: #fff3cd;
  color: #856404;
}

.payment-method.kartu {
  background: #cce5ff;
  color: #004085;
}

.payment-method.ewallet {
  background: #f8d7da;
  color: #721c24;
}

.card-content {
  padding: 1.5rem;
}

.amount {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.amount-label {
  font-size: 0.9rem;
  color: #6c757d;
}

.amount-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.card-actions {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  gap: 1rem;
}

.btn-detail,
.btn-delete {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
  cursor: pointer;
}

.btn-detail {
  background: #6F4E37;
  color: white;
  flex-grow: 1;
}

.btn-detail:hover {
  background: #5a3f2d;
}

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-delete:hover {
  background: #c82333;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1.5rem;
}

.btn-cancel,
.btn-confirm {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-cancel {
  background: #e9ecef;
  color: #495057;
}

.btn-confirm {
  background: #dc3545;
  color: white;
}

@media (max-width: 768px) {
  .history-container {
    padding: 1rem;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
  }

  .search-box {
    width: 100%;
  }

  .history-cards {
    grid-template-columns: 1fr;
  }
}
</style>