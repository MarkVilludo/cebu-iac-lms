<div class="content-wrapper " id="applicant-container">
    <section class="content-header container ">
        <h1>
            Student Applicants
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Student Applicant Details </a></li>
            <li class="active">Details</li>
        </ol>
    </section>
    <div class="content  container">
        <div action="">
            <div class="box ">
                <div class="box-header with-border font-weight-bold py-5" style="text-align:left; font-weight:bold">
                    <h3 class="box-title text-left text-primary " style="font-size:2rem">
                        Add Tuition Year
                    </h3>
                </div>

                <div class="box-body" style="padding:2rem">
                <form @submit.prevent="updateData">    
                    <div class="row">                     
                        <div class="form-group col-xs-6">
                            <label for="year">Tuition Year</label>
                            <input type="text" name="year" class="form-control" id="year" placeholder="Enter Year" v-model='request.tuitionyear'>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="year">Price Per Unit</label>
                            <input type="number" name="pricePerUnit" class="form-control" id="pricePerUnit" placeholder="Enter Price per unit" v-model='request.pricePerUnit'>
                        </div>                        
                     </div>
                    
                    <div class=row">    
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>




<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/themes/default/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"
    integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

<script>
new Vue({
    el: '#applicant-container',
    data: {
        id: <?php echo $this->uri->segment('3'); ?>,
        request: {
            tuitionyear: undefined,
            pricePerUnit: undefined,
            isDefault: 0,            
        },
        update_text: "Tuition Year",
        loader_spinner: true,                        
    },

    mounted() {

        let url_string = window.location.href;
        let url = new URL(url_string);



        // this.loader_spinner = true;
        // axios.get(api_url + 'admissions/student-info/' + this.slug)
        //     .then((data) => {
        //         this.request = data.data.data;
        //         this.loader_spinner = false;
        //     })
        //     .catch((error) => {
        //         console.log(error);
        //     })



    },

    methods: {

        updateData: function() {


            Swal.fire({
                title: 'Update Status',
                text: "Continue adding entry?",
                showCancelButton: true,
                confirmButtonText: "Yes",
                imageWidth: 100,
                icon: "question",
                cancelButtonText: "No, cancel!",
                showCloseButton: true,
                showLoaderOnConfirm: true,
                preConfirm: (login) => {

                    return axios
                        .post('<?php echo base_url(); ?>tuitionyear/submit_form/' + this.id, {
                                year: this.request.tuitionyear,
                                pricePerUnit: this.request.pricePerUnit,
                                isDefault: this.request.isDefault,                                
                            }, {
                                headers: {
                                    Authorization: `Bearer ${window.token}`
                                }
                            })
                        .then(data => {
                            console.log(data.data);
                            if (data.data.success) {
                                Swal.fire({
                                    title: "Success",
                                    text: data.data.message,
                                    icon: "success"
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    data.data.message,
                                    'error'
                                )
                            }
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                // if (result.isConfirmed) {
                //     Swal.fire({
                //         icon: result?.value.data.success ? "success" : "error",
                //         html: result?.value.data.message,
                //         allowOutsideClick: false,
                //     }).then(() => {
                //         if (reload && result?.value.data.success) {
                //             if (reload == "reload") {
                //                 location.reload();
                //             } else {
                //                 window.location.href = reload;
                //             }
                //         }
                //     });
                // }
            })
        }


    }

})
</script>