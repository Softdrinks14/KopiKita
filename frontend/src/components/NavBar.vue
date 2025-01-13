<script setup>
import { RouterLink, useRouter } from 'vue-router';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps(['name']);
const loggedInUser = ref(null);
const userRole = ref(null);
const router = useRouter();
const isMenuOpen = ref(false);

// Computed property untuk mengecek apakah user adalah admin
const isAdmin = computed(() => {
  return userRole.value === '1';
});

const logout = () => {
  const response = axios.get('http://127.0.0.1:8000/api/auth/logout', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  }).then(function (response) {
    localStorage.removeItem('token');
    localStorage.removeItem('name');
    localStorage.removeItem('role_id');
    loggedInUser.value = null;
    userRole.value = null;
    router.push({ name: 'login' });
  }).catch(function (error) {
    console.log(error);
  });
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const fetchUserInfo = async () => {
  try {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role_id');
    const response = await axios.get('http://127.0.0.1:8000/api/auth/me', {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    });
    loggedInUser.value = response.data.data.name;
    userRole.value = role;
  } catch (error) {
    console.error('Error fetching user info:', error);
  }
};

onMounted(() => {
  if (localStorage.getItem('token')) {
    fetchUserInfo();
  }
});
</script>

<template>
  <header v-if="$route.name !== 'login'" class="coffee-header">
    <nav class="coffee-navbar">
      <div class="nav-brand">
        <img src="/Premium Photo _ Stylish coffee logo design for your brand.jpg" alt="Coffee Shop" class="logo" />
        <span class="brand-name">Kopi Kita</span>
      </div>

      <button class="menu-toggle" @click="toggleMenu" :class="{ 'is-active': isMenuOpen }">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="nav-content" :class="{ 'is-open': isMenuOpen }">
        <ul class="nav-links">
          <li>
            <RouterLink to="/home" class="nav-link">
              <i class="fas fa-home"></i>
              Home
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/about" class="nav-link">
              <i class="fas fa-info-circle"></i>
              About Us
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/history" class="nav-link">
              <i class="fas fa-history"></i>
              History
            </RouterLink>
          </li>
          <!-- Menampilkan Edit Item hanya untuk admin -->
          <li v-if="isAdmin">
            <RouterLink to="/admin-item" class="nav-link">
              <i class="fas fa-list"></i>
              Edit Item
            </RouterLink>
          </li>
        </ul>

        <div class="nav-user">
          <span class="user-greeting">
            <i class="fas fa-user"></i>
            Hi, {{ loggedInUser }}
          </span>
          <button @click="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Log Out
          </button>
        </div>
      </div>
    </nav>
  </header>
  <RouterView />
</template>

<style scoped>
.coffee-header {
  background: #6F4E37;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.coffee-navbar {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #FFF;
}

.nav-content {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-links {
  display: flex;
  gap: 1.5rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-link {
  color: #FFF;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.nav-link.router-link-active {
  background: rgba(255, 255, 255, 0.2);
  font-weight: bold;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-greeting {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.logout-btn {
  background: #8B4513;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #733A0F;
}

.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 6px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.menu-toggle span {
  display: block;
  width: 25px;
  height: 2px;
  background-color: #FFF;
  transition: all 0.3s ease;
}

@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }

  .nav-content {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #6F4E37;
    flex-direction: column;
    padding: 1rem;
    gap: 1rem;
    display: none;
  }

  .nav-content.is-open {
    display: flex;
  }

  .nav-links {
    flex-direction: column;
    width: 100%;
  }

  .nav-user {
    flex-direction: column;
    width: 100%;
  }

  .menu-toggle.is-active span:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
  }

  .menu-toggle.is-active span:nth-child(2) {
    opacity: 0;
  }

  .menu-toggle.is-active span:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
  }
}
</style>