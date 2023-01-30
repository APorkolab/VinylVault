<template>
  <div>
    <div v-if="!isToken">
      <Login ref="auth" />
    </div>
    <div v-else class="container">
      <table class="table table-striped table-dark">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col">Price</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="item" v-for="item in products" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.price }}</td>
            <td>
              <router-link :to="{ name: 'edit', params: { id: item.id } }">
                Edit
              </router-link>
              <button class="btn btn-danger" @click="deleteProduct(item.id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import Auth from './Auth.vue';
import { Product } from './../models/product';
import axios from 'axios';
import * as localForage from 'localforage';
import Login from './Login.vue';

export default {
  name: 'ProductList',
  data() {
    return {
      products: [] as Product[],
    };
  },
  mounted() {
    this.getProducts();
  },

  methods: {
    async getProducts() {
      try {
        let allProduct = await axios.get(
          'http://localhost/vinylvault/products',
          {
            headers: {
              Authorization: 'Bearer ' + localForage.getItem('token'),
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (allProduct.status === 400) {
          const data = allProduct.data;
          localForage.removeItem('token');
          console.log('Please login!');
          this.$router.push('/login');
        } else {
          this.products = allProduct.data;
        }
      } catch (error) {
        console.error(error);
      }
    },
    async deleteProduct(id: number) {
      try {
        let result = await axios.delete(
          `http://localhost/vinylvault/products/${id}`,
          {
            headers: {
              Authorization: 'Bearer ' + localForage.getItem('token'),
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (result.status === 400) {
          const data = result.data;
          localForage.removeItem('token');
          console.log('Please login!');
          this.$router.push('/login');
        } else {
          console.log(result.data);
          this.products = this.products.filter((product) => product.id !== id);
        }
      } catch (error) {
        console.error(error);
      }
    },
    async createProduct(product: Product) {
      try {
        let response = await axios.post(
          'http://localhost/vinylvault/products',
          product,
          {
            headers: {
              Authorization: 'Bearer ' + 'tarara',
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (response.status === 400) {
          localForage.removeItem('token');
          console.log('Please login!');
          this.$router.push('/login');
        } else {
          console.log('Product created successfully!');
          this.$router.push('/products');
        }
      } catch (error) {
        console.error(error);
      }
    },
    async isToken() {
      return (await localForage.getItem('token')) === null || undefined
        ? false
        : true;
    },
  },
  components: { Login },
};
</script>
