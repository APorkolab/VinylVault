<template>
  <div>
    <!-- token tárolása -->
    <input type="hidden" v-model="token" />
  </div>
</template>

<script lang="ts">
export default {
  data() {
    return {
      token: '',
    };
  },
  methods: {
    setToken(token: string) {
      this.token = token;
    },
    getToken() {
      return this.token;
    },
  },
};
</script>
