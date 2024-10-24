import './bootstrap';
import router from './router'; // Import router

import { createApp } from 'vue'
import Signals from './views/Signals.vue';

// import App from './App.vue'

// const app = createApp(App)

// app.use(router)
// app.mount('#app')

createApp({})
    .component('SignalComponent', Signals)
    .mount('#app')
