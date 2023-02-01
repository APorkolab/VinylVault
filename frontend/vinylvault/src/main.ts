import { createApp } from 'vue'
import App from './App.vue'
import './style.css'
import router from './router'
import BootstrapVue from 'bootstrap-vue/dist/bootstrap-vue.esm';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'bootstrap/dist/css/bootstrap.css';


createApp(App)
	.use(router)
	.mount('#app')
	.$BootstrapVue
