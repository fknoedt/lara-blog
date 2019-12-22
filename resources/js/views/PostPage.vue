<template>
  <div class="container">

    <div class="row">

      <!-- Post Content Column -->
      <div class="col-lg-8">


        <template v-if="fullPost">

          <!-- Title -->
          <h2 class="mt-4">{{ fullPost.title }}</h2>

          <!-- Author -->
          <p class="lead">
            by
            {{ fullPost.author }}
            on {{ fullPost.readable_created_date }}
          </p>

          <hr>

          <p>{{ fullPost.description }}</p>

          <!-- Preview Image -->
          <img class="img-fluid rounded" :src="fullPost.image_url" alt="">

          <hr>

          <p>{{ fullPost.long_description}}</p>
        </template>


        <hr>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
    export default {
        name: "PostPage",
        data() {
          return {
              id: this.$route.params.id,
              fullPost: {}
          }
        },
        created() {
            this.fetchData()
        },
        methods: {
            fetchData() {
                axios.get('/api/posts/'+this.id).then(res => this.fullPost = res.data);
            }
        }
    }
</script>

<style scoped>

</style>
