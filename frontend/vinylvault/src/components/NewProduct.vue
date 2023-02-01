<template>
  <div>
    <div await v-if="!isTokenLoaded">
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

        <button
          class="btn btn-primary"
          @click="
            newProduct();
            showModal();
          "
        >
          Save
        </button>
        <a href="/products" class="btn btn-warning" role="button">Cancel</a>
      </form>
      <!-- Bootstrap modal -->
      <!-- Button trigger modal -->
      <div>
        <b-button v-b-modal.modal-1>Launch demo modal</b-button>
        <b-button @click="showModal()">eee demo modal</b-button>

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
      modalTitle: '',
      modalMessage: '',
      modalShow: false,
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
            'Content-Type': 'application/json',
          },
        };

        const response = await axios.post(
          'http://localhost/vinylvault/products',
          data,
          config
        );

        if (response.status === 201) {
          this.modalTitle = 'Success';
          this.modalMessage = 'Product created successfully';
          this.showModal();
        } else {
          this.modalTitle = 'Error';
          this.modalMessage = 'Error creating product';
          this.showModal();
        }
      } catch (error) {
        this.modalTitle = 'Error';
        this.modalMessage = 'Error creating product';
        console.error(error);
      }
      // this.$router.push('/products');
    },
    async showModal() {
      this.$root!.$emit('bv::show::modal', 'modal-1');
    },
  },
  computed: {
    async isTokenLoaded(): Promise<boolean> {
      const token = await localForage.getItem(this.modalMessage);
      return Boolean(token);
    },
  },
};
</script>
