<template>
  <div class="container-fluid">
    <div class="row vh-100 justify-content-center align-items-center login-container">
      <div class="col-md-5">
        <div class="card shadow-xl">
          <div class="card-body p-5">
            <!-- Logo and Title -->
            <div class="text-center mb-4">
              <div class="coffee-icon mb-3">
                <i class="fas fa-mug-hot fa-3x text-brown"></i>
              </div>
              <h2 class="welcome-text">Welcome Back!</h2>
              <p class="text-muted">Sign in to your coffee shop account</p>
            </div>

            <form @submit.prevent="login">
              <div class="form-group mb-4">
                <label for="email" class="form-label fw-bold">Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-envelope text-muted"></i>
                  </span>
                  <input type="email" v-model="email" class="form-control border-start-0" id="email"
                    placeholder="your@email.com" required />
                </div>
              </div>

              <div class="form-group mb-4">
                <div class="d-flex justify-content-between">
                  <label for="password" class="form-label fw-bold">Password</label>
                  <a href="#" class="text-decoration-none text-brown">Forgot Password?</a>
                </div>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-lock text-muted"></i>
                  </span>
                  <input :type="showPassword ? 'text' : 'password'" v-model="password"
                    class="form-control border-start-0" id="password" placeholder="Enter your password" required />
                  <span class="input-group-text bg-light cursor-pointer" @click="togglePassword">
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </span>
                </div>
              </div>
              <button type="submit" class="btn btn-brown w-100 mb-3" @click="goToHome">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
              </button>
            </form>

            <div class="text-center mt-4">
              <p class="mb-0">Don't have an account?
                <a href="/register" class="text-brown text-decoration-none fw-bold">Create Account</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import router from '@/router';
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      showPassword: false,
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/auth/login', { // ubah url sesuai route backend
          email: this.email,
          password: this.password
        });
        localStorage.setItem('token', response.data.data.token); // simpan token ke local storage
        localStorage.setItem('name', response.data.data.name); // simpan nama user ke local storage
        localStorage.setItem('role_id', response.data.data.role_id); // simpan role_id user ke local storage
        router.push({ name: 'home' });
      } catch (error) {
        this.errorMessage = error.response.data.message || 'Login failed';
      }
    },
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
  }
};
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  min-height: 100vh;
}

.card {
  border: none;
  border-radius: 15px;
  transition: transform 0.3s;
}

.card:hover {
  transform: translateY(-5px);
}

.text-brown {
  color: #6F4E37;
}

.btn-brown {
  background-color: #6F4E37;
  color: white;
  padding: 12px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s;
}

.btn-brown:hover {
  background-color: #5a3f2d;
  color: white;
  transform: translateY(-2px);
}

.coffee-icon {
  color: #6F4E37;
  animation: steam 2s infinite;
}

.welcome-text {
  color: #2c3e50;
  font-weight: 700;
}

.form-control {
  padding: 12px;
  border-radius: 8px;
}

.input-group-text {
  border-radius: 8px;
}

.form-control:focus {
  border-color: #6F4E37;
  box-shadow: 0 0 0 0.2rem rgba(111, 78, 55, 0.25);
}

.cursor-pointer {
  cursor: pointer;
}

@keyframes steam {
  0% {
    transform: translateY(0);
    opacity: 1;
  }

  50% {
    transform: translateY(-10px);
    opacity: 0.8;
  }

  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .card-body {
    padding: 2rem !important;
  }
}
</style>