
<aside class="right-side">
    <div id="student-viewer-container">
        <section class="content-header">
            <h1>
                <small>
                    <a class="btn btn-app" :href="base_url + 'student/view_all_students'" ><i class="ion ion-arrow-left-a"></i>All Students</a>                     
                    <a class="btn btn-app" :href="base_url + 'student/edit_student/' + student.intID"><i class="ion ion-edit"></i> Edit</a>                     
                    <a class="btn btn-app" target="_blank" :href="base_url + 'pdf/print_curriculum/' + student.intCurriculumID + '/' + student.intID"><i class="fa fa-print"></i>Curriculum Outline</a> 
                    <a target="_blank" v-if="registration" class="btn btn-app" :href="base_url + 'pdf/student_viewer_registration_print/' + student.intID +'/'+ active_sem.intID">
                        <i class="ion ion-printer"></i>Reg Form Print Preview
                    </a>                     
                    <a v-if="reg_status != 'For Advising'" target="_blank" class="btn btn-app" href="base_url + 'pdf/student_viewer_advising_print/' + student.intID + '/' + active_sem.intID">
                        <i class="ion ion-printer"></i>Print Advising Form
                    </a> 
                    <a v-else target="_blank" class="btn btn-app" href="base_url + 'department/advising/' + student.intID">
                        <i class="fa fa-book"></i>Advising/Subject Loading</a> 
                    </a>
                    <a v-if="!registration && reg_status!='For Advising'" class="btn btn-app" href="base_url + 'unity/edit_sections/' + student.intID + '/' + active_sem.intID">
                        <i class="fa fa-book"></i> Update Sections
                    </a>                         
                    <a v-if="!registration && reg_status!='For Advising'" class="btn btn-app" href="base_url + 'registrar/register_old_student2/' + student.intID">
                        <i class="fa fa-book"></i>Register Student
                    </a>                                         
                </small>
                
                <div class="box-tools pull-right">
                    <select v-model="sem_student" @change="changeTermSelected" class="form-control" >
                        <option v-for="s in sy" :value="s.intID">{{s.enumSem + ' ' + term_type + ' ' + s.strYearStart + '-' + s.strYearEnd}}</option>                      
                    </select>
                    <div v-if="registration" class="pull-right">
            
                        <label style="font-size:.6em;"> Registration Status</label>
                            
                        <select v-model="registration_status" @change="changeRegStatus" class="form-control">
                            <option value="0">Registered</option>
                            <option value="1">Enrolled</option>
                            <option value="2">Cleared</option>
                        </select>
                        
                    </div>
                </div>
                <div style="clear:both"></div>
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
                            <h3 class="widget-user-username" style="text-transform:capitalize;margin-left:0;font-size:1.3em;">{{ student.strLastname }}, {{ student.strFirstname }} {{ student.strMiddlename }}</h3>
                            <h5 class="widget-user-desc" style="margin-left:0;">{{ student.strProgramCode }} Major in {{ student.strMajor }}</h5>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                            <li><a href="#" style="font-size:13px;">Student Number <span class="pull-right text-blue">{{ student.strStudentNumber }}</span></a></li>
                            <li><a href="#" style="font-size:13px;">Curriculum <span class="pull-right text-blue">{{ student.strName }}</span></a></li>
                            <li><a style="font-size:13px;" href="#">Registration Status <span class="pull-right">{{ reg_status }}</span></a></li>
                            <li><a :href="base_url + 'unity/delete_registration/' + student.intID + '/' + active_sem.intID"><i class="ion ion-android-close"></i> Reset Status</a> </li>
                            <li>
                                <a style="font-size:13px;" href="#">Date Registered <span class="pull-right">
                                    <span style="color:#009000" v-if="registration" >{{ registration.dteRegistered }}</span>
                                    <span style="color:#900000;" v-else>N/A</span>                                
                                </a>
                            </li>                                                
                            <li><a style="font-size:13px;" href="#">Scholarship Type <span class="pull-right">{{ registration.scholarshipName }}</span></a></li>
                                
                            </ul>
                        </div>
                    </div>                
                </div>                                            
                <div class="col-sm-12">
                    <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li :class="[(tab == 'tab_1') ? 'active' : '']"><a href="#tab_1" data-toggle="tab">Personal Information</a></li>
                        <li v-if="advanced_privilages1" :class="[(tab == 'tab_2') ? 'active' : '']"><a href="#tab_2" data-toggle="tab">Report of Grades</a></li>
                        <li v-if="advanced_privilages2" :class="[(tab == 'tab_3') ? 'active' : '']"><a href="#tab_3" data-toggle="tab">Assessment</a></li>                                        
                        <li v-if="registration && advanced_privilages2" :class="[(tab == 'tab_5') ? 'active' : '']"><a href="#tab_5" data-toggle="tab">Schedule</a></li>
                        <li v-if="registration && advanced_privilages2"><a :href="base_url + 'unity/registration_viewer/' + student.intID + '/' + selected_ay">Statement of Account</a></li>
                        <li v-if="registration && advanced_privilages2"><a :href="base_url + 'unity/edit_registration/' + student.intID + '/' + selected_ay">Edit Registration</a></li>
                        <li><a :href="base_url + 'unity/accounting/' + student.intID">Accounting Summary</a></li>                    
                    </ul>
                    <div class="tab-content">
                        <div :class="[(tab == 'tab_1') ? 'active' : '']" class="tab-pane" id="tab_1">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-3 size-96">                                    
                                            <img v-if="!student.strPicture" :src="img_dir + 'default_image2.png'" class="img-responsive"/>
                                            <img v-else class="img-responsive" :src="photo_dir + student.strPicture" />                                    
                                        </div>
                                        <div class="col-sm-9">
                                            <p><strong>Student Number: </strong>{{ student.strStudentNumber }}</p>
                                            <!-- <p><strong>Learner Reference Number(LRN): </strong>{{ student.strLRN'] }}</p> -->
                                            <p><strong>Address: </strong>{{ student.strAddress }}</p>
                                            <p><strong>Contact: </strong>{{ student.strMobileNumber }}</p>
                                            <!-- <p><strong>Institutional Email: </strong>{{ student.strGSuiteEmail' }}</p>   -->
                                            <p><strong>Personal Email: </strong>{{ student.strEmail }}</p>  
                                            <p><strong>Birthdate: </strong>{{ student.dteBirthDate }}</p>  
                                            <p><strong>Date Created: </strong>{{ student.dteCreated }}</p>                                                
                                            <hr />
                                            <strong>Graduated Status:</strong>
                                            
                                            <select v-model="grad_status" v-if="registrar_privilages" class="form-control" @change="updateGradStatus">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                            <hr />                                            
                                            <div v-else>
                                                {{ student.isGraduate ? 'Grad' : 'Not Grad' }}
                                            </div>
                                        </div>                                                                        
                                    </div>    
                                </div>
                            </div>
                        </div>
                    <!-- /.tab-pane -->
                    <?php if(in_array($user['intUserLevel'],array(2,4,3)) ): ?>
                        <div v-if="advanced_privilages1" :class="[(tab == 'tab_2') ? 'active' : '']" class="tab-pane" id="tab_2">
                            <div class="box box-primary">
                                <div class="box-body">                                    
                                    <div v-if="active_sem.enumFinalized == 'no' && registration && sections.length > 0" class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <select v-model="add_subject.subject" class="select2" id="subjectSv" name="subjectSv">
                                                    <option v-for="s in curriculum_subjects" :value="s.intSubjectID">{{ s.strCode + ' ' + s.strDescription }}</option>                                                                          
                                                </select>
                                                <a href="base_url + 'subject/subject_viewer/' + curriculum_subjects[0].intSubjectID" id="viewSchedules" target="_blank" class='btn btn-default input-group-addon  btn-flat'>View Schedules</a>
                                            </div>                                                        
                                        </div>
                                        <div class="col-sm-4">
                                            <select v-model="add_subject.section" class="form-control" id="sections-to-add">
                                                <option v-for="sc in sections" :value="sc.intID">{{ sc.strSection }}</option>                                                
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <a href="#" @click="submitSubject" class='btn btn-default  btn-flat'>Add Subject <i class='fa fa-plus'></i></a>
                                        </div>
                                    </div>
                                    <hr />                                    
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Section Code</th>
                                                <th>Course Code</th>
                                                <th>Units</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                                <th>Faculty</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                          
                                            <tr v-for="record in records" style="font-size: 13px;">
                                                <td>{{ record.strSection }}</td>
                                                <td>{{ record.strCode }}</td>
                                                <td>{{ record.strUnits }}</td>
                                                <td>{{ record.v3Display }}</td>
                                                <td>{{ record.strRemarks }}</td>
                                                <td>{{ record.facultyName }}</td>
                                                <td>{{ record.recStatus }}</td>
                                                <td>                                                    
                                                    <a v-if="record.intFinalized < 2" href="#"  @click.prevent.stop="removeFromClasslist(record.intCSID)">Remove</a><br />
                                                    <a v-if="record.intFinalized < 2" :href="base_url + 'unity/classlist_viewer/' + record.classlistID">View Classlist</a>                                                    
                                                    <a v-else :href="base_url + 'unity/classlist_viewer/' + record.classlistID">View Classlist</a>                               
                                                </td>
                                            </tr>
                                            <tr style="font-size: 13px;">
                                                <td></td>
                                                <td align="right"><strong>TOTAL UNITS CREDITED:</strong></td>
                                                <td>{{ total_units }}</td>
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr style="font-size: 11px;">
                                                <td></td>
                                                <td align="right"><strong>GPA:</strong></td>
                                                <td>{{ gpa }}</td>
                                                <td colspan="3"></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                    <hr />
                                    <a target="_blank" class="btn btn-default  btn-flat" href="<?php echo base_url()."pdf/student_viewer_rog_print/".$student['intID'] ."/". $active_sem['intID']; ?>">
                                        <i class="ion ion-printer"></i> Print Preview</a> 
                                    <a target="_blank" class="btn btn-default  btn-flat" href="<?php echo base_url()."pdf/student_viewer_rog_data_print/".$student['intID'] ."/". $active_sem['intID']; ?>">
                                        <i class="ion ion-printer"></i> Print Data Preview</a> 
                                        
                                    
                                </div>
                            </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane <?php echo ($tab == "tab_3")?'active':'' ?>" id="tab_3">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Academic Standing</th>
                                            <th>CGPA</th>
                                            <th>Units Earned</th>
                                            <th>Total Units</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo switch_num($academic_standing['year']); ?> Year / <?php echo $academic_standing['status']; ?></td>
                                            <td><?php echo $gpa_curriculum; ?></td>
                                            <td><?php echo $totalUnitsEarned; ?></td>
                                            <td><?php echo $units_in_curriculum; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php 
                                    $prev_year_sem = '0';
                                    $sgpa = 0;
                                    $scount = 0;
                                    $countBridg = 0;
                                    for($i = 0;$i<count($grades); $i++): 
                                    //echo $prev_year_sem."<br />";
                                
                                    if($grades[$i]['floatFinalGrade']!="0" && $grades[$i]['floatFinalGrade']!="3.5")
                                    {    
                                        //print_r($grades[$i]['intBridging']);
                                        
                                        if ($grades[$i]['intBridging'] == 1) { 
                                            $countBridg  = $countBridg + $grades[$i]['intBridging'];
                                            $scount += $grades[$i]['strUnits'];
                                            $scount-=3;
                                        }
                                        else {
                                            
                                            $sgpa += $grades[$i]['floatFinalGrade']*$grades[$i]['strUnits'];
                                            $scount+=$grades[$i]['strUnits'];
                                        
                                        }
                                    //print_r($grades[$i]['intBridging']);
                                    //echo "<br />" . $countBridg;
                                    }

                                    ?>
                                    <?php if($prev_year_sem != $grades[$i]['syID']): 
                                        $countBridg = 0;
                                    ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="4">
                                                    <?php echo ($grades[$i]['syID'] != 0)?$grades[$i]['enumSem']." Sem A.Y. ".$grades[$i]['strYearStart']." - ".$grades[$i]['strYearEnd']:'Credited Units'; ?>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Course Code</th>
                                                <th>Course Description</th>
                                                <th>P</th>
                                                <th>M</th>
                                                <th>F</th>
                                                <th>FG</th>
                                                <th>Num. Rating</th>
                                                <th>Units</th>
                                                <th>Remarks</th>
                                                <th>Faculty</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                    <?php 
                                            
                                            endif; 
                                            $prev_year_sem = $grades[$i]['syID'];
                                        
                                            ?>
                                    <tr class="<?php echo (strtoupper($grades[$i]['strRemarks'])=='PASSED')?'green-bg':''; ?> <?php echo ($grades[$i]['strRemarks']=='Failed' || $grades[$i]['strRemarks']=='Failed(U.D.)')?'red-bg':''; ?>">
                                        <td><a href="<?php echo base_url()."unity/classlist_viewer/".$grades[$i]['classListID'] ?>"><?php echo $grades[$i]['strCode']; ?></a></td>
                                        <td><?php echo $grades[$i]['strDescription']; ?></td>
                                        <td><?php echo $grades[$i]['floatPrelimGrade']; ?></td>
                                        <td><?php echo $grades[$i]['floatMidtermGrade']; ?></td>
                                        <td><?php echo $grades[$i]['floatFinalsGrade']; ?></td>
                                        
                                        <td>
                                        <?php  echo number_format(getAve($grades[$i]['floatPrelimGrade'],$grades[$i]['floatMidtermGrade'],$grades[$i]['floatFinalsGrade']), 2); ?>
                                        </td>
                                        <td>
                                        <?php echo number_format($grades[$i]['floatFinalGrade'], 2, '.' ,','); ?>
                                        </td>
                                        <td>
                                            <?php echo $grades[$i]['strUnits']; ?> 
                                        </td>
                                        <td>
                                            <?php echo $grades[$i]['strRemarks']; ?>
                                        </td>
                                        
                                        <td>
                                        <?php
                                            if($grades[$i]['strFirstname']!="unassigned"){
                                                        $firstNameInitial = substr($grades[$i]['strFirstname'], 0,1);
                                                        echo $firstNameInitial. ". " . $grades[$i]['strLastname'];  
                                                        }
                                                        else
                                                        echo "unassigned";
                                            ?>
                                        </td>
                                    </tr>
                                <?php if($prev_year_sem != $grades[$i+1]['syID'] || count($grades) == $i+1): 
                                    $sgpa_computed = $sgpa/$scount;
                                    $scount_counted = $scount;
                                    $sgpa = 0;
                                    $scount = 0;
                                
                                ?>   
                                    <tr>
                                        <th colspan="4">GPA: <?php echo round($sgpa_computed,2); ?></th>
                                        <th colspan="6">Units: <?php echo $scount_counted; ?></th>
                                    </tr>
                                    <tr>
                                    <?php if($countBridg > 0): ?>
                                    <td colspan="10" style="font-style:italic;font-size:13px;"><small>Note: (<?php echo $countBridg; ?>) Bridging course/s - not computed in units & GPA.</small></td>
                                    <?php endif; ?> 
                                    </tr>
                                    </tbody>
                                </table>
                                <?php 
                                endif; ?>
                                <?php 
                                endfor; ?>

                            </div> 
                        </div>
                            
                    </div>
                    <?php endif; ?>
                    <?php if($registration): ?>
                        <div class="tab-pane <?php echo ($tab == "tab_5")?'active':'' ?>" id="tab_5">
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <table class="table table-condensed table-bordered">
                                            <thead>
                                                <tr style="font-size: 13px;">
                                                    <th>Section</th>
                                                    <th>Course Code</th>
                                                    <th>Course Description</th>
                                                    <th>Units</th>
                                                    <th>Schedule</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $totalUnits = 0;
                                                foreach($records as $record): ?>
                                                <tr style="font-size: 13px;">
                                                    <td><?php echo $record['strSection']; ?></td>
                                                    <td><?php echo $record['strCode']; ?></td>
                                                    <td><?php echo $record['strDescription'] ?></td>
                                                    <td><?php echo ($record['strUnits'] == 0)?'('.$record['intLectHours'].')':$record['strUnits']; ?></td>     
                                                    <?php if(!empty($record['schedule'])): ?>

                                                    <td>
                                                        <?php foreach($record['schedule'] as $sched): ?>

                                                        <?php echo date('g:ia',strtotime($sched['dteStart'])).' - '.date('g:ia',strtotime($sched['dteEnd'])); ?> <?php echo $sched['strDay']; ?> <?php echo $sched['strRoomCode']; ?>
                                                        
                                                        <?php
                                                            $hourdiff = round((strtotime($sched['dteEnd']) - strtotime($sched['dteStart']))/3600, 1);
                                                            
                                                        ?>
                                                        <input type="hidden" class="<?php echo $sched['strDay']; ?>" value="<?php echo date('gia',strtotime($sched['dteStart'])); ?>" href="<?php echo $hourdiff*2; ?>" rel="<?php echo $record['strCode']; ?> <?php echo $sched['strRoomCode']; ?>" data-section="<?php echo $sched['strSection']; ?>">
                                                        <br />
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <?php else: ?>
                                                    <td></td>

                                                    <?php endif; ?>

                                                </tr>

                                                <?php endforeach; ?>




                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                            <div class="box box-primary">
                                    <div class="box-body">
                                    <form method="post" action="<?php echo base_url() ?>pdf/print_sched">   
                                        <input type="hidden" name="sched-table" id="sched-table" />
                                        <input type="hidden" value="<?php echo $student['strLastname']."-".$student['strFirstname']."-".$student['strStudentNumber']; ?>" name="studentInfo" id="studentInfo" />
                                        
                                        <div id="sched-table-container">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center;border:1px solid #555;"></th>
                                                    <th style="text-align:center;border:1px solid #555;">Mon</th>
                                                    <th style="text-align:center;border:1px solid #555;">Tue</th>
                                                    <th style="text-align:center;border:1px solid #555;">Wed</th>
                                                    <th style="text-align:center;border:1px solid #555;">Thu</th>
                                                    <th style="text-align:center;border:1px solid #555;">Fri</th>
                                                    <th style="text-align:center;border:1px solid #555;">Sat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-sched">
                                                <tr id="700am">
                                                <td style="border:1px solid #555;">7:00-7:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="730am">
                                                    <td style="border:1px solid #555;">7:30-8:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="800am">
                                                    <td style="border:1px solid #555;">8:00-8:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="830am">
                                                    <td style="border:1px solid #555;">8:30-9:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="900am">
                                                    <td style="border:1px solid #555;">9:00-9:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="930am">
                                                    <td style="border:1px solid #555;">9:30-10:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="1000am">
                                                    <td style="border:1px solid #555;">10:00-10:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="1030am">
                                                    <td style="border:1px solid #555;">10:30-11:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="1100am">
                                                    <td style="border:1px solid #555;">11:00-11:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            <tr id="1130am">
                                                    <td style="border:1px solid #555;">11:30-12:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="1200pm">
                                                    <td style="border:1px solid #555;">12:00-12:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="1230pm">
                                                    <td style="border:1px solid #555;">12:30-1:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="100pm">
                                                    <td style="border:1px solid #555;">1:00-1:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="130pm">
                                                    <td style="border:1px solid #555;">1:30-2:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="200pm">
                                                    <td style="border:1px solid #555;">2:00-2:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="230pm">
                                                    <td style="border:1px solid #555;">2:30-3:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="300pm">
                                                    <td style="border:1px solid #555;">3:00-3:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="330pm">
                                                    <td style="border:1px solid #555;">3:30-4:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="400pm">
                                                    <td style="border:1px solid #555;">4:00-4:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="430pm">
                                                    <td style="border:1px solid #555;">4:30-5:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="500pm">
                                                    <td style="border:1px solid #555;">5:00-5:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="530pm">
                                                    <td style="border:1px solid #555;">5:30-6:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="600pm">
                                                    <td style="border:1px solid #555;">6:00-6:30</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr id="630pm">
                                                    <td style="border:1px solid #555;">6:30-7:00</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                        <input class="btn btn-flat btn-default" type="submit" value="print preview" />
                                        </form> 
                                    </div>
                                </div>
                        </div>
                    <?php endif; ?>
                    
                    </div>
                    <!-- /.tab-content -->
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

<style>
    .green-bg
    {
        background-color:#77cc77;
    }
    .red-bg
    {
        background-color:#cc7777;
    }
    .select2-container
    {
        display: block !important;
    }
</style>

<script>
new Vue({
    el: '#student-viewer-container',
    data: {
        id: '<?php echo $id; ?>', 
        tab: '<?php echo $tab; ?>',                          
        sem: '<?php echo $sem; ?>',
        student: {},
        registration: {},
        active_sem: {},
        sections: [],
        records: [],
        reg_status: '',
        sy: undefined,
        term_type: undefined,
        sem_student: undefined,
        add_subject:{
            code: undefined,
            section:undefined,
            studentID: undefined,
            activeSem: undefined,
        },        
        advanced_privilages1: false,
        advanced_privilages2: false,
        registrar_privilages: false,
        photo_dir: undefined,
        img_dir: undefined,  
        grad_status: 0,      
        selected_ay: undefined,
        base_url: '<?php echo base_url(); ?>',   
        registration_status: 0,                   
        loader_spinner: true,           
        total_units: 0,
        lab_units: 0,    
        gpa: 0,                 
    },

    mounted() {

        let url_string = window.location.href;        
        if(this.id != 0){            
            //this.loader_spinner = true;
            axios.get(this.base_url + 'unity/student_viewer_data/' + this.id + '/' + this.sem )
                .then((data) => {  
                    if(data.data.success){                                                                                                                   
                        this.student = data.data.student;
                        this.registration = data.data.registration;
                        this.registration_status = data.data.registration.intROG;                        
                        this.active_sem = data.data.active_sem;
                        this.reg_status = data.data.reg_status;
                        this.selected_ay = data.data.selected_ay;
                        this.curriculum_subjects = data.data.curriculum_subjects;                        
                        this.sections = data.data.sections;
                        this.add_subject.section = ( this.sections.length > 0 ) ? this.sections[0].intID : null;
                        this.add_subject.subject = ( this.curriculum_subjects.length > 0 ) ? this.curriculum_subjects[0].intSubjectID : null;
                        this.add_subject.studentID = this.id;
                        this.add_subject.activeSem = this.selected_ay;
                        this.advanced_privilages1 = data.data.advanced_privilages1;
                        this.advanced_privilages2 = data.data.advanced_privilages2;                        
                        this.sy = data.data.sy;
                        this.term_type = data.data.term_type;
                        this.photo_dir = data.data.photo_dir;
                        this.img_dir = data.data.img_dir;
                        this.sem_student = this.selected_ay;
                        this.registrar_privilages =  data.data.registrar_privilages;        
                        this.grad_status = this.student.isGraduate;     
                        this.records = data.data.records;           
                        this.total_units = data.data.total_units;
                        this.lab_units = data.data.lab_units;
                        this.gpa = data.data.gpa;
                    }
                    else{
                        document.location = this.base_url + 'users/login';
                    }

                    this.loader_spinner = false;                    
                })
                .catch((error) => {
                    console.log(error);
                })
        }

    },

    methods: {  
        removeFromClasslist: function(classlistID){
            Swal.fire({
                title: 'Delete Entry?',
                text: "Continue deleting entry?",
                showCancelButton: true,
                confirmButtonText: "Yes",
                imageWidth: 100,
                icon: "question",
                cancelButtonText: "No, cancel!",
                showCloseButton: true,
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    var formdata= new FormData();
                    formdata.append("intCSID",classlistID);                                                            
                    return axios
                        .post('<?php echo base_url(); ?>unity/delete_student_cs',formdata, {
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
            });

        },
        submitSubject: function(){
            if(add_subject.section){            
                var formdata= new FormData();
                for (const [key, value] of Object.entries(this.add_subject)) {
                    formdata.append(key,value);
                }                                                    

                this.loader_spinner = true;
                axios.post(base_url + 'unity/add_to_classlist_ajax', formdata, {
                    headers: {
                        Authorization: `Bearer ${window.token}`
                    }
                })
                .then(data => {
                    this.loader_spinner = false;
                    Swal.fire({
                        title: "Success",
                        text: data.data.message,
                        icon: "success"
                    }).then(function() {
                        
                    });
                });
            }
            else
                Swal.fire({
                    title: "Failed",
                    text: 'Incomplete Data',
                    icon: "success"
                });

        },
        updateGradStatus: function(){
            
            var formdata= new FormData();
            formdata.append("intID",this.student.intID);
            formdata.append("isGraduate",this.grad_status);

            this.loader_spinner = true;
            axios.post(base_url + 'unity/update_graduate_status', formdata, {
                headers: {
                    Authorization: `Bearer ${window.token}`
                }
            })
            .then(data => {
                this.loader_spinner = false;
                Swal.fire({
                    title: "Success",
                    text: data.data.message,
                    icon: "success"
                }).then(function() {
                    
                });
            });
                                
        },
        changeTermSelected: function(){
            document.location = this.base_url + "unity/student_viewer/" + 
            this.student.intID + "/" + this.sem_student + "/" + this.tab;
        },          
         
        changeRegStatus: function(){
            let url = this.base_url + 'unity/update_rog_status';
            var formdata= new FormData();
            formdata.append("intRegistrationID",this.registration.intRegistrationID);
            formdata.append("intROG",this.registration_status);
            this.loader_spinner = true;
            axios.post(url, formdata, {
                headers: {
                    Authorization: `Bearer ${window.token}`
                }
            })
            .then(data => {
                this.loader_spinner = false;
                Swal.fire({
                    title: "Success",
                    text: data.data.message,
                    icon: "success"
                }).then(function() {
                    
                });
            });
            
            
        }
    }

})
</script>