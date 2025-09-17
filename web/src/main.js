import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from '../plugins/i18n.js'

import '@/assets/css/variables.css'
import '@/assets/css/base.css'
import '@/assets/css/utilities.css'
import '@/assets/css/components.css'

const app = createApp(App)
const loadFontAwesome = () => {
    const script = document.createElement('script')
    script.src = 'https://kit.fontawesome.com/ae2976ddfe.js'
    script.crossOrigin = 'anonymous'
    document.head.appendChild(script)
  }
  
  loadFontAwesome()

app.use(router)
app.use(i18n)

app.mount('#app')