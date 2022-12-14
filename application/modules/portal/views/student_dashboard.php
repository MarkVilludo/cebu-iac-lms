<div class="content-wrapper">
     <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Welcome</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo base_url() ?>portal"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
        </section>
    <section class="content">
       <div class="row">
        <div class="col-md-4 col-sm-8 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-star"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ACADEMIC YEAR</span>
              <span class="info-box-number"><?php echo $active_sem['enumSem']." ".$term_type." ".$active_sem['strYearStart']."-".$active_sem['strYearEnd']; ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->

        </div>
        <div class="col-md-4 col-sm-8 col-xs-12">
           
            <div class="small-box bg-yellow">
            <div class="inner">
              <h3>My Profile</h3>
                <p>view your personal information</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="<?php echo base_url(); ?>portal/profile" class="small-box-footer">
              View Profile <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <div class="col-md-4 col-sm-8 col-xs-12">
            <!-- small box -->
          <div class="small-box bg-orange">
            <div class="inner">
              <h3>My Courses</h3>

              <p>view your current courses enrolled</p>
            </div>
            <div class="icon">
              <i class="fa fa-book"></i>
            </div>
            <a href="<?php echo base_url(); ?>portal/mycourses" class="small-box-footer">
              View Courses <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
            
            <!-- small box -->
       </div>
        <div class="col-md-4 col-sm-8 col-xs-12">
            <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>My Grades</h3>

              <p>view your current grades</p>
            </div>
            <div class="icon">
              <i class="fa fa-pencil"></i>
            </div>
            <a href="<?php echo base_url(); ?>portal/grades" class="small-box-footer">
              View Grades <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
            
            <!-- small box -->
       </div>
     
       <div class="col-md-4 col-sm-8 col-xs-12">
           
            <div class="small-box bg-gray">
                <div class="inner">
                <h3>My Schedule</h3>
                    <p>view your schedule</p>
                </div>
                <div class="icon">
                <i class="fa fa-calendar"></i>
                </div>
                <a href="<?php echo base_url(); ?>portal/schedule" class="small-box-footer">
                View Schedules <i class="fa fa-arrow-circle-right"></i>
                </a>
          </div>
        </div>


        <div class="col-md-4 col-sm-8 col-xs-12">
           
           <div class="small-box bg-blue">
               <div class="inner">
               <h3>My Transactions</h3>
                   <p>view your account summary</p>
               </div>
               <div class="icon">
               <i class="ion ion-calculator"></i>
               </div>
               <a href="<?php echo base_url(); ?>portal/accounting_summary" class="small-box-footer">
               View Transactions <i class="fa fa-arrow-circle-right"></i>
               </a>
         </div>
       </div>
    </div>
</div>