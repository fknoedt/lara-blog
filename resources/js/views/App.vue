<template>
    <div>
       <div class="container my-4">
         <h1><router-link :to="{ name: 'blog' }"><i class="fas fa-book-reader mx-4"></i></router-link>Simple Laravel & Vue Blog</h1>
       </div>

        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    export default {}
</script>
<style>
    #app {
        margin-top: 10px
    }
</style>
