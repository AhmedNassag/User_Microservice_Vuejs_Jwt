<template>
    <div class="text-center">
        <router-link v-if="token" to="/" class="btn btn-primary m-1">Files</router-link>
        <router-link v-else to="/register" class="btn btn-primary m-1">Register</router-link>
        <button v-if="token" class="btn btn-primary m-1" @click="logout">Logout</button>
        <router-link v-else to="/login" class="btn btn-primary m-1">Login</router-link>
    </div>
</template>

<script>
    export default
    {
        data()
        {
            return {
                token: null,
            }
        },
        created()
        {
            this.checkToken();
            EventBus.$on('auth-updated', this.updateToken); // Listen for auth-updated events from EventBus (to hide Register & Login Buttons and appear Files & Logout Buttons after make login / to appear Register & Login Buttons and hide Files & Logout Buttons after make logout)
        },
        methods:
        {
            checkToken() {
                this.token = localStorage.getItem('token'); // Dynamically fetch token
            },
            updateToken(newToken) {
                this.token = newToken; // Update the token state
            },
            logout()
            {
                let token = localStorage.getItem('token')
                let user = localStorage.getItem('user')
                if (!token) return;
                axios.post('api/logout?token='+token).then(response=>
                {
                    // localStorage.setItem('token','')
                    localStorage.removeItem('token'); // Clear the token
                    localStorage.removeItem('user'); // Clear the user
                    this.token = null; // Update local data
                    this.user = null; // Update local data
                    this.updateToken(null); // Clear the token
                    this.$router.push({ name: 'Login' }); // Redirect to login
                });
            },
            beforeDestroy() {
                EventBus.$off('auth-updated', this.updateToken); // Clean up event listener to avoid memory leaks (to hide Register & Login Buttons and appear Files & Logout Buttons after make login / to appear Register & Login Buttons and hide Files & Logout Buttons after make logout)
            },
        },
    };
</script>
