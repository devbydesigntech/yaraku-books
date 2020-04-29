<template>
    <div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Books</h3>

                <!-- Add New Book -->
                <div class="card-tools">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addNew" @click="openModalWindow">Add New <i class="fas fa-user-plus fa-fw"></i></button>
                </div>

                <!-- Sort -->
                <div class="float-left mt-3">
                    <span>Sort: </span>
                    <a @click="sort('title')" href="#">Title<span v-if="sortBy === 'title'"></span></a>
                    |
                    <a @click="sort('author')" href="#">Author<span v-if="sortBy === 'author'"></span></a>
                </div>

                <!-- Search Bar -->
                <div class="float-right">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" name="q" placeholder="Search by Title or Author" aria-label="Search" v-model="searchQuery">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                    </button>
                    </div>
                </div>
                </div>
                
              </div>
             
             <!-- Data Table -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th></th>
                  </tr>
                  </thead> 
                    <tbody>
                    <tr v-for="book in temp" :key="book.id">
                        <td>{{ book.title }}</td>
                        <td>{{ book.author }}</td>
                        <td>
                            <!-- Edit/Delete -->
                            <a href="#" data-id="book.id" @click="editModalWindow(book)">
                                <button class="btn btn-warning">Edit</button>
                            </a>
                            <a href="#" @click="deleteBook(book.id)">
                                <button class="btn btn-danger">Delete</button>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
              </div>
              <div class="card-footer">
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Window -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">

                <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Add New Book</h5>
                <h5 v-show="editMode" class="modal-title" id="addNewLabel">Update Book</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form @submit.prevent="editMode ? updateBook() : createBook()">
            <div class="modal-body">
                <div class="form-group"><span><strong>Title</strong></span>
                    <input v-model="form.title" type="text" name="title"
                        placeholder="Title"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                    <has-error :form="form" field="title"></has-error>
                </div>
                <div class="form-group"><span><strong>Author</strong></span>
                    <input v-model="form.author" type="text" name="author"
                        placeholder="Author"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('author') }">
                    <has-error :form="form" field="author"></has-error>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button v-show="editMode" type="submit" class="btn btn-primary">Update</button>
                <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>
            </div>
        </div>
        </div>
    </div> 
    <!-- End Container -->
</template>

<script>
    export default {
        data() {
            return {
                editMode: false,
                books: {},
                form: new Form({
                    id: '',
                    title : '',
                    author: ''
                }),
                searchQuery:'',
                temp:'',
                sortBy: 'title',
                sortDirection :'descending'
            }
        },

        watch:{

            //Search by Title or Author
            searchQuery() {
                if (this.searchQuery.length > 0) {
                    this.temp = this.books.filter((results) => {
                        return Object.keys(results).some((key) => {
                            let string = String(results[key])
                            return string.toLowerCase().indexOf(this.searchQuery.toLowerCase()) > -1

                        })
                    });
                } else {
                    this.temp = this.books
                }
            }
        },


        methods: {

        //Sort by Title or Author with toggle
        sort(prop) {
            if (prop === this.sortBy) {
                this.sortDirection = this.sortDirection === 'ascending' ? 'descending' : 'ascending';
            } else {
                this.sortDirection = 'ascending';
            }
            this.sortBy = prop;

            this.books.sort((a,b) => {
                if(a[prop] < b[prop]) {
                    return this.sortDirection === 'ascending' ? -1 : 1;
                } else if (a[prop] > b[prop]) {
                    return this.sortDirection === 'ascending' ? 1 : -1;
                }
            })
        },
        
        // Modal Window for Editing
        editModalWindow(book){
           this.form.clear();
           this.editMode = true
           this.form.reset();
           $('#addNew').modal('show');
           this.form.fill(book)
        },
        updateBook(){
           this.form.put('api/book/'+this.form.id)
               .then(()=>{

                   Toast.fire({
                      icon: 'success',
                      title: 'Book updated successfully'
                    })

                    Fire.$emit('AfterCreatedBookLoadIt');

                    $('#addNew').modal('hide');
               })
               .catch(()=>{
                  console.log("Error.....")
               })
        },
        openModalWindow(){
           this.editMode = false
           this.form.reset();
           $('#addNew').modal('show');
        },

        loadBooks() {

        axios.get("api/book").then( data => (this.books = this.temp = data.data));

        },

        // Add New Book
        createBook(){

            this.$Progress.start()

            this.form.post('api/book')
                .then(() => {
                   
                    Fire.$emit('AfterCreatedBookLoadIt'); //custom events

                        Toast.fire({
                          icon: 'success',
                          title: 'Book created successfully'
                        })

                        this.$Progress.finish()

                        $('#addNew').modal('hide');

                })
                .catch(() => {
                   console.log("Error......")
                })

          },

          //Modal Window for Delete
          deleteBook(id) {
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                
              if (result.value) {
                //Send Request to server
                this.form.delete('api/book/'+id)
                    .then((response)=> {
                            Swal.fire(
                              'Deleted!',
                              'Book deleted successfully',
                              'success'
                            )
                    this.loadBooks();
                    
                    }).catch(() => {
                        Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'Something went wrong!',
                        })
                    })
                }
            })
          }
        },

        //Return List of Books after Add
        created() { 
            this.loadBooks();
            Fire.$on('AfterCreatedBookLoadIt',()=>{ //custom events fire on
                this.loadBooks();
            });
        },
    }
</script> 