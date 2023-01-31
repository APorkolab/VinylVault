<template>
  <div>
    <div class="container">
      <form @submit.prevent="login">
        <div class="form-group">
          <label for="username">Username</label>
          <input
            type="text"
            class="form-control"
            id="username"
            v-model="username"
            placeholder="Enter your username"
          />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            class="form-control"
            id="password"
            v-model="password"
            placeholder="Enter your password"
          />
        </div>
        <button class="btn btn-primary" type="submit">Login</button>
      </form>
    </div>
  </div>
</template>
<script lang="ts">
import axios from 'axios';
import * as localForage from 'localforage';
export default {
  data() {
    return {
      username: '',
      password: '',
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post(
          'http://localhost/vinylvault/api/login.php',
          {
            username: this.username,
            password: this.password,
          },
          {
            headers: {
              'Content-Type': 'application/json',
            },
          }
        );

        const data = response.data;
        await localForage.setItem('access_token', data.access_token);
        if ((await localForage.getItem('access_token')) !== null) {
          console.log(await localForage.getItem('access_token'));
          this.$router.push('/products');
        } else {
          console.log(
            'access_token: ' + (await localForage.getItem('access_token'))
          );
          console.log('Invalid username or password');
        }
      } catch (error) {
        console.log(
          'access_token: ' + (await localForage.getItem('access_token'))
        );
        console.error(error);
      }
    },
  },
};
</script>
