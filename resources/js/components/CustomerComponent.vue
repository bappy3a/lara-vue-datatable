<template>
    <div id="customer">
      <div class="row justify-content-center">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Customers</h4>
                    <div class="card-tools" style="position: absolute;right: 1rem;top: .5rem;">
                      <button type="button" class="btn btn-success" @click="create">Ad New <i class="fas fa-plus"></i></button>
                      <button type="button" class="btn btn-primary" @click="reload">Reload <i class="fas fa-sync"></i></button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <div class="row">
                        <div class="col-md-2">
                          <strong>Search By:</strong>
                        </div>
                        <div class="col-md-3">
                          <select v-model="queryFiled" class="form-control" id="fileds">
                            <option value="name">Name</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="addres">Address</option>
                            <option value="tobal">Total</option>
                          </select>
                        </div>
                        <div class="col-md-7">
                          <input v-model="query" type="text" class="form-control" placeholder="Search">
                        </div>
                      </div>
                    </div>
                      <div class="table-responsive-sm">
                          <table class="table table-hover table-bordered table-striped">
                              <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <!-- <th scope="col">Address</th> -->
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-center">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr v-for="(customer,index) in customer" :key="customer.id">
                                    <th scope="row">{{ index +1 }}</th>
                                    <th>{{ customer.name }}</th>
                                    <th>{{ customer.email }}</th>
                                    <th>{{ customer.phone }}</th>
                                    <!-- <th>{{ customer.addres }}</th> -->
                                    <th>{{ customer.tobal }}</th>
                                    <th class="text-center" >
                                      <button @click="view(customer)" type="button" class="btn btn-info btn-sm">
                                        <i class="far fa-eye"></i>
                                      </button>
                                      <button @click="edit(customer)" type="button" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                      </button>
                                      <button @click="distry(customer)" type="button" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                      </button>
                                    </th>
                                  </tr>
                                  <tr v-show="!customer.length">
                                    <th colspan="6">
                                      <div class="alert alert-danger" role="alert">
                                        Sorry :( No Data Found.)
                                      </div>
                                    </th>
                                  </tr>
                              </tbody>
                          </table>
                           <pagination
                              v-if="pagination.last_page > 1"
                              :pagination="pagination"
                              :offset="5"
                              @paginate="query === '' ? getData() : searchData()"
                            ></pagination>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- Modal Add Customer -->
      <div class="modal fade" id="customerModalLong" tabindex="-1" role="dialog" aria-labelledby="customerModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="customerModalLongTitle">{{ editMode ? "Edite Customer" : "Add New Customer" }} </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="editMode ? update() : store() " @keydown="form.onKeydown($event)">
              <alert-error :form="form"></alert-error>
              <div class="modal-body">
                  <div class="form-group">
                    <label>Name</label>
                    <input v-model="form.name" type="text" name="name"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input v-model="form.email" type="text" name="email"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                    <has-error :form="form" field="email"></has-error>
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <input v-model="form.phone" type="text" name="phone"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
                    <has-error :form="form" field="phone"></has-error>
                  </div>

                  <div class="form-group">
                    <label>Address</label>
                    <textarea v-model="form.addres" type="text" name="addres"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('addres') }"></textarea>
                    <has-error :form="form" field="addres"></has-error>
                  </div>
                  <div class="form-group">
                    <label>Total</label>
                    <input v-model="form.tobal" type="text" name="tobal"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('tobal') }">
                    <has-error :form="form" field="tobal"></has-error>
                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button :disabled="form.busy" type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
        </div>
        <!-- Modal Add Customer End-->
      </div>
      <vue-progress-bar></vue-progress-bar>
      <vue-snotify></vue-snotify>
    </div>
</template>

<script>
    export default {
      data() {
          return{
            editMode : false,
            query: '',
            queryFiled:'name',
            customer:[],
            form :new Form({
              id: '',
              name: '',
              email: '',
              phone: '',
              addres: '',
              tobal: '',
            }),
            pagination:{
              current_page: 1,
            }
          }
      },
      watch: {
        query:function(newQ,Old){
          if (newQ === '') {
            this.getData()
          } else{
            this.searchData()
          }
        }
      },

      mounted() {
          this.getData();
      },
      methods: {
        getData(){
          this.$Progress.start();
          axios.get("/api/customer?page=" + this.pagination.current_page)
                .then(response => {
                  this.customer = response.data.data;
                  this.pagination = response.data.meta;
                  this.$Progress.finish();
                })
                .catch(e => {
                  console.log(e);
                  this.$Progress.fail();
                })
        },
        searchData(){
          this.$Progress.start();
          axios.get('api/search/customers/'+this.queryFiled+'/'+this.query+'?page='+this.pagination.current_page)
          .then(response => {
                  this.customer = response.data.data;
                  this.pagination = response.data.meta;
                  this.$Progress.finish();
                })
          .catch(e => {
                  console.log(e);
                  this.$Progress.fail();
                })
        },
        reload(){
          this.getData();
          this.query = '';
          this.queryFiled = 'name';
          this.$snotify.success('Data Successfully Refresh','success')
        },
        create(){
          this.editMode = false
          this.form.reset()
          this.form.clear()
          $('#customerModalLong').modal('show');
        },
        store(){
          this.$Progress.start();
          this.form.busy = true;
          this.form.post('/api/customer')
            .then(response => {
              this.getData();
              $('#customerModalLong').modal('hide');
              if (this.form.successful) {
                this.$Progress.finish();
                this.$snotify.success('Data Successfully Save','success')
              }else{
                this.$Progress.fail()
                this.$snotify.error('Sumthing went Worong try again later.','Error')
              }
            })
            .catch(e => {
              console.log(e);
              this.$Progress.fail();
            })
        },
        edit(customer){
          this.editMode = true;
          this.form.reset()
          this.form.clear()
          this.form.fill(customer)
          $('#customerModalLong').modal('show');
        },
        update(){
          this.$Progress.start();
          this.form.busy = true;
          this.form.put('/api/customer/'+ this.form.id)
            .then(response => {
              this.getData();
              $('#customerModalLong').modal('hide');
              if (this.form.successful) {
                this.$Progress.finish();
                this.$snotify.success('Data Successfully Update','success')
              }else{
                this.$Progress.fail()
                this.$snotify.error('Sumthing went Worong try again later.','Error')
              }
            })
            .catch(e => {
              console.log(e);
              this.$Progress.fail();
            })
        },
        distry(customer){
          this.$snotify.confirm(
            "You will not be able to recover this data!",
            "Are you sure?",
            {
              showProgressBar: false,
              closeOnClick: false,
              pauseOnHover: true,
              buttons: [
                {
                  text: "Yes",
                  action: toast => {
                    this.$snotify.remove(toast.id);
                    this.$Progress.start();
                    axios
                      .delete("/api/customer/" + customer.id)
                      .then(response => {
                        this.getData();
                        this.$Progress.finish();
                        this.$snotify.success(
                          "Customer Successfully Deleted",
                          "Success"
                        );
                      })
                      .catch(e => {
                        this.$Progress.fail();
                        console.log(e);
                      });
                  },
                  bold: true
                },
                {
                  text: "No",
                  action: toast => {
                    this.$snotify.remove(toast.id);
                  },
                  bold: true
                }
              ]
            }
          );
        }


      }
    }
</script>
