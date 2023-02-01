<template>
  <div>
    <div v-if="!isToken">
      <Login ref="auth" />
    </div>

    <div v-else class="container">
      <router-link :to="{ name: 'newProduct', params: {} }">
        <button class="btn btn-primary">New product</button>
      </router-link>
      <div>
        <b-button v-b-modal.modal-1>Launch demo modal</b-button>

        <b-modal id="modal-1" title="BootstrapVue">
          <p class="my-4">Hello from modal!</p>
        </b-modal>
      </div>
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
                <button class="btn btn-warning">Edit product</button>
              </router-link>
              <button class="btn btn-danger" @click="deleteProduct(item.id)">
                Delete product
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import Auth from './Register.vue';
import { Product } from './../models/product';
import axios from 'axios';
import * as localForage from 'localforage';
import Login from './Login.vue';
import router from '../router';

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
      console.log(localForage.getItem('access_token'));
      try {
        let allProduct = await axios.get(
          'http://localhost/vinylvault/products',
          {
            headers: {
              Authorization:
                'Bearer ' +
                (await localForage.getItem('access_token').then((value) => {
                  return value;
                })),
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (allProduct.status === 200) {
          this.products = allProduct.data;
        } else {
          localForage.removeItem('access_token');
          console.log('Please login!');
          this.$router.push('/login');
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
              Authorization:
                'Bearer ' + (await localForage.getItem('access_token')),
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
          await localForage.removeItem('access_token');
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
              Authorization:
                'Bearer ' + (await localForage.getItem('access_token')),
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (response.status === 400) {
          await localForage.removeItem('access_token');
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
      return (await localForage.getItem('access_token')) === null || undefined
        ? false
        : true;
    },
  },
  components: { Login },
};
</script>
