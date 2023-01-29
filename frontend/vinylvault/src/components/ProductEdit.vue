<template>
  <div>
    <Auth ref="auth" />
  </div>
  <div class="container">
    <form>
      <div class="form-group">
        <label for="name">Name</label>
        <input
          type="text"
          class="form-control"
          id="name"
          v-model="name"
          placeholder="Enter product name"
        />
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <input
          type="text"
          class="form-control"
          id="description"
          v-model="description"
          placeholder="Enter product description"
        />
      </div>
      <div class="form-group">
        <label for="price">Price</label>
        <input
          type="text"
          class="form-control"
          id="price"
          v-model="price"
          placeholder="Enter product price"
        />
      </div>
      <button class="btn btn-primary" @click="saveProduct">Save</button>
    </form>
  </div>
</template>

<script lang="ts">
export default {
  data() {
    return {
      name: '',
      description: '',
      price: '',
    };
  },
  created() {
    this.fetchProduct();
  },
  methods: {
    async fetchProduct() {
      const response = await fetch('/api/products/' + this.$route.params.id, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token'),
        },
      });
      const product = await response.json();
      this.name = product.name;
      this.description = product.description;
      this.price = product.price;
    },
    async saveProduct() {
      const response = await fetch('/api/products/' + this.$router.params.id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + localStorage.getItem('token'),
        },
        body: JSON.stringify({
          name: this.name,
          description: this.description,
          price: this.price,
        }),
      });
      if (response.ok) {
        this.$router.push({ name: 'product-list' });
      }
    },
  },
};
</script>
