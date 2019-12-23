<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading mb-3">
                        <button @click="initAddPost()" class="btn btn-primary btn-xs pull-right mr-2">
                            + Add New Post
                        </button>
                        <span class="text-large">Posts</span>
                        &nbsp;|&nbsp;
                        <a href="/admin/categories" class="text-link">Categories</a>
                    </div>

                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-responsive" v-if="this.posts.length > 0">
                            <tbody>
                            <tr>
                                <th>
                                    No.
                                </th>
                                <th>
                                    Title
                                </th>
                                <th>
                                    Description
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            <tr v-for="(post, index) in this.posts">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ post.title }}
                                </td>
                                <td>
                                    {{ post.description }}
                                </td>
                                <td>
                                    <button @click="initUpdate(index)" class="btn btn-success btn-xs">Edit</button>
                                    <button @click="deletePost(index)" class="btn btn-danger btn-xs">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="add_post_model">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Post</h4>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-danger" v-if="errors.length > 0">
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category:</label>
                            <input type="text" name="category_id" id="category_id" placeholder="Category ID" class="form-control"
                                   v-model="post.category_id">
                        </div>
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" name="title" id="title" placeholder="Title" class="form-control"
                                   v-model="post.title">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" name="description" id="description" placeholder="Description" class="form-control"
                                   v-model="update_post.description">
                        </div>
                        <div class="form-group">
                            <label>Text:</label>
                            <ckeditor :editor="editor" v-model="post.long_description" :config="editorConfig"></ckeditor>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" @click="createPost" class="btn btn-primary">Submit</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="update_post_model">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update Post</h4>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-danger" v-if="errors.length > 0">
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category:</label>
                            <input type="text" placeholder="Category ID" class="form-control"
                                   v-model="update_post.category_id">
                        </div>
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" placeholder="Title" class="form-control"
                                   v-model="update_post.title">
                        </div>
                        <div class="form-group">
                            <label for="title">Description:</label>
                            <input type="text" placeholder="Description" class="form-control"
                                   v-model="update_post.description">
                        </div>
                        <div class="form-group">
                            <label>Text:</label>
                            <ckeditor :editor="editor" v-model="update_post.long_description" :config="editorConfig" class="ckeditor-style"></ckeditor>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" @click="updatePost" class="btn btn-primary">Submit</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>
</template>

<script>
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    export default {
        data(){
            return {
                post: {
                    category_id: '',
                    title: '',
                    description: '',
                    long_description: ''
                },
                errors: [],
                posts: [],
                update_post: {},
                token: 'IjyOhUjG9MYyzp9HZdgu1cIKHuPIXG2S2pTZppJYjN5EXcCu0qpi6Rx7', // TODO: fetch from the BE
                editor: ClassicEditor,
                editorData: 'Text',
                editorConfig: {
                    toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                }
            }
        },
        mounted()
        {
            this.readPosts();
        },
        methods: {
            initAddPost()
            {
                this.errors = [];
                $("#add_post_model").modal("show");
            },
            createPost()
            {
                axios.post('/api/posts', {
                        category_id: this.post.category_id,
                        title: this.post.title,
                        description: this.post.description,
                        long_description: this.post.long_description,
                    },
                    {
                        headers: { Authorization: "Bearer " + this.token }
                    })
                    .then(response => {

                        this.reset();

                        $("#add_post_model").modal("hide");

                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.title) {
                            this.errors.push(error.response.data.errors.title[0]);
                        }

                        if (error.response.data.errors.description) {
                            this.errors.push(error.response.data.errors.description[0]);
                        }
                    });
            },
            reset()
            {
                this.post.category_id = '';
                this.post.title = '';
                this.post.description = '';
                this.post.long_description = '';
            },
            readPosts()
            {
                axios.get('/api/posts')
                    .then(response => {

                        this.posts = response.data;

                    });
            },
            initUpdate(index)
            {
                this.errors = [];
                $("#update_post_model").modal("show");
                this.update_post = this.posts[index];
            },
            updatePost()
            {
                axios.patch('/api/posts/' + this.update_post.id, {
                        category_id: this.update_post.category_id,
                        title: this.update_post.title,
                        description: this.update_post.description,
                        long_description: this.update_post.long_description
                    },
                    {
                        headers: { Authorization: "Bearer " + this.token }
                    })
                    .then(response => {

                        this.reset();
                        $("#update_post_model").modal("hide");

                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.name) {
                            this.errors.push(error.response.data.errors.name[0]);
                        }

                        if (error.response.data.errors.description) {
                            this.errors.push(error.response.data.errors.description[0]);
                        }
                    });
            },
            deletePost(index)
            {
                let conf = confirm("Do you really want to delete this Post?");
                if (conf === true) {

                    axios.delete('/api/posts/' + this.posts[index].id,
                        {
                            headers: { Authorization: "Bearer " + this.token }
                        })
                        .then(response => {

                            this.posts.splice(index, 1);

                        });
                }
            }
        }
    }
</script>
<style>
    .text-large {
        font-size: 150%;
        vertical-align: middle;
    }
    .text-link {
        vertical-align: middle;
    }
    .ckeditor-style {
        height: 140px;
    }
</style>
