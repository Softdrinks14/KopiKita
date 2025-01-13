<template>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <!-- <div class="logo-container">
                <img src="@/assets/coffee-cup.svg" alt="Coffee Cup Logo" class="coffee-logo">
            </div> -->
            
            <!-- Welcome Text -->
            <h1 class="welcome-text">Create Account</h1>
            <p class="subtitle">Sign up for your coffee shop account</p>

            <!-- Registration Form -->
            <form @submit.prevent="handleRegister" class="auth-form">
                <!-- Username Field -->
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            v-model="formData.name"
                            placeholder="Enter your username"
                            required
                            class="form-input"
                        >
                    </div>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-container">
                        <i class="fas fa-envelope input-icon"></i>
                        <input 
                            type="email" 
                            v-model="formData.email"
                            placeholder="your@email.com"
                            required
                            class="form-input"
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            v-model="formData.password"
                            placeholder="Enter your password"
                            required
                            class="form-input"
                        >
                        <i 
                            :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                            class="toggle-password"
                            @click="showPassword = !showPassword"
                        ></i>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-button">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <p class="bottom-text">
                Already have an account? 
                <router-link to="/" class="auth-link">Sign In</router-link>
            </p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

export default {
    setup() {
        const router = useRouter();
        const showPassword = ref(false);
        const formData = ref({
            name: '',
            email: '',
            password: '',
        });

        const handleRegister = async () => {
            try {
                const payload = {
                    ...formData.value,
                    role_id: '2'  // user pasti memiliki role 2
                };

                const response = await axios.post('http://127.0.0.1:8000/api/register', payload);
                
                // notifikasi success
                alert('Registration successful!');
                router.push('/');
            } catch (error) {
                if (error.response && error.response.data) {
                    // show error
                    const errors = error.response.data;
                    let errorMessage = '';
                    for (const key in errors) {
                        errorMessage += `${errors[key]}\n`;
                    }
                    alert(errorMessage);
                } else {
                    alert('Registration failed. Please try again.');
                }
            }
        };

        return {
            formData,
            showPassword,
            handleRegister
        };
    }
};
</script>

<style scoped>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background-color: #f5f5f5;
}

.auth-card {
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.logo-container {
    text-align: center;
    margin-bottom: 24px;
}

.coffee-logo {
    width: 48px;
    height: 48px;
    color: #6F4E37;
}

.welcome-text {
    font-size: 24px;
    font-weight: 600;
    color: #2D3748;
    text-align: center;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #718096;
    margin-bottom: 32px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #4A5568;
    font-weight: 500;
}

.input-container {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
}

.form-input {
    width: 100%;
    padding: 12px 40px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus {
    border-color: #6F4E37;
    outline: none;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
    cursor: pointer;
}

.auth-button {
    width: 100%;
    padding: 12px;
    background-color: #6F4E37;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
}

.auth-button:hover {
    background-color: #5a3f2d;
}

.bottom-text {
    text-align: center;
    margin-top: 24px;
    color: #718096;
}

.auth-link {
    color: #6F4E37;
    text-decoration: none;
    font-weight: 500;
}

.auth-link:hover {
    text-decoration: underline;
}
</style>