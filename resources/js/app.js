import '../assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import router from './router/index.ts'

import {LoadingPlugin} from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import {addAuthToken} from "./general.js";


const app = createApp(App);

app.config.performance = true
/*
const devtools = window.__VUE_DEVTOOLS_GLOBAL_HOOK__;
devtools.enabled = true
if (typeof devtools === 'object')
{
	devtools.enabled = true
	devtools.emit('app:init', app, '1.0.0', {})
}
 */
app.use(router)
app.use(LoadingPlugin)

app.mount('#app')

addAuthToken();