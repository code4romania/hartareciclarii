import '../assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import router from './router/index.ts'

import {LoadingPlugin} from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import {addAuthToken} from "./general.js";

import moment from 'moment';
import ro from "moment/dist/locale/ro"
moment.locale('ro', ro);

const app = createApp(App);

app.use(router)
app.use(LoadingPlugin)

app.config.globalProperties.$moment = moment;

app.mount('#app')

addAuthToken();
