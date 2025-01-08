import Register from './components/Register';
import Login from './components/Login';
import File from './components/File';
import Role from './components/Role';
import User from './components/User';

const routes =
[
    {path:'/register',component:Register,name:'Register'},
    {path:'/login',component:Login,name:'Login'},
    {path:'/',component:File,name:'File'},
    {path:'/roles',component:Role,name:'Role'},
    {path:'/users',component:User,name:'User'},
]

export default routes
