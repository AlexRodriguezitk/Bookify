import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
//Boostrapp
import 'bootstrap'
import './assets/bootstrap.min.css'

const app = createApp(App)

app.use(router)
app.use(createPinia())

app.mount('#app')
