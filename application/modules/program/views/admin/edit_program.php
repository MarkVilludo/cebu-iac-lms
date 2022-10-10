<aside class="right-side">
<section class="content-header">
                    <h1>
                        Program
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Program</a></li>
                        <li class="active">Edit Program</li>
                    </ol>
                </section>
<div class="content">
    <div class="span10 box box-primary">
        <div class="box-header">
                <h3 class="box-title">Edit Program</h3>
        </div>
       
            
            <form id="validate-program" action="<?php echo base_url(); ?>program/submit_edit_program" method="post" role="form">
                <input type="hidden" name="intProgramID" value="<?php echo $item['intProgramID']; ?>" />
                <div class="box-body">
                         <div class="form-group col-xs-6">
                            <label for="strProgramCode">Program Code</label>
                            <input type="text" value="<?php echo $item['strProgramCode']; ?>" name="strProgramCode" class="form-control" id="strProgramCode" placeholder="Enter Code">
                        </div>
                    <div class="form-group col-xs-6">
                            <label for="strMajor">Major</label>
                            <input type="text" value="<?php echo $item['strMajor']; ?>" name="strMajor" class="form-control" id="strMajor" placeholder="Enter Major">
                        </div>

                         <div class="form-group col-xs-6">
                            <label for="strProgramDescription">Program Description</label>
                             <textarea name="strProgramDescription" class="form-control"><?php echo $item['strProgramDescription']; ?></textarea>
                        </div>
                    
                         <div class="form-group col-xs-6">
                            <label for="enumEnabled">Enable Program Status</label>
                              <select class="form-control" name="enumEnabled" id="enumEnabled" >
                                <option <?php echo ($item['enumEnabled'] == 0)?'selected':''; ?> value="0">No</option>
                                <option <?php echo ($item['enumEnabled'] == 1)?'selected':''; ?> value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12">
                            <input type="submit" value="update" class="btn btn-default  btn-flat">
                        </div>
                    <div style="clear:both"></div>
                </div>
            </form>
    </div>
    </div>
</aside>