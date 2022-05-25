/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import { DataTable, Paginate, ModalComponent,Increaser,PreviewFile,CheckableItem,GroupCheckBox,DropDown,DropDownItem } from "@danmerccoscco/personal";

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */


Vue.component("data-table", DataTable);
Vue.component("check-box", CheckableItem);
Vue.component("paginator", Paginate);
Vue.component("increaser", Increaser);
Vue.component("modal-component", ModalComponent);
Vue.component("PreviewFile", PreviewFile);
Vue.component("group-checkbox", GroupCheckBox);
Vue.component("drop-down", DropDown);
Vue.component("drop-down-item", DropDownItem);
//Vue.component('example-component', require('./components/ExampleComponent.vue').default);
const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
