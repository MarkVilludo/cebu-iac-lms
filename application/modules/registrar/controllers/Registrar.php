<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registrar extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        
        if(!$this->is_registrar() && !$this->is_super_admin() && !$this->is_department_head())
		  redirect(base_url()."unity");
        
		$this->config->load('themes');		
		$theme = $this->config->item('unity');
		if($theme == "" || !isset($theme))
			$theme = $this->config->item('global_theme');
		
        $settings = $this->data_fetcher->fetch_table('su-tb_sys_settings');
		foreach($settings as $setting)
		{
			$this->settings[$setting['strSettingName']] = $setting['strSettingValue'];
		}
        
        $this->data['img_dir'] = base_url()."assets/themes/".$theme."/images/";	
        $this->data['student_pics'] = base_url()."assets/photos/";
        $this->data['css_dir'] = base_url()."assets/themes/".$theme."/css/";
        $this->data['js_dir'] = base_url()."assets/themes/".$theme."/js/";
        $this->data['title'] = "CCT Unity";
        $this->load->library("email");	
        $this->load->helper("cms_form");
		$this->load->model("google_login");	
		$this->load->model("facebook_login");	
		$this->load->model("user_model");
        $this->config->load('courses');
        
        $this->data['department_config'] = $this->config->item('department');
        $this->data['terms'] = $this->config->item('terms');
        $this->data['term_type'] = $this->config->item('term_type');
        $this->data['unit_fee'] = $this->config->item('unit_fee');
        $this->data['misc_fee'] = $this->config->item('misc_fee');
        $this->data['lab_fee'] = $this->config->item('lab_fee');
        $this->data['id_fee'] = $this->config->item('id_fee');
        $this->data['athletic'] = $this->config->item('athletic');
        $this->data['srf'] = $this->config->item('srf');
        $this->data['sfdf'] = $this->config->item('sfdf');
        $this->data['csg'] = $this->config->item('csg');
        
        $this->data['page'] = "registrar";
        
        //$this->data["subjects"] = $this->data_fetcher->fetch_table('tb_mas_subjects');
        //$this->data["students"] = $this->data_fetcher->fetch_table('tb_mas_users',array('strLastname','asc'));
        
        $this->data["user"] = $this->session->all_userdata();
        $this->data['unread_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intRead'=>'0','intTrash'=>0,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['all_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intTrash'=>0,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['trashed_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intTrash'=>1,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['sent_messages'] = $this->data_fetcher->count_sent_items($this->session->userdata('intID'));
		
	}
    
    public function index()
	{	
        
        if($this->faculty_logged_in())
            redirect(base_url()."unity/faculty_dashboard");
        
        else
            redirect(base_url()."users/login");
        
        
	}
    

    public function registered_students_report($sem = null)
    {
       
            $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
            
            
            if($sem!=null)
            {
                $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($sem);
            }
            else
                $this->data['active_sem'] = $this->data_fetcher->get_active_sem();
            
            $this->data['selected_ay'] = $this->data['active_sem']['intID'];
            $programs = $this->data_fetcher->fetch_table('tb_mas_programs');
            
            $report = array();
            $total_resident = 0;
            $total_7th_district =0;
            $total_paying = 0; 
            
            foreach($programs as $prog)
            {
                $r['program'] = $prog['strProgramCode'];
                
                $r['resident_scholars'] = $this->data_fetcher->getScholars($prog['intProgramID'],'resident scholar',$this->data['selected_ay']);
                $total_resident+=$r['resident_scholars'];
                
                $r['paying'] = $this->data_fetcher->getScholars($prog['intProgramID'],'paying',$this->data['selected_ay']);
                $total_paying+= $r['paying'];
                
                $r['seventh_district'] = $this->data_fetcher->getScholars($prog['intProgramID'],'7th district scholar',$this->data['selected_ay']);
                $total_7th_district+= $r['seventh_district'];
                
                $report[] = $r;
            }
            
            
            $this->data['total_resident'] = $total_resident;
            $this->data['total_paying'] = $total_paying;
            $this->data['total_seventh_district'] = $total_7th_district;
            $this->data['total_all'] = $total_resident + $total_paying + $total_7th_district;
            $this->data['report'] = $report;
            
            $this->data['page'] = "registered_students";
            $this->data['opentree'] = "reports";
            //print_r($this->data['classlist']);
            $this->load->view("common/print_header",$this->data);
            $this->load->view("admin/registered_students_report",$this->data);
            $this->load->view("common/footer",$this->data); 
           // print_r($this->data['classlists']);
            
        
       
    }
    
    
    public function edit_ay($id)
    {
        
        if($this->is_super_admin() || $this->is_registrar())
        {
          
            $this->data['item'] = $this->data_fetcher->getAy($id);
            
            
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/edit_ay",$this->data);
            $this->load->view("common/footer",$this->data); 
            $this->load->view("ay_validation_js",$this->data); 
           // print_r($this->data['classlists']);
            
        }
        else
            redirect(base_url()."/users/login");    
        
        
    }
    
    
    
    public function submit_registration_old()
    {
        $post = $this->input->post();
      
        if(isset($post['subjects-loaded']))
        {
            
            $index = 0;
            $program = $this->data_fetcher->getProgram($post['intProgramID']);
            $active_sem = $this->data_fetcher->get_active_sem();
            foreach($post['subjects-loaded'] as $subject)
            {
                
                $subject_data = $this->data_fetcher->getSubjectCurr($subject,$post['intProgramID']);
                               
                $this->data['col1'][] = $subject_data['strCode'];

                if(!$this->data_fetcher->checkSubjectTaken($post['studentID'],$subject)) //if subject has not been taken
                {
                    if(isset($post['section-'.$subject]) && $post['section-'.$subject]!=0 && $post['section-'.$subject] != "new")// if section exists
                    {
                        
                        $cl_get = $this->data_fetcher->fetch_classlist_id($post['section-'.$subject]);

                        $cl_data['intStudentID'] = $post['studentID'];
                        $cl_data['intClassListID'] = $cl_get['intID'];
                        $this->data_poster->addStudentClasslist($cl_data);
                        $this->data['col2'][] = "Student Registered to Section ".$cl_get['strSection'];
                        $this->data['col3'][] = "<a href='".base_url()."unity/classlist_viewer/".$cl_get['intID']."'>View Classlist</a>";
                        
                        
                    }
                    elseif(isset($post['subjects-section'][$index]) && $post['section-'.$subject] != "new")
                    {
                        
                        $cl_get = $this->data_fetcher->fetch_classlist_id($post['subjects-section'][$index]);
                       
                        $cl_data['intStudentID'] = $post['studentID'];
                        $cl_data['intClassListID'] = $cl_get['intID'];
                        $this->data_poster->addStudentClasslist($cl_data);
                        $this->data['col2'][] = "Student Registered to Section ".$cl_get['strSection'];
                        $this->data['col3'][] = "<a href='".base_url()."unity/classlist_viewer/".$cl_get['intID']."'>View Classlist</a>";
                    }
                    else // kapag wla pa section
                    {
                        
                        
                        if(isset($post['section-'.$subject]) && $post['section-'.$subject] == "new")
                        {
                            $cl = $this->data_fetcher->checkClasslistExists($subject,$post['strAcademicYear'],$subject_data['strCode'],"new");
                            
                        }
                        else{
                            $cl = $this->data_fetcher->checkClasslistExists($subject,$post['strAcademicYear'],$subject_data['strCode']); // this is where auto sectioning happens
                            
                        }
                        

                        if(!is_array($cl)) //if $cl is not array
                        {
                             
                            //echo $cl;
                            if($cl!="1"){
                                $cl = explode("-",$cl);
                                $letter = $cl[count($cl)-1];
                                //echo $letter."<br />";
                                $letter++;
                                //echo $letter;
                            }
                            else
                            {
                                $letter = "A";
                            }
                            
                            
                            

                            $classlist['intFacultyID'] = 999;
                            $classlist['intSubjectID'] = $subject;
                            $classlist['strAcademicYear'] = $post['strAcademicYear'];
                            $classlist['strUnits'] = $subject_data['strUnits'];
                            $classlist['strSection'] = $subject_data['strCode']."-".$subject_data['intYearLevel']."-".$letter;
                            $classlist['strClassName'] = $subject_data['strCode'];
                           
                            
                            $this->data_poster->post_data('tb_mas_classlist',$classlist);
                            $cid = $this->db->insert_id();
                            $cname = $classlist['strClassName'];
                        }
                        else
                        {
                            $cname = $cl['strClassName'];
                            $cid = $cl['intID'];
                        }

                        
                        $cl_data['intStudentID'] = $post['studentID'];
                        $cl_data['intClassListID'] = $cid;
                        $this->data_poster->addStudentClasslist($cl_data);
                        $this->data['col2'][] = "Student Registered to Section ".$cname;
                        $this->data['col3'][] = "<a href='".base_url()."unity/classlist_viewer/".$cid."'>View Classlist</a>";
                        

                    }
                }              
                else
                {
                    
                    $this->data['col2'][] = "already passed or is enrolled in subject";
                    $this->data['col3'][] ="";
                }


                $index++;
                    
            }
            /*
            $academic_standing = $this->data_fetcher->getAcademicStanding($post['studentID'],$post['strAcademicYear']);
            
            if(!$this->data_fetcher->checkRegistered($post['studentID'],$post['strAcademicYear'])){
                $reg['intStudentID'] = $post['studentID'];
                $reg['intAYID'] = $post['strAcademicYear'];
                $reg['intYearLevel'] = $academic_standing['year'];
                $reg['dteRegistered'] = date("Y-m-d");
                $reg['enumRegistrationStatus'] = $post['enumRegistrationStatus'];
                
                if($post['enumStudentType']=="cross")
                    $st = "Cross Registered From ".$post['strFrom'];
                elseif($post['enumStudentType']=="transferee")
                    $st = "Transferred From ".$post['strFrom'];
                else
                    $st = $post['enumStudentType'];
                
                $reg['enumStudentType'] = $st;
                
                
                $this->data_poster->post_data('tb_mas_registration',$reg);
            }
            
            */
        }
        else
        {
            
            if($post['enumRegistrationStatus'] == "regular"){
                $this->session->set_flashdata('message','Select Subjects to Register');
                $this->session->set_flashdata('datapost',$post);
                redirect(base_url()."unity/register_old_student_not_post");
            }
            else
            {
            
                $this->data['col1'][] ="Not Registered"; 
                $this->data['col2'][] = "";
                $this->data['col3'][] ="";   
            }
        }
        
        $this->data['student_link'] = "<a href='".base_url()."unity/student_viewer/".$post['studentID']."'>View Student Info</a>";
        
        $this->data['sid'] = $post['studentID'];
        $this->data['ayid'] = $post['strAcademicYear'];

        echo json_encode($this->data);
        
        $this->session->set_userdata('from_advising',$this->data);
        //redirect(base_url()."registrar/advising_done");
        
    } 

    public function approveCompletion($id){
        if($this->is_super_admin() || $this->is_registrar())
        {
          
            $completion = $this->data_fetcher->getCompletionByID($id);
            if($completion['enumStatus'] != 1){
                $item = $this->data_fetcher->getItem('tb_mas_classlist_student',$completion['intClasslistStudentID'],'intCSID');
                $grade = getAve($item['floatPrelimGrade'],$item['floatMidtermGrade'],$completion['floatNewFinalTermGrade']); 
                

                $data['eq'] = getEquivalent($grade);
                $data['eq_raw'] = $grade;

                //updated final Grade
                $d['floatFinalsGrade'] = $completion['floatNewFinalTermGrade'];
                $d['floatFinalGrade'] = $data['eq'];
                $d['strRemarks'] = $data['remarks'] = getRemarks($data['eq']);
                $d['enumStatus'] = 'act';

                $this->data_poster->update_classlist('tb_mas_classlist_student',$d,$completion['intClasslistStudentID']);
                $this->data_poster->approve_completion($id);
            }
            redirect(base_url().'/registrar/completions');
            
        }
        else
            redirect(base_url()."/users/login");   

    }

    public function completions(){

        $this->load->view("common/header",$this->data);
        $this->load->view("admin/completions",$this->data);
        $this->load->view("common/footer",$this->data); 
        $this->load->view("common/completions_conf",$this->data); 
    }

    public function advising_done(){

        $data = $this->session->userdata('from_advising');        
        if(isset($data)){
            $this->load->view("common/header",$data);
            $this->load->view("admin/reg_student_result",$data);
            $this->load->view("common/footer",$data);

            $this->session->unset_userdata('from_advising');
        }
        else{
            echo "Invalid Transaction";
        }

    }
    
    public function submit_registration_old2()
    {
        $post = $this->input->post();
       
       
        $academic_standing = $this->data_fetcher->getAcademicStanding($post['studentID'],$post['strAcademicYear']);        
        $data['sid'] = $post['studentID'];
        $data['ayid'] = $post['strAcademicYear'];
        $student = $this->data_fetcher->getStudent($post['studentID']);
        
        $data['student_link'] = base_url()."unity/student_viewer/".$post['studentID'];
        
        if(!$this->data_fetcher->checkRegistered($post['studentID'],$post['strAcademicYear'])){
            $reg['intStudentID'] = $post['studentID'];
            $reg['intAYID'] = $post['strAcademicYear'];
            $reg['intYearLevel'] = $academic_standing['year'];
            $reg['dteRegistered'] = date("Y-m-d");
            $reg['enumRegistrationStatus'] = $post['enumRegistrationStatus'];
            $reg['enumScholarship'] = $post['enumScholarship'];        
            $reg['enumStudentType'] = $post['enumStudentType'];
            $s = $this->data_fetcher->get_sem_by_id($data['ayid']);
            $data['message'] = "Congratulations, you have been registered for ".$s['enumSem']." Term S.Y. ".$s['strYearStart']."-".$s['strYearEnd'];
            $data['tuition_payment_link'] = base_url()."unity/student_tuition_payment/".$student['slug'];
            $data['success'] = true;
            $this->data_poster->post_data('tb_mas_registration',$reg);
            
            if($student['strStudentNumber'][0] == "T")
                $stud['strStudentNumber'] = $tempNum = $this->data_fetcher->generateNewStudentNumber();

            $stud['intStudentYear'] = $academic_standing['year']; 
            $this->data_poster->post_data('tb_mas_users',$stud,$post['studentID']);

            //SEND AND GENERATE PAYMENT FOR TUITION LINK
        }
        else
        {
            $data['message'] = "Student Already Registered";
        }
        
        echo json_encode($data);
        // $this->load->view("common/header",$this->data);
        // $this->load->view("admin/reg_student_result2",$this->data);
        // $this->load->view("common/footer",$this->data); 
    } 
    
    public function submit_registration_new($post)
    {
           
            $index = 0;
            $program = $this->data_fetcher->getProgram($post['intProgramID']);
            if(isset($post['subjects-loaded']))
            {
                foreach($post['subjects-loaded'] as $subject)
                {
                    $subject_data = $this->data_fetcher->getSubject($subject);


                    $this->data['col1'][] = $subject_data['strCode'];

                    if(!$this->data_fetcher->checkSubjectTaken($post['studentID'],$subject))
                    {

                        if(isset($post['subjects-section'][$index]))
                        {

                            $cl_get = $this->data_fetcher->fetch_classlist_id($post['subjects-section'][$index]);

                            $cl_data['intStudentID'] = $post['studentID'];
                            $cl_data['intClassListID'] = $cl_get['intID'];
                            $this->data_poster->addStudentClasslist($cl_data);
                            $this->data['col2'][] = "Student Registered to ";
                            $this->data['col3'][] = "<a href='".base_url()."unity/classlist_viewer/".$cl_get['intID']."'>View Classlist</a>";
                        }
                        else
                        {

                            $cl = $this->data_fetcher->checkClasslistExists($subject,$post['strAcademicYear'],$subject_data['strCode']);
                            if(!is_array($cl))
                            {
                                if($cl!="1"){
                                    $cl = explode("-",$cl);
                                    $letter = $cl[2];
                                    $letter++;
                                }
                                else
                                {
                                    $letter = "A";
                                }

                                $classlist['intFacultyID'] = 999;
                                $classlist['intSubjectID'] = $subject;
                                $classlist['strAcademicYear'] = $post['strAcademicYear'];
                                $classlist['strUnits'] = $subject_data['strUnits'];
                                $classlist['strSection'] = $subject_data['strCode']."-".$subject_data['intYearLevel']."-".$letter;
                                $classlist['strClassName'] = $subject_data['strCode'];
                                $this->data_poster->post_data('tb_mas_classlist',$classlist);
                                $cid = $this->db->insert_id();
                                $cname = $classlist['strClassName'];
                            }
                            else
                            {
                                //print_r($cl);
                                //echo "<br />".$cl['strClassName'];
                                $cname = $cl['strClassName'];
                                $cid = $cl['intID'];
                            }

                            $cl_data['intStudentID'] = $post['studentID'];
                            $cl_data['intClassListID'] = $cid;
                            $this->data_poster->addStudentClasslist($cl_data);
                            $this->data['col2'][] = "Student Registered to Section ".$cname;
                            $this->data['col3'][] = "<a href='".base_url()."unity/classlist_viewer/".$cid."'>View Classlist</a>";
                        }
                    }
                    else
                    {
                        $this->data['col2'][] = "already passed or is enrolled in subject";
                        $this->data['col3'][] ="";
                    }



                    $index++;
                }

                $reg['intStudentID'] = $post['studentID'];
                $reg['intAYID'] = $post['strAcademicYear'];
                $reg['intYearLevel'] = $post['intYearLevel'];
                $reg['dteRegistered'] = date("Y-m-d");
                $reg['enumRegistrationStatus'] = $post['enumRegistrationStatus'];

                if($post['enumStudentType']=="cross")
                    $st = "Cross Registered From ".$post['strFrom'];
                elseif($post['enumStudentType']=="transferee")
                    $st = "Transferred From ".$post['strFrom'];
                else
                    $st = $post['enumStudentType'];
            }
        
            $reg['enumStudentType'] = $st;
            $this->data_poster->post_data('tb_mas_registration',$reg);


            $this->data['student_link'] = "<a href='".base_url()."unity/student_viewer/".$post['studentID']."'>View Student Info</a>";
        
            
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/reg_student_result",$this->data);
            $this->load->view("common/footer",$this->data); 
        
        
    }
    
    
    public function registration_viewer($id,$sem = null)
    {
        $active_sem = $this->data_fetcher->get_active_sem();
        
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        
        
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        $payment = $this->data_fetcher->getTransactionsPayment($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        $pay =  array();
        foreach($payment as $p){
            if(isset($pay[$p['strTransactionType']]))
                $pay[$p['strTransactionType']] += $p['intAmountPaid'];
            else
                $pay[$p['strTransactionType']] = $p['intAmountPaid'];
            
        }
        $this->data['payment'] = $pay;
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['student']['enumScholarship']);
        
        $this->load->view("common/header",$this->data);
        $this->load->view("admin/registration_viewer",$this->data);
        $this->load->view("common/footer",$this->data); 
        $this->load->view("common/registration_viewer_conf",$this->data); 
    }
    
    function get_transaction_ajax()
    {
        $post = $this->input->post();
        $or = $post['orNumber'];
        $transactions = $this->data_fetcher->getTransactionsOR($or);
        $total = 0;
        $ret ='<div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">OR Number: <br />'.$or.'</div>
                    <div class="col-sm-4">Date  <br />'.$transactions[0]['dtePaid'].'</div>
                </div>
                <hr />
                
                <div class="row">
                    <div class="col-sm-4">Nature of Collection</div>
                    <div class="col-sm-4">Amount</div>
                </div>
                <hr />
                ';
        
        foreach($transactions as $trans){
            $ret .='<div class="row">
                        <div class="col-sm-4">'.$trans['strTransactionType'].'</div>
                        <div class="col-sm-4">'.$trans['intAmountPaid'].'</div>
                        <div class="col-sm-4"><button type="button" rel="'.$trans['intTransactionID'].'" class="btn btn-box-tool trash-transaction"><i style="font-size:2em;" class="ion ion-trash-a"></i></button></div>
                    </div>';

                    $total += $trans['intAmountPaid'];
        }
        $words = convert_number($total);
           $ret .= '
           <div class="row">
            <div class="col-sm-4" style="text-align:right">Total:</div>
            <div class="col-sm-4">'.$total.'</div>
           </div>
           <hr />
            <div>Amount in words:<br />'.$words.' pesos</div>
           </div>
        </div>';
        $data['viewer'] = $ret; 
        echo json_encode($data);
        
    }

    function get_registration_info($slug){

        $sem = $this->data_fetcher->get_active_sem();
        $sdata['student'] = $this->data_fetcher->fetch_single_entry('tb_mas_users',$slug,'slug');
        $sdata['registration_data'] =  $this->data_fetcher->getRegistrationInfo($sdata['student']['intID'],$sem['intID']);
        $sdata['tuition_data'] =  $this->data_fetcher->getTuition($sdata['student']['intID'],$sem['intID']);
        $sdata['current_sem'] = $sem['intID'];
        
        
        $data['data'] = $sdata;
        $data['message'] = "Success";
        $data['success'] = true;
        echo json_encode($data);
    }
    
    function get_tuition_ajax()
    {
        $post = $this->input->post();
        //print_r($post);
        if(!isset($post['subjects_loaded']))
        {
            $post['subjects_loaded'] = array();
        }
        $tuition = $this->data_fetcher->getTuitionSubjects($post['stype'],$this->data['unit_fee'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$post['scholarship'],$post['subjects_loaded'],$post['studentID']);
       
        
        $ret ='<div class="box box-solid">
            <div class="box-header">
                <h4 class="box-title">ASSESSMENT OF FEES</h4>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">Tuition:</div>
                    <div class="col-sm-6 text-green">'.$tuition['tuition'].'</div>
                </div>
                <hr />
                
                <div class="row">
                    <div class="col-sm-6">Miscellaneous:</div>
                    <div class="col-sm-6 text-green"></div>
                </div>';
            
                foreach($tuition['misc_fee'] as $key=>$val){
                $total_misc = 0;
                $ret .='<div class="row">
                            <div class="col-sm-6" style="text-align:right;">'.$key.'</div>
                            <div class="col-sm-6">'.$val.'</div>
                        </div>';
                 $total_misc += $val;
                }
                
                $ret .= '
                <div class="row">
                    <div class="col-sm-6" style="text-align:right;">Total:</div>
                    <div class="col-sm-6 text-green">'.$total_misc.'</div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-6">ID Fee:</div>
                    <div class="col-sm-6 text-green">'.$tuition['id_fee'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">Athletic Fee:</div>
                    <div class="col-sm-6 text-green">'.$tuition['athletic'].'</div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-6">SRF:</div>
                    <div class="col-sm-6 text-green">'.$tuition['srf'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">SFDF:</div>
                    <div class="col-sm-6 text-green">'.$tuition['sfdf'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">Lab Fee:</div>
                    <div class="col-sm-6">'.$tuition['lab'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="text-align:right;">Total:</div>
                    <div class="col-sm-6 text-green">'.$tuition['lab'].'</div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-6">CSG:</div>
                    <div class="col-sm-6"></div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="text-align:right;">Student Handbook:</div>
                    <div class="col-sm-6">'.$tuition['csg']['student_handbook'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="text-align:right;">Student Publication:</div>
                    <div class="col-sm-6">'.$tuition['csg']['student_publication'].'</div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="text-align:right;">Total:</div>
                    <div class="col-sm-6 text-green">'.($tuition['csg']['student_handbook']+$tuition['csg']['student_publication']).'</div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-6">Total:</div>
                    <div class="col-sm-6 text-green">'.$tuition['total'].'</div>
                </div>
            </div>
        </div>';
        
        $data['tuition'] = $ret;
        echo json_encode($data);
    }
    
    function generate_student_number()
    {
        
        $post = $this->input->post();
        $data['studentNumber'] = $this->data_fetcher->generateStudentNumber(substr($post['year'],-2));
        
        echo json_encode($data);   
    }
    
    function load_advised_subjects($status = "regular")
    {
        
        $post = $this->input->post();
        $subjects = $this->data_fetcher->getRequiredSubjects($post['sid'],$post['cid'],$post['sem'],$post['year']);
        $s = array();
        
        if($status!="regular")
            for($i=1;$i<=3;$i++)
            {
                if($i != $post['sem']){
                    $s = $this->data_fetcher->getRequiredSubjects($post['sid'],$post['cid'],$i,$post['year']);
                }
                $subjects = array_merge($subjects,$s);
            }
        
        $data = $subjects;
        echo json_encode($data);  
    }
    
    function generate_or()
    {  
        $data['orNumber'] = $this->data_fetcher->generateOR();
        echo json_encode($data);
        
    }
    

    public function register_old_student($studNum=null)
    {
        
       
			
            //print_r($post);
            //die();
            $this->data['message'] = $this->session->flashdata('message');
            
            //$this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
            if($studNum==null){
                $post = $this->input->post();
                $this->data['student'] = $this->data_fetcher->getStudent($post['studentNumber']);
            }
            else
                $this->data['student'] = $this->data_fetcher->getStudent($studNum);
            
            
            
            if(empty($this->data['student']))
            {
                //Message here for no student found
                $this->session->set_flashdata('error_message','Student does not exist');
                redirect(base_url().'registrar/register_student');
            }
			$this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
            $active_sem = $this->data_fetcher->get_processing_sem();
            $this->data['reg_status'] = $this->data_fetcher->getRegistrationStatus($this->data['student']['intID'],$active_sem['intID']);
            $sem = 1;
            
            switch($active_sem['enumSem'])
            {
                case "1st":
                    $sem = 1;
                    break;
                case "2nd":
                    $sem = 2;
                    break;
                case "3rd":
                    $sem = 3;
                    break;
                default:
                    $sem = 1;
            }
            
            $this->data['active_sem'] = $active_sem;
            
            
            $this->data['subjects'] = $this->data_fetcher->get_subjects_by_course($this->data['student']['intProgramID'],$sem);
            
            if(!empty($this->data['subjects']))
                $this->data['sections'] = $this->data_fetcher->fetch_classlist_by_subject($this->data['subjects'][0]['intID'],$active_sem['intID']);
           
            
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/sectioning_student",$this->data);
            $this->load->view("common/footer",$this->data); 
            $this->load->view("common/register_old_student_conf",$this->data);
           // print_r($this->data['classlists']);
            
            
    }

    public function register_old_student_data($studNum){

        $data['student'] = $this->data_fetcher->getStudent($studNum);
        $data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy',array('intProcessing','desc'));
        $data['scholarships'] = $this->data_fetcher->fetch_table('tb_mas_scholarships');
        $active_sem = $this->data_fetcher->get_processing_sem();
        $data['reg_status'] = $this->data_fetcher->getRegistrationStatus($data['student']['intID'],$active_sem['intID']);
            $sem = 1;
            
            switch($active_sem['enumSem'])
            {
                case "1st":
                    $sem = 1;
                    break;
                case "2nd":
                    $sem = 2;
                    break;
                case "3rd":
                    $sem = 3;
                    break;
                default:
                    $sem = 1;
            }
            
            $data['active_sem'] = $active_sem;
            $data['term_type'] = $this->data['term_type'];

            $ret['data'] = $data;
            $ret['success'] = true;
            $ret['message'] = "Success";

            echo json_encode($ret);

    }    
    
    public function register_old_student2($studNum=null)
    {
                   
            if($studNum==null){
                $post = $this->input->post();
                $studNum = $post['studentNumber'];
            }                        
            
            $this->data['id'] = $studNum;
            
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/register_old_student",$this->data);
            $this->load->view("common/footer",$this->data); 
            //$this->load->view("common/register_old_student_conf2",$this->data);
            //$this->load->view("registration_validation_js",$this->data);
            // print_r($this->data['classlists']);
            
            
    }
    
    public function register_old_student_not_post()
    {
        
        
            $this->data['message'] = $this->session->flashdata('message');
            $post = $this->session->flashdata('datapost');
            
            //$this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
            $this->data['student'] = $this->data_fetcher->getStudentStudentNumber($post['studentNumber']);
			$this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
            $active_sem = $this->data_fetcher->get_active_sem();
            $sem = 1;
            switch($active_sem['enumSem'])
            {
                case "1st":
                    $sem = 1;
                    break;
                case "2nd":
                    $sem = 2;
                    break;
                case "3rd":
                    $sem = 3;
                    break;
                default:
                    $sem = 1;
            }
            
            $this->data['subjects'] = $this->data_fetcher->get_subjects_by_course($this->data['student']['strProgramCode'],$sem);
            if(!empty($this->data['subjects']))
                $this->data['sections'] = $this->data_fetcher->fetch_classlist_by_subject($this->data['subjects'][0]['intID'],$active_sem['intID']);
           // print_r($this->data['records']);
            
            $this->data['active_sem'] = $active_sem;
            
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/register_old_student",$this->data);
            $this->load->view("common/footer",$this->data); 
            $this->load->view("common/register_old_student_conf",$this->data);
           // print_r($this->data['classlists']);
            
           
    }
    
    public function submit_transaction_ajax()
    {
        $post = $this->input->post();
     
        
        if($this->is_super_admin() || $this->is_accounting()){
            for($i=0;$i < count($post['intAmountPaid']);$i++){
                
                $data['dtePaid'] = date("Y-m-d H:i:s");
                $data['intRegistrationID'] = $post['intRegistrationID'];
                $data['intORNumber'] = $post['intORNumber'];
                $data['intAYID'] = $post['intAYID'];
                $data['intAmountPaid'] = $post['intAmountPaid'][$i];
                $data['strTransactionType'] = $post['strTransactionType'][$i];
                
                $this->data_poster->post_data('tb_mas_transactions',$data);
                $this->data_poster->log_action('Transaction','Added a new Transaction ID: '.$this->db->insert_id(),'green');
                //redirect(base_url()."unity/view_schedules");
                
            }
            $s['message'] = "success";
        }
        else 
            $s['message'] = "Please log in as admin or registrar";
        
        
        echo json_encode($s);
            
    }
    
    public function submit_new_ay()
    {
        $post = $this->input->post();
        $post['strYearEnd'] = $post['strYearStart']+1;
        //print_r($post);
        $this->data_poster->post_data('tb_mas_sy',$post);
        redirect(base_url()."registrar/set_ay");
            
    }
    
    public function set_ay()
    {
        if($this->is_super_admin() || $this->is_registrar())
        {
            $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
            $this->data['page'] = "set_ay";
            $this->data['opentree'] = "registrar";
            //print_r($this->data['classlist']);
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/set_ay",$this->data);
            $this->load->view("common/footer",$this->data); 
           // print_r($this->data['classlists']);
            
        }
        else
            redirect(base_url()."/users/login");   
    }
    
    public function submit_ay()
    {
        
        $post = $this->input->post();
        $id2 = $post['intProcessing'];        
        $post['enumStatus'] = "active";
        $post2['intProcessing'] = 1;
        $id = $post['strAcademicYear'];
        
        unset($post['strAcademicYear']);
        //print_r($post);
        $this->data_poster->set_ay_inactive();
        $this->data_poster->post_data('tb_mas_sy',$post,$id);
        $this->data_poster->post_data('tb_mas_sy',$post2,$id2);
        
        $activeSem = $this->data_fetcher->get_active_sem();
        $this->data_poster->log_action('Academic Term','Updated Term: '.$activeSem['enumSem']." term ".$activeSem['strYearStart']."-".$activeSem['strYearEnd'],'blue');
        //echo $activeSem['strYearStart']-1;
        
        redirect(base_url()."/registrar/set_ay");
            
    }
    
    public function update_incomplete_subjects($id)
    {
        if($this->is_super_admin() || $this->is_registrar())
        {
            $activeSem = $this->data_fetcher->getAy($id);
            $this->data_poster->updateIncompleteSubjects($activeSem['strYearStart'],$activeSem['enumSem']);
        }
        redirect(base_url()."unity");
    }
    
    public function edit_submit_ay()
    {
        $post = $this->input->post();
        //print_r($post);
        if($post['enumStatus'] == "active"){
            $this->data_poster->set_ay_inactive();
            $post['intProcessing'] = "1";
        }
        $post['strYearEnd'] = $post['strYearStart'] + 1;
       // $this->data_poster->set
        $this->data_poster->post_data('tb_mas_sy',$post,$post['intID']);
        $this->data_poster->log_action('School Year','Updated SY Info: '.$post['enumSem']." ".$post['strYearStart']." - ".$post['strYearEnd'],'aqua');
        redirect(base_url()."registrar/view_all_ay");
            
    }
    

    
    public function register_student()
    {

        $this->data['error_message'] = $this->session->flashdata('error_message');
        $this->data['page'] = "register_student";
        $this->data['opentree'] = "registrar";
        $this->load->view("common/header",$this->data);
        $this->load->view("admin/register_student",$this->data);
        $this->load->view("common/footer",$this->data);
        $this->load->view("common/registration_conf",$this->data);

        //print_r($this->data['classlist']);
    }
    
    public function add_ay()
    {
        if($this->is_super_admin() || $this->is_registrar())
        {
            $this->data['page'] = "add_ay";
            $this->data['opentree'] = "registrar";
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/add_ay",$this->data);
            $this->load->view("common/footer",$this->data); 
            
           // $this->load->view("student_validation_js",$this->data); 
            //print_r($this->data['classlist']);
            
        }
        else
            redirect(base_url()."/users/login");  
    }
    
    
    public function view_all_ay()
    {
        if($this->faculty_logged_in())
        {
            $this->data['page'] = "view_academic_year";
            $this->data['opentree'] = "registrar";
            $this->data['academic_years'] = $this->data_fetcher->fetch_table('tb_mas_sy',array('strYearStart','desc'),20);
            $this->load->view("common/header",$this->data);
            $this->load->view("admin/ay_view",$this->data);
            $this->load->view("common/footer",$this->data); 
            //print_r($this->data['classlist']);
            
        }
        else
            redirect(base_url()."/users/login");  
    }
    
    
    public function faculty_logged_in()
    {
        if($this->session->userdata('faculty_logged'))
            return true;
        else
            return false;
    }
    
    
    public function is_admin()
    {
         $admin = $this->session->userdata('intUserLevel');
        if($admin == 1 || $this->is_super_admin())
            return true;
        else
            return false;
    }
    
    public function is_super_admin()
    {
         $admin = $this->session->userdata('intUserLevel');
        if($admin == 2)
            return true;
        else
            return false;
    }
    
    public function is_registrar()
    {
        $admin = $this->session->userdata('intUserLevel');
        if($admin == 3)
            return true;
        else
            return false;
        
    }
    
    public function is_department_head()
    {
        $admin = $this->session->userdata('intUserLevel');
        if($admin == 4)
            return true;
        else
            return false;
        
    }
    
    public function is_accounting()
    {
        $admin = $this->session->userdata('intUserLevel');
        if($admin == 6)
            return true;
        else
            return false;
        
    }
    
}