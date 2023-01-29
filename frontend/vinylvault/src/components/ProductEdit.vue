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
import Auth from './Auth.vue';

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
    this.fetchProduct();
  },
  methods: {
    async fetchProduct() {
      const response = await fetch('/api/products/' + this.$route.params.id, {
        headers: {
          Authorization: 'Bearer ' + localForage.getItem('token'),
        },
      });
      const product = await response.json();
      this.product = product;
    },
    async saveProduct() {
      const response = await fetch('/api/products/' + this.$route.params.id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + localForage.getItem('token'),
        },
        body: JSON.stringify(this.product),
      });
      if (response.ok) {
        this.$router.push({ name: 'product-list' });
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
