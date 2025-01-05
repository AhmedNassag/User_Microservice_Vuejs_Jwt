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
        },
        methods:
        {
            checkToken() {
                this.token = localStorage.getItem('token'); // Dynamically fetch token
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
                    this.$router.push({ name: 'Login' }); // Redirect to login
                });
            }
        },
        watch: {
            token() {
                // Watcher to trigger UI updates if needed
            }
        }
    };
</script>
