<template>
<div class="container">
    <div class="row mb-3" v-if="categoryName">
        <h5>Category <span class="badge badge-secondary">{{ categoryName }}</span></h5>
    </div>
    <div class="row">
        <div class="col-12 col-lg-8">
          <template v-if="postsList">
            <post
                    v-for="post in postsList"
                    :key="post.id"
                    :title="post.title"
                    :text="post.description"
                    :date="post.readable_created_date"
                    :author="post.author"
                    :img="post.image_url"
                    :post-link="/post/+post.id"
            ></post>
          </template>
          <template v-if="postsList.length === 0">
              <div class="row">
                  No Posts to Display
              </div>
          </template>
        </div>
      <!-- Sidebar Widgets Column -->
      <div class="col-md-4">

        <!-- Categories Widget -->
        <div class="card">
          <h5 class="card-header">Categories</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg">
                <ul class="list-unstyled mb-0">
                  <li v-for="category in categoriesList">
                      <router-link :to="{path: '/categories/' + category.id}"><i class="fas fa-caret-right mx-2" v-if="category.parent_id"></i>{{ category.name }}</router-link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Info -->
        <div class="card my-4">
          <h5 class="card-header">About</h5>
          <div class="card-body">
            This is a very simple Blog built with Laravel, Vue.js and Bootstrap 4.
          </div>
        </div>

      </div>
    </div>
</div>
</template>

<script>
    import Post from '../components/Post'
    import axios from 'axios'
    export default {
        name: "Blog",
        data() {
          return {
              postsList: [],
              categoriesList: [],
              categoryName: ''
          }
        },
        created() {
            this.fetchPosts();
            this.fetchCategories();
        },
        beforeRouteUpdate(to, from, next) {
            next();
            this.fetchPosts();
        },
        watch: {
            '$route'() {
                // clear the category name when navigating out of a category
                if (this.$router.currentRoute.name !== 'CategoryPage') {
                    this.categoryName = '';
                }
                this.fetchPosts();
            }
        },
        components: {
            Post
        },
        methods: {
            fetchPosts() {
                // console.log('Current Route Name: ', this.$router.currentRoute.name);
                let categoryFilter = '';

                // Category Page: retrieve category to display it's info & set the posts filter for that category
                if (this.$router.currentRoute.name === 'CategoryPage') {
                    categoryFilter = `?c=${this.$router.currentRoute.params.id}`;
                    axios.get(`/api/categories/${this.$router.currentRoute.params.id}`).then(res => this.categoryName = res.data.parent_category ? `${res.data.parent_category} > ${res.data.name}` : res.data.name);
                } else if (this.$router.currentRoute.name === 'categories') { // default category page: retrieve the first category
                    categoryFilter = '?first=1'; // retrieve the first category
                    axios.get('/api/categories/0?first=1').then(res => this.categoryName = res.data.parent_category ? `${res.data.parent_category} > ${res.data.name}` : res.data.name);
                }

                axios.get(`/api/posts${categoryFilter}`).then(res => this.postsList = res.data);
            },
            fetchCategories() {
                axios.get('/api/categories').then(res => this.categoriesList = res.data);
            },
        }
    }
</script>

<style>

</style>
