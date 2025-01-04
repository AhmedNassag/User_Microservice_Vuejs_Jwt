require('./bootstrap');
window.Vue = require('vue').default;
import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter)
import AppHeader from './components/AppHeader';
import routes from './routes';
import Swal from 'sweetalert2';
window.Swal = Swal
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
window.Toast = Toast

const router = new VueRouter({
    mode:'history',
    routes
})
const app = new Vue({
    el: '#app',
    router,
    components:
    {
        AppHeader,
    }
});
