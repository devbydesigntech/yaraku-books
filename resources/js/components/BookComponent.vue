<template>
    <div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <!-- Export -->
                <form action="/export" method="GET">
                    <div class="float-right mr-3">
                        <div class="float-left pr-2 pt-1">Select: </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="title" value="title" v-model="handlerTitle" @change="loadTitles()"> 
                            <label class="form-check-label" for="inlineCheckbox1">Title</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="author" value="author" v-model="handlerAuthor" @change="loadAuthors()">
                            <label class="form-check-label" for="inlineCheckbox2">Author</label>
                        </div>
                        <div class="btn btn-outline-primary" @click="CsvExport()">
                            Export .csv
                        </div>
                        <div class="btn btn-outline-primary" @click="XmlExport()">
                            Export .xml
                        </div>
                    </div>
                </form>
                <h3 class="card-title">Books</h3>

                <!-- Add New Book -->
                <div class="card-tools">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addNew" @click="openModalWindow">Add New</button>
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
                <table class="table table-hover text-nowrap" id="mytable">
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
import XLSX from 'xlsx'
import Vue from 'vue'
import { Form } from 'vform'
import Swal from 'sweetalert2'

    export default {
        data() {
            return {
                editMode: false,
                books: [],
                form: new Form({
                    id: '',
                    title: '',
                    author: ''
                }),
                searchQuery: '',
                temp: '',
                sortBy: 'title',
                sortDirection: 'descending',
                handlerTitle: '',
                handlerAuthor:''

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
            },
        },


        methods: {

        CsvExport () { // On Click CSV download button

        // export json to Worksheet
        var ws = XLSX.utils.json_to_sheet(this.temp)

        // A workbook is the name given to file
        var wb = XLSX.utils.book_new() // make Workbook

        // add Worksheet to Workbook
        // Workbook contains one or more worksheets
        XLSX.utils.book_append_sheet(wb, ws, 'books') // sheetName is name of Worksheet
        
        // export Excel file
        XLSX.writeFile(wb, 'book.csv') // name of the file is 'book.csv'

        },

        XmlExport () { 

        var ws = XLSX.utils.json_to_sheet(this.temp)

        var wb = XLSX.utils.book_new()

        XLSX.utils.book_append_sheet(wb, ws, 'books')
        
        // export file
        XLSX.writeFile(wb, 'book.xml') // name of the file is 'book.xml'

        },

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
        editModalWindow(book) {
           this.form.clear();
           this.editMode = true
           this.form.reset();
           $('#addNew').modal('show');
           this.form.fill(book)
        },

        updateBook() {
           this.form.put('/api/book/'+this.form.id)
               .then(() => {
                    let Fire = new Vue()
                    window.Fire = Fire;

                   Toast.fire({
                      icon: 'success',
                      title: 'Book updated successfully'
                    })

                    this.loadBooks()

                    Fire.$emit('AfterCreatedBookLoadIt');

                    $('#addNew').modal('hide');
               })
               .catch(() => {
                  console.log("Error.....")
               })
        },

        openModalWindow() {
           this.editMode = false
           this.form.reset();
           $('#addNew').modal('show');
        },

        // Load initial instance of books
        loadBooks() {
            axios.get("/api/book").then(data => (this.books = this.temp = data.data));
        },

        // Load titles logic
        loadTitles() {
            if (this.handlerTitle == true) {
                axios.get("/api/title").then(data => (this.books = this.temp = data.data)) // Load titles
            } if (this.handlerAuthor == true) {
                if (this.handlerTitle == false) {
                    axios.get("/api/author").then(data => (this.books = this.temp = data.data)) // Load authors when uncheck title
                }
            } if (this.handlerTitle == false) {
                if (this.handlerAuthor == true) {
                    axios.get("/api/author").then(data => (this.books = this.temp = data.data)) // Load authors when uncheck title
                }
            } if (this.handlerTitle == true && this.handlerAuthor == true) {
                axios.get("/api/book").then(data => (this.books = this.temp = data.data)) // Load titles and authors when both checked
            } if (this.handlerTitle == false && this.handlerAuthor == false) {
                axios.get("/api/book").then(data => (this.books = this.temp = data.data)) // Load titles and authors when both unchecked
            }
        },

        // Load authors logic
        loadAuthors() {
            if (this.handlerAuthor == true) {
                axios.get("/api/author").then(data => (this.books = this.temp = data.data)) // Load authors
            } if (this.handlerTitle == true) {
                if (this.handlerAuthor == false) {
                    axios.get("/api/title").then(data => (this.books = this.temp = data.data)) // Load titles when uncheck author
                }
            } if (this.handlerAuthor == false) {
                if(this.handlerTitle == true) {
                axios.get("/api/title").then(data => (this.books = this.temp = data.data)) // Load titles when uncheck author
                }
            } if (this.handlerTitle == true && this.handlerAuthor == true) {
                axios.get("/api/book").then(data => (this.books = this.temp = data.data)) // Load titles and authors when both checked
            } if (this.handlerTitle == false && this.handlerAuthor == false) {
                axios.get("/api/book").then(data => (this.books = this.temp = data.data)) // Load titles and authors when both unchecked
            }
        },

        // Add New Book
        createBook() {
            this.$Progress.start()
            this.form.post('/api/book')
                .then(() => {
                    let Fire = new Vue()
                    window.Fire = Fire;

                    this.loadBooks()

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
                this.form.delete('/api/book/'+id)
                    .then((response) => {
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
            let Fire = new Vue()
            window.Fire = Fire;
            Fire.$on('AfterCreatedBookLoadIt', () => { //custom events fire on
                this.loadBooks();
            });
        },
    }
</script> 