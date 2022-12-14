<aside class="right-side">
<section class="content-header">
                    <h1>
                        Academic Year
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li class="active">Set Academic Year</li>
                    </ol>
                </section>
<div class="content">
    <div class="span10 box box-primary">
        <div class="box-header">
                <h3 class="box-title">Set Academic Year</h3>
        </div>
       
            
            <form id="set-ay-form" action="<?php echo base_url(); ?>registrar/submit_ay" method="post" role="form">
                 <div class="box-body">
                     
                     
                     
                <div class="form-group col-xs-6">
                    <label>Choose Academic Year to activate</label>
                    <select class="form-control" name="strAcademicYear">
                        <?php foreach($sy as $s): ?>
                            <option <?php echo ($s['enumStatus'] == "active")?'selected':''; ?>  value="<?php echo $s['intID'] ?>"><?php echo $s['enumSem']." ".$term_type." ".$s['strYearStart']."-".$s['strYearEnd'];  ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-xs-6">
                    <label>Choose Academic Year for processing</label>
                    <select class="form-control" name="intProcessing">
                        <?php foreach($sy as $s): ?>
                            <option <?php echo ($s['intProcessing'] == 1)?'selected':''; ?>  value="<?php echo $s['intID'] ?>"><?php echo $s['enumSem']." ".$term_type." ".$s['strYearStart']."-".$s['strYearEnd'];  ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group col-xs-12">
                    <input type="button" id = "submit-ay" value="set" class="btn btn-default  btn-flat">
                </div>
                <div style="clear:both"></div>
            </form>
       
        </div>
</aside>