<aside class="right-side">
<section class="content-header">
                    <h1>
                        Subject
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Subject</a></li>
                        <li class="active">Edit Subject</li>
                    </ol>
                </section>
<div class="content">
    <div class="span10 box box-primary">
        <div class="box-header">
                <h3 class="box-title">Edit Subject</h3>
        </div>
       
            
            <form id="validate-subject" action="<?php echo base_url(); ?>subject/edit_submit_subject" method="post" role="form">
                <input type="hidden" name="intID"  id="intID" value="<?php echo $subject['intID']; ?>">
                 <div class="box-body">
                     <div class="form-group col-xs-6">
                        <label for="strCode">Subject Code</label>
                        <input type="text" value="<?php echo $subject['strCode']; ?>" name="strCode" class="form-control" id="strCode" placeholder="Enter Subject Code">
                    </div>
                     
                     <div class="form-group col-xs-6">
                        <label for="strUnits">Number of Units</label>
                        <input type="number" value="<?php echo $subject['strUnits']; ?>"  name="strUnits" class="form-control" id="strUnits" placeholder="Enter Number of Units">
                    </div>
                    <div class="form-group col-xs-6">
                            <label for="strUnits">Number of Units for Tuition</label>
                            <input type="number" name="strTuitionUnits" value="<?php echo $subject['strTuitionUnits'] ?>" class="form-control" id="strTuitionUnits" placeholder="Enter Number of Units">
                    </div>
                    <?php echo cms_dropdown('strLabClassification','Lab Type',$lab_types,'col-sm-6',$subject['strLabClassification']); ?>                                                
                     <div class="form-group col-xs-6">
                            <label for="intLab">Laboratory Units</label>
                            <input type="number" class="form-control" value="<?php echo $subject['intLab'] ?>" name="intLab" id="intLab" />
                        </div>
                     <div class="form-group col-xs-6">
                            <label for="intLectHours">Lecture/Class Units</label>
                            <input type="number" class="form-control" value="<?php echo $subject['intLectHours'] ?>" name="intLectHours" id="intLectHours" />
                    </div>                        
                        <div class="form-group col-xs-6">
                            <label for="isNSTP">NSTP Subject?</label>
                            <select class="form-control" name="isNSTP" id="isNSTP" >
                                <option <?php echo ($subject['isNSTP'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($subject['isNSTP'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="isInternshipSubject">Internship Subject?</label>
                            <select class="form-control" name="isInternshipSubject" id="isInternshipSubject" >
                                <option <?php echo ($subject['isInternshipSubject'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($subject['isInternshipSubject'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div> 
                        <div class="form-group col-xs-6">
                            <label for="isThesisSubject">Thesis Subject?</label>
                            <select class="form-control" name="isThesisSubject" id="isThesisSubject" >
                                <option <?php echo ($subject['isThesisSubject'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($subject['isThesisSubject'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div> 
                        <div class="form-group col-xs-6">
                            <label for="intBridging">Bridging</label>
                            <select class="form-control" name="intBridging" id="intBridging" >
                                <option  <?php echo ($subject['intBridging'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($subject['intBridging'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div>
                     <div class="form-group col-xs-6">
                            <label for="intYearLevel">Major Subject</label>
                            <select class="form-control" name="intMajor" id="intMajor" >
                                <option <?php echo ($subject['intMajor'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($subject['intMajor'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div>
                      <?php echo cms_dropdown('strDepartment','Department',$dpt,'col-sm-6',$subject['strDepartment']); ?>
                        <div class="form-group col-xs-6">
                            <label for="intEquivalentID1">Equivalent Subject 1</label>
                            <select class="form-control select2" name="intEquivalentID1">
                                <option value="0">None</option>
                                <?php foreach($subjects as $s1): ?>
                                    <option <?php echo ($subject['intEquivalentID1'] == $s1['intID'])?'selected':''; ?> value="<?php echo $s1['intID']; ?>"><?php echo $s1['strCode'].' '.$s1['strDescription']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="intEquivalentID2">Equivalent Subject 2</label>
                            <select class="form-control select2" name="intEquivalentID2">
                                <option <?php echo ($subject['intEquivalentID2']==0)?'selected':''; ?> value="0">None</option>
                                <?php foreach($subjects as $s1): ?>
                                    <option <?php echo ($subject['intEquivalentID2']==$s1['intID'])?'selected':''; ?> value="<?php echo $s1['intID']; ?>"><?php echo $s1['strCode'].' '.$s1['strDescription']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                <div class="form-group col-xs-6">
                        <label>Description</label>
                        <textarea class="form-control"  name="strDescription" rows="3" placeholder="Enter Description"><?php echo $subject['strDescription']; ?></textarea>
                    </div>
                
                <div class="form-group col-xs-12">
                    <input type="submit" value="update" class="btn btn-default  btn-flat">
                </div>
                <div style="clear:both"></div>
            </form>
            <div class="row">
                <div class="col-md-5">
                    <h4>Select Prerequisites</h4>
                    <select style="height:300px" class="form-control select2" id="prereq-selector" multiple>
                        <?php foreach($prereq as $pre): ?>
                            <option value="<?php echo $pre['intID']; ?>"><?php echo $pre['strCode'].' '.$pre['strDescription']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <br /><br />
                    <a href="#" id="load-prereq" class="btn btn-default  btn-flat btn-block">Load <i class="ion ion-arrow-right-c"></i> </a>
                    <a href="#" id="unload-prereq" class="btn btn-default  btn-flat btn-block"><i class="ion ion-arrow-left-c"></i> Remove</a>
                    <a href="#" id="save-prereq" class="btn btn-default  btn-flat btn-block">Save</a>

                </div>
                <div class="col-md-5">
                    <h4>Prerequisites</h4>
                    <select style="height:100px" class="form-control" id="prereq-selected" multiple>
                        <?php foreach($selected_prereq as $pre): ?>
                            <option value="<?php echo $pre['intID']; ?>"><?php echo $pre['strCode']." ".$pre['strDescription']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <h4>Select Schema</h4>
                    <select style="height:150px" class="form-control" id="days-selector" multiple>
                        <?php foreach($days as $val): ?>
                            <option value="<?php echo $val; ?>"><?php echo switch_day_schema($val); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <br /><br />
                    <a href="#" id="load-days" class="btn btn-default  btn-flat btn-block">Load <i class="ion ion-arrow-right-c"></i> </a>
                    <a href="#" id="unload-days" class="btn btn-default  btn-flat btn-block"><i class="ion ion-arrow-left-c"></i> Remove</a>
                    <a href="#" id="save-days" class="btn btn-default  btn-flat btn-block">Save</a>

                </div>
                <div class="col-md-5">
                    <h4>Schema for Scheduling</h4>
                    <select style="height:150px" class="form-control" id="days-selected" multiple>
                        <?php foreach($selected_days as $val): ?>
                            <option value="<?php echo $val; ?>"><?php echo switch_day_schema($val); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <h4>Select Rooms</h4>
                    <select style="height:300px" class="form-control" id="room-selector" multiple>
                        <?php foreach($rooms as $room): ?>
                            <option value="<?php echo $room['intID']; ?>"><?php echo $room['strRoomCode']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <br /><br />
                    <a href="#" id="load-rooms" class="btn btn-default  btn-flat btn-block">Load <i class="ion ion-arrow-right-c"></i> </a>
                    <a href="#" id="unload-rooms" class="btn btn-default  btn-flat btn-block"><i class="ion ion-arrow-left-c"></i> Remove</a>
                    <a href="#" id="save-rooms" class="btn btn-default  btn-flat btn-block">Save</a>

                </div>
                <div class="col-md-5">
                    <h4>Rooms for Use</h4>
                    <select style="height:300px" class="form-control" id="room-selected" multiple>
                        <?php foreach($selected_rooms as $room): ?>
                            <option value="<?php echo $room['intID']; ?>"><?php echo $room['strRoomCode']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
       
        </div>
</aside>