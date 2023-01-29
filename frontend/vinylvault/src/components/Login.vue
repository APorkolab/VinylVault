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
              'Access-Control-Allow-Headers':
                'Content-Type, Authorization, X-Requested-With, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers',
              'Content-Type':
                'application/x-www-form-urlencoded; charset=UTF-8',
              'Access-Control-Allow-Origin': '*',
            },
          }
        );
        if (response.status === 200) {
          const data = response.data;
          localForage.setItem('token', data.access_token);
          this.$router.push('/home');
        } else {
          console.log('Invalid username or password');
        }
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
