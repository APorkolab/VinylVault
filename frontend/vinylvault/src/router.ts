import Vue from 'vue'
import VueRouter from 'vue-router'
import { createRouter, createWebHistory } from 'vue-router'
import ProductList from './components/ProductList.vue'
import ProductEdit from './components/ProductEdit.vue'
import NewProduct from './components/NewProduct.vue'
import Register from './components/Register.vue'
import Login from './components/Login.vue'
import Auth from './components/Register.vue'



export default createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: '/',
			component: ProductList,
		},
		{
			path: '/products',
			name: 'ProductList',
			component: ProductList
		},
		{
			path: '/products/:id',
			name: 'edit',
			component: ProductEdit,
			props: true
		},
		{
			path: '/login',
			name: 'Login',
			component: Login
		},
		{
			path: '/register',
			name: 'Register',
			component: Register
		},
		{
			path: '/products/new',
			name: 'newProduct',
			component: NewProduct
		}
	]
})