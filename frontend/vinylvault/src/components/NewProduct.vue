<template>
  <div>
    <div v-if="!isToken">
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
        <button class="btn btn-primary" @click="newProduct()">Save</button>
        <a href="/products" class="btn btn-warning" role="button">Cancel</a>
      </form>
      <!-- Bootstrap modal -->
      <div
        class="modal fade"
        id="productModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="productModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="productModalLabel">
                {{ modalTitle }}
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              {{ modalMessage }}
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import * as localForage from 'localforage';
import axios from 'axios';
import { Product } from '../models/product';

export default {
  name: 'NewProduct',
  data() {
    return {
      product: new Product(),
      isToken: false,
      modalTitle: '',
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
          is_avaible: this.product.is_avaible,
        };

        const token = await localForage.getItem('access_token');

        const config = {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
          },
        };

        await axios.post('http://localhost/vinylvault/products', data, config);
        this.modalTitle = 'Success';
        this.modalMessage = 'Product created successfully';
      } catch (error) {
        this.modalTitle = 'Error';
        this.modalMessage = 'Error creating product';

        console.error(error);
      }
      this.$router.push({ name: 'products' });
    },
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
