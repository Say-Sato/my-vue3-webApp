// src/main.js
import './assets/main.css'

import { createApp } from 'vue' // VueのcreateApp関数をインポート
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import 'cropperjs/dist/cropper.css'

import App from './App.vue' // ルートコンポーネントをインポート
import router from './router'

// Vueアプリケーションを作成
const app = createApp(App).use(router)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
app.use(pinia)

// アプリを特定のDOM要素にマウント
app.mount('#app') // 'app' IDを持つ要素にマウント
