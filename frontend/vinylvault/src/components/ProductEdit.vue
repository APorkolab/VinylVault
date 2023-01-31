<template>
  <div>
    <div v-if="!isToken">
      <Login ref="auth" />
    </div>
    <div v-else class="container">
      <form>
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
        <button class="btn btn-primary" @click="saveProduct">Save</button>
      </form>
    </div>
  </div>
</template>
<script lang="ts">
import * as localForage from 'localforage';
import axios from 'axios';

export default {
  data() {
    return {
      product: {
        name: '',
        description: '',
        price: '',
      },
    };
  },
  created() {
    this.getProduct();
  },
  methods: {
    async getProduct() {
      try {
        const productResponse = await axios.get(
          'http://localhost/vinylvault/products/' + this.$route.params.id,
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
        this.product = productResponse.data;
      } catch (error) {
        console.error(error);
      }
    },
    async saveProduct() {
      try {
        const productResponse = await axios.put(
          'http://localhost/vinylvault/products/' + this.$route.params.id,
          this.product,
          {
            headers: {
              Authorization: 'Bearer ' + (await localForage.getItem('token')),
              'HTTP-STATUS': '200',
              'Content-Type': 'application/json',
              'Access-Control-Allow-Origin': '*',
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
            },
          }
        );
        if (productResponse.status === 200) {
          this.$router.push({ name: 'product-list' });
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
};
</script>
