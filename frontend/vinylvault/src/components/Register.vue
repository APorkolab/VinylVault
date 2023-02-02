<template>
  <div class="container mt-5">
    <h1 class="text-center">User Registration</h1>
    <form @submit.prevent="onSubmit" class="mt-5">
      <div class="form-group">
        <label for="name">Name</label>
        <input
          type="text"
          class="form-control"
          id="name"
          v-model="formData.name"
        />
      </div>

      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          class="form-control"
          id="username"
          v-model="formData.username"
        />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          v-model="formData.password"
        />
      </div>

      <button type="submit" class="btn btn-primary">Register</button>
    </form>
  </div>
</template>
<script lang="ts">
import axios from 'axios';
export default {
  data() {
    return {
      formData: {
        name: '',
        username: '',
        password: '',
      },
    };
  },
  methods: {
    async onSubmit() {
      try {
        const config = {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
        };
        const data = new URLSearchParams();
        data.append('username', this.formData.username);
        data.append('password', this.formData.password);
        data.append('name', this.formData.name);
        const response = await axios.post(
          'http://localhost/vinylvault/register.php',
          data,
          config
        );
        // success
        if (response.status === 200) {
          alert('Success Register!');
          console.log('Success Register!');
          this.$router.push('/login');
        }
      } catch (error) {
        console.error(error);
        alert('Error Register!');
      }
    },
  },
};
</script>
