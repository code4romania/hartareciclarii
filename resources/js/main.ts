import '../assets/main.css'
import {addAuthToken, checkAuthRoutes, checkUnauthorizedResponse} from './general.js';

import { createApp } from 'vue'
import App from "./App.vue"
import router from './router/index.ts'

import {LoadingPlugin} from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

createApp(App).use(router).use(LoadingPlugin).mount("#app");

addAuthToken();