import { createApp } from 'vue'
import './style.css'
import router from './src/router.ts'
import App from './App.vue'

createApp(App).mount('#app')

Vue.config.productionTip = false

new Vue({
	router,
	render: (h) => h(App),
}).$mount('#app')