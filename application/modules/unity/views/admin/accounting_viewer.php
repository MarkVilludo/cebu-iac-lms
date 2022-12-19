<?php $paid = 0; ?>
<aside class="right-side">
    <div id="vue-container">
    <section class="content-header">
        <h1>
            <small>
                <a class="btn btn-app" href="<?php echo base_url() ?>student/view_all_students" ><i class="ion ion-arrow-left-a"></i>All Students</a> 
                                <a class="btn btn-app trash-student-record2" rel="<?php echo $student['intID']; ?>" href="#"><i class="ion ion-android-close"></i> Delete</a>   
                                <a class="btn btn-app" href="<?php echo base_url()."student/edit_student/".$student['intID']; ?>"><i class="ion ion-edit"></i> Edit</a> 
                                
                                
            </small>
        </h1>


    </section>
        <hr />
    <div class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-red">
                <!-- /.widget-user-image -->
                <h3 class="widget-user-username" style="text-transform:capitalize;margin-left:0;font-size:1.3em;"><?php echo strtolower($student['strLastname'].", ". $student['strFirstname']); ?>
                            <?php echo ($student['strMiddlename'] != "")?' '.strtolower($student['strMiddlename']):''; ?></h3>
                <h5 class="widget-user-desc" style="margin-left:0;"><?php echo $student['strProgramCode']." Major in ".$student['strMajor']; ?></h5>
                </div>
                <div class="box-footer no-padding">
                <ul class="nav nav-stacked">
                    <li><a href="#" style="font-size:13px;">Student Number <span class="pull-right text-blue"><?php echo $student['strStudentNumber']; ?></span></a></li>                  
                </ul>
                </div>
            </div>
                
            </div>
            
        
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                <!-- <li><a href="<?php echo base_url(); ?>unity/student_viewer/<?php echo $student['intID']; ?>/<?php echo $selected_ay; ?>/tab_1">Personal Information</a></li>
                <?php if(in_array($user['intUserLevel'],array(2,4)) ): ?>
                    <li><a href="<?php echo base_url(); ?>unity/student_viewer/<?php echo $student['intID']; ?>/<?php echo $selected_ay; ?>/tab_2">Report of Grades</a></li>
                <li><a href="<?php echo base_url(); ?>unity/student_viewer/<?php echo $student['intID']; ?>/<?php echo $selected_ay; ?>/tab_3">Assessment</a></li>
                <?php endif; ?>
                    <?php if($active_registration && in_array($user['intUserLevel'],array(2,3,4,6))): ?>
                <li><a href="<?php echo base_url(); ?>unity/student_viewer/<?php echo $student['intID']; ?>/<?php echo $selected_ay; ?>/tab_5">Schedule</a></li>
                <li><a href="<?php echo base_url()."unity/registration_viewer/".$student['intID']."/".$selected_ay; ?>">Statement of Account</a></li>
                <?php endif; ?> -->
                <li class="active"><a href="#tab_1" data-toggle="tab">Accounting Summary</a></li>
                </ul>
                <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
        
            <div class="box box-solid box-success">
                <div class="box-header">                            
                    <h4 class="box-title">Transactions</h4>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>OR Number</th>
                            <th>Payment Type</th>
                            <th>Amount Paid</th>
                            <th>Online Payment Charge</th>
                            <th>Total Due</th>
                            <th>Status</th>
                            <th>Online Response Message</th>
                            <th>Date Paid</th>
                        </tr>     
                        <tr>
                            <td>{{ reservation_payment.or_number }}</td>
                            <td>{{ reservation_payment.description }}</td>
                            <td>{{ reservation_payment.subtotal_order }}</td>
                            <td>{{ reservation_payment.charges }}</td>
                            <td>{{ reservation_payment.total_amount_due }}</td>
                            <td>{{ reservation_payment.status }}</td>
                            <td>{{ reservation_payment.response_message }}</td>
                            <td>{{ reservation_payment.updated_at }}</td>
                        </tr>           
                        <tr>
                            <td colspan="3">
                            Total Tuition: P{{ total_formatted }}
                            </td>
                        </tr>
                        <tr>
                            <!-- <td style="<?php echo ($remaining_balance!=0)?'background:#c55;color:#fff;':''; ?>" colspan="3">
                            remaining balance: <?php echo $remaining_balance; ?>php
                            </td> -->
                        </tr>
                    </table>

                    <hr />
                    
                </div>
            </div>    
        </div>
        
        </div>
    </div>
</aside>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/themes/default/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"
    integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

<script>
new Vue({
    el: '#vue-container',
    data: {
        request: {
            
        },        
        tuition: {},
        total_formatted: 0,
        sy: {},
        reservation_payment: {},
        selected_ay: undefined,
        student: {},
        loader_spinner: true,
        type: "",
        slug: "<?php echo $student['slug']; ?>",
        update_status: "",
        status_remarks: "",
    },

    mounted() {

        let url_string = window.location.href;
        let url = new URL(url_string);

        this.loader_spinner = true;

        axios.get('<?php echo base_url(); ?>unity/accounting_viewer_data/<?php echo $id."/".$sem; ?>')
        .then((data) => {
            this.tuition = data.data.data;                
            this.loader_spinner = false;
            if(this.tuition.tuition)
                this.total_formatted = this.tuition.tuition.total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            axios.get(api_url + 'finance/transactions/' + this.slug + '/' + this.tuition.selected_ay)
            .then((data) => {
                this.request = data.data.data;
                this.loader_spinner = false;
            })
            .catch((error) => {
                console.log(error);
            })

            axios.get(api_url + 'finance/reservation/' + this.slug)
            .then((data) => {
                this.reservation_payment = data.data.data;                
                this.loader_spinner = false;
            })
            .catch((error) => {
                console.log(error);
            })
        })
        .catch((error) => {
            console.log(error);
        })



    },

    methods: {

        updateStatus: function() {


            Swal.fire({
                title: 'Update Status',
                text: "Are you sure you want to update?",
                showCancelButton: true,
                confirmButtonText: "Yes",
                imageWidth: 100,
                icon: "question",
                cancelButtonText: "No, cancel!",
                showCloseButton: true,
                showLoaderOnConfirm: true,
                preConfirm: (login) => {

                    return axios
                        .post(api_url + 'admissions/student-info/' + this.slug +
                            '/update-status', {
                                status: this.update_status,
                                remarks: this.status_remarks,
                                admissions_officer: "<?php echo $user['strFirstname'] . '  ' . $user['strLastname'] ; ?>"
                            }, {
                                headers: {
                                    Authorization: `Bearer ${window.token}`
                                }
                            })
                        .then(data => {
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