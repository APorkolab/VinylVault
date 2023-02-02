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
          <label for="is_avaible">Is available?</label>
          <input type="checkbox" id="is_avaible" v-model="product.is_avaible" />
        </div>

        <button class="btn btn-primary" @click="newProduct()">Save</button>
        <a href="/products" class="btn btn-warning" role="button">Cancel</a>
      </form>
      <div>
        <b-modal id="modal-1" title="BootstrapVue">
          <p class="my-4">{{ modalMessage }}</p>
        </b-modal>
      </div>
    </div>
    <div></div>
  </div>
</template>
<script lang="ts">
import * as localForage from 'localforage';
import axios from 'axios';
import { Product } from '../models/product';
import { BIcon, BIconCamera } from 'bootstrap-vue';

export default {
  name: 'NewProduct',
  data() {
    return {
      product: new Product(),
      isTokenLoaded: false,
      modalMessage: '',
    };
  },
  methods: {
    async newProduct() {
      try {
        const data = {
          name: this.product.name,
          description: this.product.description,
          price: this.product.price,
          is_avaible: this.product.is_avaible ? 1 : 0,
        };

        const token = await localForage.getItem('access_token');

        const config = {
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
        };

        const response = await axios
          .post('http://localhost/vinylvault/products', data, config)
          .then((response) => {
            if (response.status !== 201) {
              this.modalMessage = 'Error creating product';
            } else {
              this.modalMessage = 'Product created successfully';
            }
          })
          .then(() => {
            alert(this.modalMessage);
          })
          .catch((error) => {
            console.error(error);
            alert('Error creating product');
          });
      } catch (error) {
        console.error(error);
      }
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
