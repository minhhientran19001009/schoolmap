import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import ClientFooter from './components/layout/ClientFooter.vue'
import './assets/main.css'

const app = createApp(App)
app.component('ClientFooter', ClientFooter)
app.use(router)
app.mount('#app')

