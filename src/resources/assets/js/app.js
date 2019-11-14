/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue'
import App from './app.vue'
import store from './store'
import './bootstrap'

import { Modal, FormFile, ButtonGroup, Button } from 'bootstrap-vue/es/components'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTruck, faCloud, faFolder, faUpload, faDownload, faPlusSquare, faSyncAlt, faTrashAlt, faFileAlt, faLevelUpAlt, faHome, faArrowRight, faSave, faEdit, faEye, faSearch } from '@fortawesome/free-solid-svg-icons'
Vue.use(Modal, FormFile, ButtonGroup, Button);
library.add(faTruck, faCloud, faFolder, faFileAlt, faUpload, faDownload, faPlusSquare, faSyncAlt, faTrashAlt, faLevelUpAlt, faHome, faArrowRight, faDownload, faSave, faEdit, faEye, faSearch)
Vue.component('font-awesome-icon', FontAwesomeIcon)

 new Vue({
  el: '#filevuer-main',
  store,
  components: {
    app: App
  }
})
