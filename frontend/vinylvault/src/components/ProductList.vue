<template>
  <div>
    <Auth ref="auth" />
  </div>
  <div class="container">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ product.price }}</td>
          <td>
            <router-link
              :to="{ name: 'edit', params: { id: product.id } }"
              class="btn btn-primary"
              >Edit</router-link
            >
            <button class="btn btn-danger" @click="deleteProduct(product.id)">
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { mapState } from 'vuex';
import { State } from './store';

import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

export default {
  name: 'ProductList',
  computed: {
    ...mapState<State>({
      products: (state) => state.products,
    }),
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await fetch('http://localhost/vinylvault/products', {
          method: 'GET',
          headers: {
            Authorization: 'Bearer ' + this.$refs.auth.getToken(),
          },
        });
        const data = await response.json();
        this.$store.commit('setProducts', data);
      } catch (error) {
        console.error(error);
      }
    },
    async deleteProduct(id) {
      try {
        const response = await fetch(
          `http://localhost/vinylvault/products/${id}`,
          {
            method: 'DELETE',
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          }
        );
        if (response.ok) {
          this.$store.commit('deleteProduct', id);
        }
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
