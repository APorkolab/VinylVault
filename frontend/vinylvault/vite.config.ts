import { defineConfig } from 'vite'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import vue from '@vitejs/plugin-vue'
import { router } from "./src/router.ts"


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue({
      pluginOptions: {
        plugins: [VueRouter, Vuex]
      }
    })
  ],
  alias: {
    "@": __dirname + '/src'
  },
  history: 'browser'
})
