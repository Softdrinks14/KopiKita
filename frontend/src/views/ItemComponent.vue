<template>
    <NavBar :name="loggedInUser" />
    <div class="admin-container">
        <!-- Add/Edit Item Modal -->
        <div class="modal" v-if="showModal" @click.self="closeModal">
            <div class="modal-content">
                <h3>{{ editingItem ? 'Edit Item' : 'Add New Item' }}</h3>
                <form @submit.prevent="saveItem" class="item-form">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" v-model="formData.name" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label>Price (Rp):</label>
                        <input type="number" v-model="formData.price" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label>Category:</label>
                        <select v-model="formData.category" required class="form-input">
                            <option value="Hot Coffee">Hot Coffee</option>
                            <option value="Cold Coffee">Cold Coffee</option>
                            <option value="Pastries">Pastries</option>
                            <option value="Snacks">Snacks</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Image:</label>
                        <input type="file" @change="handleImageUpload($event)" accept=".jpg,.png" class="form-input">
                    </div>

                    <div class="modal-buttons">
                        <button type="submit" class="btn-save">Save</button>
                        <button type="button" @click="closeModal" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Controls -->
        <div class="admin-controls">
            <button @click="openAddModal" class="btn-add">
                <i class="fas fa-plus"></i> Add New Item
            </button>
        </div>

        <!-- Items Grid -->
        <div class="menu-section">
            <div class="items-grid">
                <div class="menu-item" v-for="item in menuItems" :key="item.id">
                    <div class="menu-card">
                        <div class="menu-img-wrapper">
                            <img v-if="item.image" :src="url + item.image" :alt="item.name">
                            <div v-else class="placeholder-img">No Image</div>
                        </div>
                        <div class="menu-card-content">
                            <div class="menu-info">
                                <h5 class="menu-title">{{ item.name }}</h5>
                                <span class="menu-category">{{ item.category }}</span>
                                <p class="menu-price">Rp {{ formatPrice(item.price) }}</p>
                            </div>
                            <div class="item-actions">
                                <button @click="editItem(item)" class="btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteItem(item.id)" class="btn-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
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
        NavBar,
    },
    data() {
        return {
            menuItems: [],
            showModal: false,
            editingItem: null,
            url: 'http://127.0.0.1:8000/storage/items/',
            formData: {
                name: '',
                price: '',
                image_file: '',
                category: 'Hot Coffee',
            }
        };
    },

    async mounted() {
        await this.getItems();
    },

    methods: {
        formatPrice(price) {
            return price.toLocaleString('id-ID');
        },

        async getItems() {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/item', {
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                });
                this.menuItems = response.data.data;
            } catch (error) {
                console.error('Error fetching items:', error);
            }
        },

        handleImageUpload(event) {
            this.formData.image_file = event.target.files[0];
        },

        openAddModal() {
            this.editingItem = null;
            this.formData = {
                name: '',
                price: '',
                category: 'Hot Coffee',
                image_file: ''
            };
            this.showModal = true;
        },

        editItem(item) {
            this.editingItem = item;
            this.formData = {
                name: item.name,
                price: item.price,
                category: item.category,
                image_file: item.files,
            };
            this.showModal = true;
        },

        async saveItem() {
            try {
                let formData = new FormData();
                formData.append('name', this.formData.name);
                formData.append('price', this.formData.price);
                formData.append('image_file', this.formData.image_file);
                formData.append('category', this.formData.category);
                formData.append('_method', this.editingItem ? 'PATCH' : 'POST');

                if (this.editingItem) {
                    await axios.post(`http://127.0.0.1:8000/api/item/${this.editingItem.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                    });
                } else {
                    await axios.post('http://127.0.0.1:8000/api/item', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                    });
                }

                await this.getItems();
                this.closeModal();
            } catch (error) {
                console.error('Error saving item:', error);
            }
        },

        async deleteItem(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                try {
                    await axios.delete(`http://127.0.0.1:8000/api/item/${id}`, {
                        headers: {
                            'Content-Type': 'application/json',
                            Authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                    });
                    await this.getItems();
                } catch (error) {
                    console.error('Error deleting item:', error);
                }
            }
        },

        closeModal() {
            this.showModal = false;
            this.editingItem = null;
            this.formData = {
                name: '',
                price: '',
                category: 'Hot Coffee',
                image_file: ''
            };
        }
    }
};
</script>

<style scoped>
.admin-container {
    padding: 20px;
}

.admin-controls {
    margin-bottom: 20px;
}

.btn-add {
    background: #6F4E37;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-add:hover {
    background: #5a3f2d;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
}

.item-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.form-input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-save {
    background: #6F4E37;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-cancel {
    background: #808080;
    color: #333;
    border: 1px solid #ddd;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.item-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-edit {
    background: #6F4E37;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    padding: 20px 0;
}

.menu-item {
    height: 100%;
}

.menu-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.menu-img-wrapper {
    aspect-ratio: 4/3;
    overflow: hidden;
    background: #f5f5f5;
}

.menu-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.menu-img-wrapper img:hover {
    transform: scale(1.05);
}

.placeholder-img {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eee;
    color: #666;
    font-size: 0.9rem;
}

.menu-card-content {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
}

.menu-info {
    flex: 1;
}

.menu-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #333;
}

.menu-category {
    font-size: 0.85rem;
    color: #666;
    display: block;
    margin-bottom: 8px;
}

.menu-price {
    font-size: 1.1rem;
    font-weight: 600;
    color: #6F4E37;
    margin: 0;
}

.item-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    padding-top: 12px;
    border-top: 1px solid #eee;
}

.btn-edit, .btn-delete {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: transform 0.2s, opacity 0.2s;
}

.btn-edit:hover, .btn-delete:hover {
    transform: scale(1.05);
    opacity: 0.9;
}

.btn-edit {
    background: #6F4E37;
}

.btn-delete {
    background: #dc3545;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .items-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
    }

    .menu-card-content {
        padding: 12px;
    }
}
</style>