<template>
    <div>
        <input type="email" class="form-control m-1" placeholder="Enter Email" v-model="user.email">
        <input type="password" class="form-control m-1" placeholder="Enter Password" v-model="user.password">
        <button class="btn btn-primary btn-block m-1" @click="login">Login</button>
        <div v-if="error" class="alert alert-danger">The Credentials You Entered Are Incorrect, Please Try Again...</div>
    </div>
</template>

<script>
    export default {
        data()
        {
            return {
                user:
                {
                    email:'',
                    password:''
                },
                error:false,
                token:null
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
            login()
            {
                axios.post('api/login',this.user).then(response=>
                {
                    if(response.data.status=='success')
                    {
                        let token = response.data.token
                        let user = response.data.user
                        localStorage.setItem('token',token)
                        localStorage.setItem('user', JSON.stringify(user));
                        this.error = false
                        EventBus.$emit('auth-updated', token); // Emit auth-updated event via EventBus (to hide Register & Login Buttons and appear Files & Logout Buttons after make login / to appear Register & Login Buttons and hide Files & Logout Buttons after make logout)
                        this.$router.push({name:'File'})
                    }
                    else
                    {
                        this.error = true
                    }
                })
            }
        }
    }
</script>
