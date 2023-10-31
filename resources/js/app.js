import '../assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import router from './router/index.ts'

import {LoadingPlugin} from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

const app = createApp(App);

app.use(router)
app.use(LoadingPlugin)

app.mount('#app')
