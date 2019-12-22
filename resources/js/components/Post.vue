<template>
    <!-- Blog Post -->
    <div class="card mb-4">
        <img class="card-img-top" :src="img" alt="Card image cap">
        <div class="card-body">
            <h2 class="card-title">{{ title }}</h2>
            <p class="card-text">{{ text }}</p>
            <router-link :to="postLink" class="btn btn-primary">Read More &rarr;</router-link>
        </div>
        <div class="card-footer text-muted">
            Posted on {{ date }} by {{ author }}
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            img : {
                type: String,
                default: 'http://placehold.it/750x300'
            },
            title: {
                type: String,
                default: 'Post title'
            },
            text: {
                type: String,
                default: 'Post text'
            },
            postLink: {
                type: String,
                default: '/blog'
            },
            date: {
                type: String,
                default: '?'
            },
            author: {
                type: String,
                default: '?'
            }
        }
    }
</script>
<style>

</style>
