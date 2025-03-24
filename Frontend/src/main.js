import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
//Boostrapp
import 'bootstrap'
import './assets/bootstrap.min.css'

const app = createApp(App)

app.use(router)

app.mount('#app')
