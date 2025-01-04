import Register from './components/Register';
import Login from './components/Login';
import File from './components/File';

const routes =
[
    {path:'/register',component:Register,name:'Register'},
    {path:'/login',component:Login,name:'Login'},
    {path:'/',component:File,name:'File'},
]

export default routes
