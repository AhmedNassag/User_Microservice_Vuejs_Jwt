<template>
    <div>
        <input type="text" class="form-control m-1" placeholder="Enter Name" v-model="user.name">
        <input type="email" class="form-control m-1" placeholder="Enter Email" v-model="user.email">
        <input type="password" class="form-control m-1" placeholder="Enter Password" v-model="user.password">
        <button class="btn btn-primary btn-block m-1" @click="register">Register</button>
        <div v-if="error" class="alert alert-danger">Error</div>
    </div>
</template>

<script>
export default
{
    data()
    {
        return {
            user:
            {
                name:'',
                email:'',
                password:''
            },
            error: false
        }
    },
    methods:
    {
        register()
        {
            axios.post('api/register',this.user).then(response=>
            {
                if(response.data.status=='error')
                {
                    this.error = true
                }
                else
                {
                    this.$router.push({name:'File'})
                }
            })
        }
    }
}
</script>
