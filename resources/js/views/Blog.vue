<template>
<div class="container">
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
                    :post-link="/blog/+post.id"
            ></post>
          </template>
        </div>
      <!-- Sidebar Widgets Column -->
      <div class="col-md-4">

        <!-- Categories Widget -->
        <div class="card">
          <h5 class="card-header">Categories</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6">
                <ul class="list-unstyled mb-0">
                  <li v-for="category in categoriesList">
                    <router-link :to="{path: '/categories/' + category.id}">{{ category.name }}</router-link>
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
              categoriesList: []
          }
        },
        created() {
            this.fetchPosts();
            this.fetchCategories();
        },
        components: {
            Post
        },
        methods: {
            fetchPosts() {
                axios.get('/api/posts').then(res => this.postsList = res.data);
            },
            fetchCategories() {
                axios.get('/api/categories').then(res => this.categoriesList = res.data);
            },
        }
    }
</script>

<style>

</style>
