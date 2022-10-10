<aside class="right-side">
<section class="content-header">
                    <h1>
                        Student
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Student</a></li>
                        <li class="active">Add Student</li>
                    </ol>
                </section>
<div class="content">
    <div class="span10 box box-primary">
        <div class="box-header">
                <h3 class="box-title">New Student</h3>
        </div>
       
            
            <form id="validate-student" action="<?php echo base_url(); ?>student/submit_student" method="post" role="form" enctype="multipart/form-data">
                 <div class="box-body">
                     <div class="row">
                     
                    <div class="form-group col-xs-4">
                        <label for="strLastname">Last Name*</label>
                        <input type="text" name="strLastname" class="form-control" id="strLastname" placeholder="Enter Last Name">
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="strFirstname">First Name*</label>
                        <input type="text" name="strFirstname" class="form-control" id="strFirstname" placeholder="Enter First Name">
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="strMiddlename">Middle Name</label>
                        <input type="text" name="strMiddlename" class="form-control" id="strMiddlename" placeholder="Enter Middle Name">
                    </div>
                      <div class="form-group col-xs-4">
                        <label for="enumGender">Gender: </label>
                        <select class="form-control" name="enumGender" >
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    </div>
                 
                <input type="hidden" value="<?php echo date("Y-m-d H:i:s"); ?>" name="dteCreated" />
                <input type="hidden" value="new" id="stype" />
                <div class="form-group col-xs-4">
                        <label for="strStudentNumber">Student Number</label>
                        <div class="input-group">
                          <input type="text" name="strStudentNumber" id="strStudentNumber" class="form-control" id="strStudentNumber" placeholder="Enter Student Number">
                          <span class="input-group-btn">
                            <button type="button" id="generate-stud-num" rel="<?php echo $yearStart; ?>" class="btn btn-danger btn-flat">Generate</button>
                          </span>
                        </div>
                    
                    
                    </div>
                <div class="form-group col-xs-4">
                         <label for="strLRN">Learner Reference Number (LRN)</label>
                        <input type="text" name="strLRN" class="form-control" id="strLRN" placeholder="Enter Learner Reference Number">                 </div>
                <div class="form-group col-xs-6">
                        <label for="strEmail">Email</label>
                        <input type="email" name="strEmail" class="form-control" id="strEmail" placeholder="Enter Email Address">
                    </div>
                     <div class="form-group col-xs-6">
                        <label for="dteBirthDate">Birthday</label>
                        <div class="input-group date">                      
                         <input type="text" name="dteBirthDate" value=""  class="form-control validate" id="dteBirthDate" placeholder="Enter Birthday">
                         <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>    
                <div class="form-group col-xs-6">
                        <label for="strMobileNumber">Contact Number</label>
                        <input type="number" name="strMobileNumber" class="form-control" id="strMobileNumber" placeholder="Enter Contact Number">
                    </div>
                <div class="form-group col-xs-6">
                        <label>Address</label>
                        <textarea class="form-control" name="strAddress" rows="3" placeholder="Enter Address"></textarea>
                    </div>
                    <div class="form-group col-xs-6">
                        <label for="strZipCode">Zip Code</label>
                        <input type="number" name="strZipCode" class="form-control" id="strZipCode" placeholder="Enter Zip Code">
                    </div>
                <!--div class="form-group col-xs-6">
                    <label for="strSection">Section</label>
                    <select class="form-control" name="strSection" > 
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                        <option value="K">K</option>
                        <option value="K">N</option>
                    </select>
                </div-->         
                <div class="form-group col-xs-6">
                    <label for="intProgramID">Course</label>
                    <select class="form-control select2" name="intProgramID" id="addStudentCourse" >
                        <option value="0">--Select--</option>
                        <?php foreach ($programs as $prog): ?>
                        <option value="<?php echo $prog['intProgramID']; ?>"><?php echo $prog['strProgramCode']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                </div>
                <div class="form-group col-xs-6">
                    <label for="">Curriculum</label>
                    <select class="form-control" name="intCurriculumID" id="intCurriculumID" >
                        <?php foreach ($curriculum as $curr): ?>
                        <option value="<?php echo $curr['intID']; ?>"><?php echo $curr['strName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                </div>
                
                    <div class="form-group col-xs-12">
                        <label for="srtPicture">Upload Picture</label>
                                <input type="file" name="strPicture" value=" <?php echo $img_dir?>default_image2.png" />
                    </div>
                    <div class="form-group col-xs-6">
                        <input type="submit" value="Submit" class="btn btn-lg btn-default  btn-flat">
                    </div>
                </div>
                
                
               
                </div>                 
            </form>
       
        </div>
</aside>