<template>
  <div>
    <div v-if="!isTokenLoaded">
      <router-link to="/login">
        <button class="btn btn-primary">Login</button>
      </router-link>
    </div>
    <div v-else class="container">
      <form id="productForm">
        <div class="form-group">
          <label for="name">Name</label>
          <input
            type="text"
            class="form-control"
            id="name"
            v-model="product.name"
            placeholder="Enter product name"
          />
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <input
            type="text"
            class="form-control"
            id="description"
            v-model="product.description"
            placeholder="Enter product description"
          />
        </div>
        <div class="form-group">
          <label for="price">Price</label>
          <input
            type="text"
            class="form-control"
            id="price"
            v-model="product.price"
            placeholder="Enter product price"
          />
        </div>
        <div class="form-group">
          <label for="is_available">Is available?</label>
          <input
            type="checkbox"
            id="is_available"
            v-model="product.is_avaible"
          />
        </div>
        <button class="btn btn-primary" @click="saveProduct()">Save</button>
        <a href="/products" class="btn btn-warning" role="button">Cancel</a>
      </form>
    </div>
  </div>
</template>
<script lang="ts">
import * as localForage from 'localforage';
import axios from 'axios';
import { identifier } from '@babel/types';
import { Product } from './../models/product';
export default {
  data() {
    return {
      product: {} as Product,
      isToken: false,
    };
  },
  created() {
    if (this.$route.params.id != '0') {
      this.getProduct();
    }
  },
  methods: {
    async getProduct() {
      try {
        const token = await localForage.getItem('access_token');
        const productResponse = await axios.get(
          'http://localhost/vinylvault/products/' + this.$route.params.id,
          {
            headers: {
              Authorization:
                'Bearer  ' +
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

        if (productResponse.status === 200) {
          this.product = productResponse.data;
        } else {
          localForage.removeItem('access_token');
          console.log('Please login!');
          this.$router.push('/login');
        }
      } catch (error) {
        console.error(error);
      }
    },

    async saveProduct() {
      try {
        const data = {
          name: this.product.name,
          description: this.product.description,
          price: this.product.price,
          is_avaible: this.product.is_avaible ? 1 : 0,
        };

        const productResponse = await axios.patch(
          'http://localhost/vinylvault/products/' + this.$route.params.id,
          JSON.stringify(data),
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
              'Access-Control-Allow-Headers': '*',
            },
          }
        );
        // this.product = productResponse.data;
      } catch (error) {
        console.error(error);
      }
      // this.$router.push({ name: 'products' });
    },
  },
  computed: {
    async isTokenLoaded(): Promise<boolean> {
      const token = await localForage.getItem('access_token');
      return Boolean(token);
    },
  },
};
</script>
