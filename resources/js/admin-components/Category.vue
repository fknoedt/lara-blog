<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading mb-3">
                        <button @click="initAddCategory()" class="btn btn-primary btn-xs pull-right mr-2">
                            + Add New Category
                        </button>
                        <span class="text-large">Categories</span>
                        &nbsp;|&nbsp;
                        <a href="/admin/posts" class="text-link">Posts</a>
                    </div>

                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-responsive" v-if="this.categories.length > 0">
                            <tbody>
                            <tr>
                                <th>
                                    No.
                                </th>
                                <th>
                                    Slug
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Parent
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            <tr v-for="(category, index) in this.categories">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ category.slug }}
                                </td>
                                <td>
                                    {{ category.name }}
                                </td>
                                <td>
                                    {{ category.parent_category }}
                                </td>
                                <td>
                                    <button @click="initUpdate(index)" class="btn btn-success btn-xs">Edit</button>
                                    <button @click="deleteCategory(index)" class="btn btn-danger btn-xs">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="add_category_model">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Category</h4>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-danger" v-if="errors.length > 0">
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug:</label>
                            <input type="text" name="slug" id="slug" placeholder="Slug" class="form-control"
                                   v-model="category.slug">
                        </div>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" placeholder="Name" class="form-control"
                                   v-model="category.name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" @click="createCategory" class="btn btn-primary">Submit</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="update_category_model">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update Category</h4>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-danger" v-if="errors.length > 0">
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug:</label>
                            <input type="text" placeholder="Slug" class="form-control"
                                   v-model="update_category.slug">
                        </div>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" placeholder="Name" class="form-control"
                                   v-model="update_category.name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" @click="updateCategory" class="btn btn-primary">Submit</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>
</template>

<script>
    export default {
        data(){
            return {
                category: {
                    slug: '',
                    name: '',
                    parent_category: ''
                },
                errors: [],
                categories: [],
                update_category: {},
                token: 'IjyOhUjG9MYyzp9HZdgu1cIKHuPIXG2S2pTZppJYjN5EXcCu0qpi6Rx7' // TODO: fetch from the BE
            }
        },
        mounted()
        {
            this.readCategories();
        },
        methods: {
            initAddCategory()
            {
                this.errors = [];
                $("#add_category_model").modal("show");
            },
            createCategory()
            {
                axios.category('/api/categories', {
                        slug: this.category.slug,
                        name: this.category.name
                    },
                    {
                        headers: { Authorization: "Bearer " + this.token }
                    })
                    .then(response => {

                        this.reset();

                        $("#add_category_model").modal("hide");

                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.slug) {
                            this.errors.push(error.response.data.errors.slug[0]);
                        }

                        if (error.response.data.errors.name) {
                            this.errors.push(error.response.data.errors.name[0]);
                        }
                    });
            },
            reset()
            {
                this.category.slug = '';
                this.category.name = '';
                this.category.parent_category = '';
            },
            readCategories()
            {
                axios.get('/api/categories')
                    .then(response => {

                        this.categories = response.data;

                    });
            },
            initUpdate(index)
            {
                this.errors = [];
                $("#update_category_model").modal("show");
                this.update_category = this.categories[index];
            },
            updateCategory()
            {
                axios.patch('/api/categories/' + this.update_category.id, {
                        slug: this.update_category.slug,
                        name: this.update_category.name
                    },
                    {
                        headers: { Authorization: "Bearer " + this.token }
                    })
                    .then(response => {

                        $("#update_category_model").modal("hide");

                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.slug) {
                            this.errors.push(error.response.data.errors.slug[0]);
                        }

                        if (error.response.data.errors.name) {
                            this.errors.push(error.response.data.errors.name[0]);
                        }
                    });
            },
            deleteCategory(index)
            {
                let conf = confirm("Do you really want to delete this Category?");
                if (conf === true) {

                    axios.delete('/api/categories/' + this.categories[index].id,
                        {
                            headers: { Authorization: "Bearer " + this.token }
                        })
                        .then(response => {

                            this.categories.splice(index, 1);

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
</style>
