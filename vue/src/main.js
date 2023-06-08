import { createApp } from 'vue'
import store from './store'
import router from './router'
import App from './App.vue'

import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css"
import "datatables.net-bs5"
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css"
import "datatables.net-responsive-dt"
import './style.css'

createApp(App)
  .use(store)
  .use(router)
  .mount('#app')


