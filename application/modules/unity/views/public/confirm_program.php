<div id="registration-container">    
    <div class="container">       
        <div class="content">                        
            <div class="box">
                <div class="box-header">
                    <h3>Name :{{ student.strFirstname }} {{ student.strLastname }} <br />
                        Stud No :{{ student.strStudentNumber }}
                    </h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Selected Program</th>
                                <td>{{ student.strProgramDescription }}</td>
                                <td>
                                    <button v-if="!show_select" class="btn btn-secondary" @click="updateProgram">Change Selected Program</button>
                                    <select v-else v-model="request.intProgramID" class="form-control">
                                        <option v-for="program in programs" :value="program.intProgramID">{{ program.strProgramDescription }}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr />
                    <div class="text-center">
                        <button class="btn btn-primary" @click="confirmProgram">Confirm Selected Program</button>                        
                    </div>
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

<style scoped="">
.box_mode_payment {
    border: 1px solid #000;
    height: 41px;
    width: 57px;
    margin: 4px;
    cursor: pointer;
}

.box_mode_payment.active {
    background: #1c54a5;
}

.spinner {
    animation-name: spin;
    animation-duration: 1000ms;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
<script>
new Vue({
    el: '#registration-container',
    data: {
        id: '<?php echo $id; ?>',   
        base_url: '<?php echo base_url(); ?>',            
        student: {},
        programs: [],
        request: {
            intProgramID: undefined,
        },
        payload:{

        },
        show_select: false,
    },    
    mounted() {

        let url_string = window.location.href;                
        axios.get(this.base_url + 'unity/program_confirmation_data/' + this.id + '/')
                .then((data) => {  
                    this.student = data.data.student;     
                    this.request.intProgramID = this.student.intProgramID;         
                    this.programs = data.data.programs;                                 
                })
                .catch((error) => {
                    console.log(error);
                })

        

    },

    methods: {  
        updateProgram: function(){
            this.show_select =  true;
        },
        confirmProgram: function(){
            this.loading_spinner = true;
            Swal.fire({
                showCancelButton: false,
                showCloseButton: false,
                allowEscapeKey: false,
                title: 'Loading',
                text: 'Processing Payment',
                icon: 'info',
            })
            Swal.showLoading();

            axios
                .post(api_url + 'registrar/confirm_selected_program/' + this.student.slug , this.payload, {
                    headers: {
                        Authorization: `Bearer ${window.token}`
                    }
                })
                .then(data => {     
                    var formdata= new FormData();
                    for (const [key, value] of Object.entries(this.request)) {
                        formdata.append(key,value);
                    }                                                    
                    axios
                    .post(this.base_url + 'unity/student_confirm_program', formdata, {
                        headers: {
                            Authorization: `Bearer ${window.token}`
                        }
                    })
                    .then(data => {
                        Swal.hideLoading();
                        location.reload();
                    });               
                    
                });
            
        }
    }

})
</script>

