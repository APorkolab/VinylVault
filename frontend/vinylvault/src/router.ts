import Vue from 'vue'
import VueRouter from 'vue-router'
import ProductList from './components/ProductList.vue'
import ProductEdit from './components/ProductEdit.vue'
import Auth from './components/Auth.vue'



const routes = [
	{
		path: '/',
		name: 'ProductList',
		component: ProductList
	},
	{
		path: '/edit/:id',
		name: 'ProductEdit',
		component: ProductEdit
	},
	{
		path: '/auth',
		name: 'Auth',
		component: Auth
	}
]

const router = new VueRouter({
	routes
})

export { router }