/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import 'bootstrap/dist/css/bootstrap.css';

import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './views/App'
import NotFound from './views/NotFound';
import Blog from './views/Blog';
import PostPage from './views/PostPage';

Vue.use(VueRouter)

// SPA router
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            // main page
            path: '/blog',
            name: 'blog',
            component: Blog,
        },
        {
            // post page
            path: '/blog/:id',
            name: 'PostPage',
            component: PostPage
        },
        {
            // main page
            path: '/categories',
            name: 'categories',
            component: Blog,
        },
        {
            // post page
            path: '/categories/:id',
            name: 'CategoryPage',
            component: Blog
        },
        { path: '/404', name: '404', component: NotFound },
        { path: '*', redirect: '/404' },
    ],
});

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});
