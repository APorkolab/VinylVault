import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import * as bootstrap from "bootstrap";
import * as $ from 'jquery';

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: {
      vue: '@vue/compat',
    },
  },
  plugins: [vue()],
});

