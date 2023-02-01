// --- // TODO: // - tesztelni a POST, PUT, metódusokat tesztelni a logoutot és
megírni // ---
<template>
  <div>
    <div await v-if="!isToken">
      <Login ref="auth" />
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
        <button class="btn btn-primary" @click="saveProduct(product.id)">
          Save
        </button>
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
        this.product = productResponse.data;
      } catch (error) {
        console.error(error);
      }
    },

    async saveProduct(productId: number) {
      try {
        let url = 'http://localhost/vinylvault/products/' + productId;
        const productData = JSON.stringify(this.product);
        const productResponse = await axios.put(url, productData, {
          headers: {
            Authorization:
              'Bearer ' + (await localForage.getItem('access_token')),
            'HTTP-STATUS': '200',
            'Content-Type': 'application/json',
          },
        });
        // if (productResponse.status === 200 || productResponse.status === 201) {
        // }
      } catch (error) {
        console.error(error);
      }
      this.$router.push({ name: 'products' });
    },
  },
  async goToProductPage() {
    try {
      await axios.get('http://localhost/vinylvault/products/', {
        headers: {
          Authorization:
            'Bearer ' + (await localForage.getItem('access_token')),
        },
      });
      this.$router.push({ name: 'products' });
    } catch (error) {
      console.error(error);
    }
  },
  computed: {
    async isToken() {
      const token = await localForage.getItem('access_token');
      const isToken = Boolean(token);
      return isToken;
    },
  },
};
</script>
