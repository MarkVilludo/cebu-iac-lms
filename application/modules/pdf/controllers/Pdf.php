<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once('src/facebook.php');

class Pdf extends CI_Controller {

	public function __construct()
	{
		
        parent::__construct();
		
        $this->config->load('themes');		
		$theme = $this->config->item('unity');
		if($theme == "" || !isset($theme))
			$theme = $this->config->item('global_theme');
		
        $settings = $this->data_fetcher->fetch_table('su-tb_sys_settings');
		foreach($settings as $setting)
		{
			$this->settings[$setting['strSettingName']] = $setting['strSettingValue'];
		}
        //$this->load->model("user_model");
        //$this->config->load('courses');
        $this->data["user"] = $this->session->all_userdata();
        $this->load->helper("cms_form");
        $this->load->helper('pdf');
        $this->load->helper('text');
        $this->config->load('courses');
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
        $this->data['img_dir'] = base_url()."assets/themes/".$theme."/images/";	
        $this->data['student_pics'] = base_url()."assets/photos/";
        
		
	}
    
    function save_registration_file($id, $dir, $sem = null)
    {
       
        $data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $data['selected_ay'] = $sem;
        else
            $data['selected_ay'] = $active_sem['intID'];
        
        $data['terms'] = $this->data['terms'];
        $data['term_type'] = $this->data['term_type'];
        $data['unit_fee'] = $this->data['unit_fee'];
        $data['misc_fee'] = $this->data['misc_fee'];
        $data['lab_fee'] = $this->data['lab_fee'];
        $data['id_fee']  = $this->data['id_fee'];
        $data['athletic'] = $this->data['athletic'];
        $data['srf'] = $this->data['srf'];
        $data['sfdf'] = $this->data['sfdf'];
        $data['csg'] = $this->data['csg'];
        $data['img_dir'] = $this->data['img_dir'];
        $data['student_pics'] = $this->data['student_pics'];

        $data['active_sem'] = $this->data_fetcher->get_sem_by_id($data['selected_ay']);
        $data['student'] = $this->data_fetcher->getStudent($id);
        $data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$data['selected_ay']);
        
        $data['academic_standing'] = $this->data_fetcher->getAcademicStanding($data['student']['intID'],$data['student']['intCurriculumID']);

        $data['transactions'] = $this->data_fetcher->getTransactions($data['registration']['intRegistrationID'],$data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $data['tuition'] = $this->data_fetcher->getTuition($id,$data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$data['registration']['enumScholarship']);
        
        $student['has_nstp'] = true;
            
            $records = $this->data_fetcher->checkClasslistStudentNSTP($data['student']['intID'],$sem);
            if(empty($records))
                $student['has_nstp'] = false;
            
            $s_array[] = $student;

        $registration = $data['registration'];
        $tuition = $data['tuition'];
        //$total = $data['total'];
        $units = $tuition['tuition']/$data['unit_fee'];

        if($student['has_nstp']) {
            $units -= 3;
            $nstp_units = 3;
            $nstp_fee = $data['unit_fee'] * 3;
            $tuition['tuition'] -= $nstp_fee;
            $data['tuition'] = $tuition;
            $data['nstp_fee'] = $nstp_fee; 
        }
        else {
                $nstp_units = 0;
                $nstp_fee = 0;
                $data['nstp_units'] = $nstp_units;
                $data['nstp_fee'] = $nstp_fee;
        }
        
        switch($data['student']['strProgramCode'])
        {
            case 'BSCS':
                $data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSIT':
                $data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSBA-MM':
                $data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSBA-HRDM':
                $data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSOA':
                $data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSE-E':
                $data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-F':
                $data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-M':
                $data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-SS':
                $data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSHM':
                $data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            case 'BSTM':
                $data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            default:
                $data['deanSignature'] = 'signat-SCS-Dean2.png';
        }
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $data['records'][] = $record;
        }
        
        
        
       $data['dirname'] = $dir;

        //for total units
        $data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("save_pdf_reg2",$data);
    
    }

    
    function zipAndDownload($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0)
    {
        set_time_limit(0);
        
        
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem == 0)            
            $sem = $active_sem['intID'];

        
        $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem);
                
        
        $fname = date('ymdhis');
                
        $dirName = FCPATH.'assets/temp/'.$fname;          
        mkdir($dirName);
        
        $filename = FCPATH.'assets/temp/'.$fname.".zip";
        
        foreach($students as $student){
            $this->save_registration_file($student['intID'],$dirName,$sem);
        }
        
        $zip = new ZipArchive();        
        
        if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }
                
        if (is_dir($dirName)){
            if ($dh = opendir($dirName)){
               while (($file = readdir($dh)) !== false){               
                 // If file
                 if (is_file($dirName.'/'.$file)) {
                     
                    if($file != '' && $file != '.' && $file != '..'){               
                       $zip->addFile($dirName.'/'.$file,$file);                        
                       
                    }
                 }           

               }
               closedir($dh);
             }
        }        
        $zip->close();
        
        $this->delete_directory($dirName);
        $this->downloadZip($filename);
        echo "done";
    }
    
    function delete_directory($dirname) {
        if (is_dir($dirname))
               $dir_handle = opendir($dirname);
         if (!$dir_handle)
              return false;
         while($file = readdir($dir_handle)) {
               if ($file != "." && $file != "..") {
                    if (!is_dir($dirname."/".$file))
                         unlink($dirname."/".$file);
                    else
                         delete_directory($dirname.'/'.$file);
               }
         }
         closedir($dir_handle);
         rmdir($dirname);
         return true;
    }
    
    function downloadZip($filename){
        
      if (file_exists($filename)) {
         header('Content-Type: application/zip');
         header('Content-Disposition: attachment; filename="'.basename($filename).'"');
         header('Content-Length: ' . filesize($filename));

         flush();
         readfile($filename);
         // delete file
         unlink($filename);

       }
        
    }
  
    
    function student_viewer_advising_blank_print()
    {
       $this->load->view("print_advising_blank",$this->data);
    }

    function student_viewer_advising_print($id,$sem= null)
    {
        $active_sem = $this->data_fetcher->get_processing_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];
        
        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        $this->data['prev_sem'] = $this->data_fetcher->get_prev_sem($active_sem['intID']);
            

        if(!empty($this->data['prev_sem']))
        {

            $this->data['prev_records'] = $this->data_fetcher->getClassListStudentsSt($this->data['student']['intID'],$this->data['prev_sem']['intID']);

            while(empty($this->data['prev_records']))
            {
                if(empty($this->data['prev_sem']))
                    break;

                $this->data['prev_sem'] = $this->data_fetcher->get_prev_sem($this->data['prev_sem']['intID']);
                $this->data['prev_records'] = $this->data_fetcher->getClassListStudentsSt($this->data['student']['intID'],$this->data['prev_sem']['intID']);
            }

        }
        else
            $this->data['prev_records'] = null;
        
        $subjects = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);
        $ret = array();
        foreach($subjects as $subj)
        {
            $classlists = $this->data_fetcher->fetch_classlist_by_subject($subj['subjectID'],$active_sem['intID']);
            $subj['classlists'] = $classlists;
            $ret[] = $subj;
        }
        $this->data['advised'] = $ret;
        
       $this->load->view("print_advising",$this->data);
        
        
    }
    function student_viewer_advising_print_data($id,$sem= null)
    {
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];
        
        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
         $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        $this->data['prev_sem'] = $this->data_fetcher->get_prev_sem();
            

        if(!empty($this->data['prev_sem']))
        {

            $this->data['prev_records'] = $this->data_fetcher->getClassListStudentsSt($this->data['student']['intID'],$this->data['prev_sem']['intID']);

            while(empty($this->data['prev_records']))
            {
                if(empty($this->data['prev_sem']))
                    break;

                $this->data['prev_sem'] = $this->data_fetcher->get_prev_sem($this->data['prev_sem']['intID']);
                $this->data['prev_records'] = $this->data_fetcher->getClassListStudentsSt($this->data['student']['intID'],$this->data['prev_sem']['intID']);
            }

        }
        else
            $this->data['prev_records'] = null;
        
        $subjects = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);
        
        foreach($subjects as $subj)
        {
            $classlists = $this->data_fetcher->fetch_classlist_by_subject($subj['subjectID'],$active_sem['intID']);
            $subj['classlists'] = $classlists;
            $ret[] = $subj;
        }
        $this->data['advised'] = $ret;
        
       $this->load->view("print_advising_data",$this->data);
        
        
    }
    
    function student_viewer_rog_data_print($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
        $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);
        
        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_view_student_data_rog",$this->data);
    
    }
    
    
    function student_viewer_registration_print($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
        $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);

        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id, $this->data['selected_ay'], $this->data['registration']['enumScholarship']);
        
        $student['has_nstp'] = true;
            
            $records = $this->data_fetcher->checkClasslistStudentNSTP($this->data['student']['intID'],$sem);
            if(empty($records))
                $student['has_nstp'] = false;
            
            $s_array[] = $student;

        $registration = $this->data['registration'];
        $tuition = $this->data['tuition'];
        //$total = $data['total'];
        

        switch($this->data['student']['strProgramCode'])
        {
            case 'BSCS':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSIT':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSBA-MM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSBA-HRDM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSOA':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSE-E':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-F':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-M':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-SS':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSHM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            case 'BSTM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            default:
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
        }
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_view_student_reg2",$this->data);
        //$this->load->view("save_pdf_reg2",$this->data);
    
    }

    function student_viewer_registration_print2($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
        $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);

        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
       
        $student['has_nstp'] = true;
            
            $records = $this->data_fetcher->checkClasslistStudentNSTP($this->data['student']['intID'],$sem);
            if(empty($records))
                $student['has_nstp'] = false;
            
            $s_array[] = $student;

        $registration = $this->data['registration'];
        $tuition = $this->data['tuition'];
        //$total = $data['total'];
        $units = $tuition['tuition']/$this->data['unit_fee'];

        if($student['has_nstp']) {
            $units -= 3;
            $nstp_units = 3;
            $nstp_fee = $this->data['unit_fee'] * 3;
            $tuition['tuition'] -= $nstp_fee;
            $this->data['tuition'] = $tuition;
            $this->data['nstp_fee'] = $nstp_fee; 
        }
        else {
                $nstp_units = 0;
                $nstp_fee = 0;
                $this->data['nstp_units'] = $nstp_units;
                $this->data['nstp_fee'] = $nstp_fee;
        }

        switch($this->data['student']['strProgramCode'])
        {
            case 'BSCS':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSIT':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSBA-MM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSBA-HRDM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSOA':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSE-E':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-F':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-M':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-SS':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSHM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            case 'BSTM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            default:
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
        }
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_view_student_reg_comp",$this->data);
        //$this->load->view("save_pdf_reg2",$this->data);
    
    }

    function student_completion_form_print($csid)
    {
       
        $cs = $this->data_fetcher->getClasslistStudent($csid);
        $st = $this->data_fetcher->getCompletion($csid);
        $this->data['cs'] = $cs;
        $this->data['st'] = $st;
        $ave = getAve($cs['floatPrelimGrade'],$cs['floatMidtermGrade'],$st['floatNewFinalTermGrade']);
        $eq = getEquivalent($ave);
        $this->data['ave'] = $ave;
        $this->data['eq'] = $eq;

        switch($this->data['cs']['strProgramCode'])
        {
            case 'BSCS':
                $this->data['school'] = 'Computer Studies';
            break;
            case 'BSIT':
                $this->data['school'] = 'Computer Studies';
            break;
            case 'BSBA-MM':
                $this->data['school'] = 'Business & Management';
            break;
            case 'BSBA-HRDM':
                $this->data['school'] = 'Business & Management';
            break;
            case 'BSOA':
                $this->data['school'] = 'Business & Management';
            break;
            case 'BSE-E':
                $this->data['school'] = 'Education';
            break;
            case 'BSE-F':
                $this->data['school'] = 'Education';
            break;
            case 'BSE-M':
                $this->data['school'] = 'Education';
            break;
            case 'BSE-SS':
                $this->data['school'] = 'Education';
            break;
            case 'BSHM':
                $this->data['school'] = 'Hospitality & Tourism Management';
            break;
            case 'BSTM':
                $this->data['school'] = 'Hospitality & Tourism Management';
            break;
            default:
                $this->data['school'] = 'n/a';
        }
   
        $this->load->view("print_view_student_completion",$this->data);
    
    }
    
    
    function student_viewer_registration_data_print_legacy($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
        $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);

        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_reg_legacy",$this->data);
    
    }
    
    function student_viewer_registration_data_print($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
        $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);

        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);
        
        switch($this->data['student']['strProgramCode'])
        {
            case 'BSCS':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSIT':
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
            break;
            case 'BSBA-MM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSBA-HRDM':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSOA':
                $this->data['deanSignature'] = 'signat-SBM-Dean2.png';
            break;
            case 'BSE-E':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-F':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-M':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSE-SS':
                $this->data['deanSignature'] = 'signat-SOE-Dean2.png';
            break;
            case 'BSHM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            case 'BSTM':
                $this->data['deanSignature'] = 'signat-SHTM-Dean2.png';
            break;
            default:
                $this->data['deanSignature'] = 'signat-SCS-Dean2.png';
        }

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_view_student_reg_data",$this->data);
    
    }
     function student_viewer_registration_blank_print()
    {
        $this->load->view("print_view_student_reg_blank",$this->data);
    }

      function old_reg($id, $sem = null)
    {
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
          $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);

        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        $this->load->view("print_view_student_reg_data",$this->data);
    }

    function get_curriculum_for_printing($slug){

        $data['student'] = $this->data_fetcher->getItem('tb_mas_users',$slug,'slug');
        $data['curriculum'] = $this->data_fetcher->getItem('tb_mas_curriculum',$data['student']['intCurriculumID']);
        $grades = $this->data_fetcher->assessCurriculum($data['student']['intID'],$data['student']['intCurriculumID']);
        array_unshift($grades,array('strCode'=>'none','floatFinalGrade'=>'n/a','strRemarks'=>'n/a'));
        $data['grades'] = $grades;
        $data['curriculum_subjects'] = $this->data_fetcher->getSubjectsInCurriculumMain($data['student']['intCurriculumID']);
        $data['equivalent_subjects'] = $this->data_fetcher->getSubjectsInCurriculumEqu($data['student']['intCurriculumID']);

        echo json_encode($data);

    }
    
    function print_curriculum($id,$studentId)
    {
        $grades = $this->data_fetcher->assessCurriculum($studentId,$id);
        array_unshift($grades,array('strCode'=>'none','floatFinalGrade'=>'n/a','strRemarks'=>'n/a'));
        $this->data['grades'] = $grades;
        $this->data['curriculum'] = $this->data_fetcher->getItem('tb_mas_curriculum',$id);
        $this->data['student'] = $this->data_fetcher->getItem('tb_mas_users',$studentId);
        //$this->data['curriculum_subjects'] = $this->data_fetcher->getSubjectsInCurriculum($id);
        $this->data['curriculum_subjects'] = $this->data_fetcher->getSubjectsInCurriculumMain($this->data['student']['intCurriculumID']);
        $this->data['equivalent_subjects'] = $this->data_fetcher->getSubjectsInCurriculumEqu($this->data['student']['intCurriculumID']);
            
         //print_r($this->data['spouse']);
        tcpdf();
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
       // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('A4'), true, 'UTF-8', false, true);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Curriculum");
       
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //$pdf->SetAutoPageBreak(TRUE, 6);

       //font setting
        //$pdf->SetFont('calibril_0', '', 15, '', 'false');
        // set default font subsetting mode
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_FOOTER);
        
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // Add a page
        
        $pdf->AddPage();
        $html = $this->load->view('curriculum',$this->data,true);
        //$html = $pdf->unhtmlentities($html);

        $pdf->writeHTML($html, true, false, true, false, '');
            
            
       
        $pdf->Output("curriculum.pdf", 'I');
    }
    
    function student_viewer_rog_print($id, $sem = null)
    {
       
        $this->data['sy'] = $this->data_fetcher->fetch_table('tb_mas_sy');
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        $this->data['student'] = $this->data_fetcher->getStudent($id);
        $this->data['registration'] = $this->data_fetcher->getRegistrationInfo($id,$this->data['selected_ay']);
        
         $this->data['academic_standing'] = $this->data_fetcher->getAcademicStanding($this->data['student']['intID'],$this->data['student']['intCurriculumID']);
        
        $this->data['transactions'] = $this->data_fetcher->getTransactions($this->data['registration']['intRegistrationID'],$this->data['selected_ay']);
        //--------TUITION-------------------------------------------------------------------
        $this->data['tuition'] = $this->data_fetcher->getTuition($id,$this->data['selected_ay'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$this->data['registration']['enumScholarship']);
        
        $records = $this->data_fetcher->getClassListStudentsSt($id,$this->data['selected_ay']);

       
        foreach($records as $record)
        {
            $record['schedule'] = $this->data_fetcher->getScheduleByCode($record['classlistID']);
            //print_r($record['schedule']);
            $this->data['records'][] = $record;
        }

        //for total units
        $this->data['total_units'] = $this->data_fetcher->getTotalUnits($id);
        
        $this->load->view("print_view_student_rog",$this->data);
    
    }
    
    public function print_sched($sem = null)
    {
        $post  = $this->input->post();
        
        $active_sem = $this->data_fetcher->get_active_sem();
        //$this->data['active_sem'] = $this->data_fetcher->get_active_sem();
        if($sem!=null)
            $this->data['selected_ay'] = $sem;
        else
            $this->data['selected_ay'] = $active_sem['intID'];

        $this->data['active_sem'] = $this->data_fetcher->get_sem_by_id($this->data['selected_ay']);
        //$this->data['faculty'] = $this->data_fetcher->getFaculty($id);
        
        $this->data['sched'] = $post['sched-table'];
        $this->data['facultyName'] = $post['facultyName'];
        $this->data['facultyDept'] = $post['facultyDept'];
        
        $this->load->view("print_sched",$this->data);
    }
    
    public function print_classlist_registrar($id,$page="front")
    {
        
        //print_r($this->data['spouse']);
        tcpdf();
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
       // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('A4'), true, 'UTF-8', false, true);
        $pdf = new TCPDF("P", PDF_UNIT, array(8.5, 13), true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Classlist");
        
        $this->data['classlist'] = $this->data_fetcher->fetch_classlist_by_id(null,$id);
        $this->data['sy'] = $this->data_fetcher->get_sem_by_id($this->data['classlist']['strAcademicYear']);
        $students = $this->data_fetcher->getClassListStudents($id);
        $this->data['subject'] = $this->data_fetcher->getSubjectNoCurr($this->data['classlist']['intSubjectID']);
        $schedule = $this->data_fetcher->fetch_table('tb_mas_room_schedule',null,null,array('strScheduleCode'=>$id));
        $this->data['faculty'] =  current($this->data_fetcher->fetch_table('tb_mas_faculty',null,null,array('intID'=>$this->data['classlist']['intFacultyID'])));
        $days = "";
        $added_days = array();
        $times = "";
        
        foreach($schedule as $sc)
        {
            if(!in_array($sc['strDay'],$added_days))
            {
                $added_days[] = $sc['strDay'];
                $days.=get_day($sc['strDay'])." ";
            }
            $times .= date("h:ia",strtotime($sc['dteStart']))."-".date("h:ia",strtotime($sc['dteEnd'])).", ";
        }
        $this->data['days'] = $days;
        $this->data['time'] = $times;
        //print_r($schedule);
        $this->data['program'] = current($this->data_fetcher->fetch_table('tb_mas_programs',null,null,array('intProgramID'=>$this->data['subject']['intProgramID'])));
       
        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(PDF_MARGIN_LEFT, .25, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //$pdf->SetAutoPageBreak(TRUE, 6);

       //font setting
        //$pdf->SetFont('calibril_0', '', 15, '', 'false');
        // set default font subsetting mode
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        
        $pdf->SetAutoPageBreak(false, PDF_MARGIN_FOOTER);
        
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        
        if($page == "front"){
            $pdf->AddPage();
            $ret = 0;
            $this->data['nothing_follows'] = true;
            if(count($students) > 40)
            {
                $ret = count($students) -40;
                $this->data['students'] = array_slice($students, 0, 40);
                $this->data['nothing_follows'] = false;
            }
            else
            {
                $this->data['students'] = $students;
            }

            $this->data['snum'] = 1;
            $html = $this->load->view('classlist_view',$this->data,true);



            //$html = $pdf->unhtmlentities($html);

            $pdf->writeHTML($html, true, false, true, false, '');
            if($ret > 0)
            {
                $this->data['nothing_follows'] = true;
                $pdf->AddPage();
                $this->data['students'] = array_slice($students, -$ret);
                $this->data['snum'] = 41;
                $html = $this->load->view('classlist_view',$this->data,true);
                $pdf->writeHTML($html, true, false, true, false, '');
            }
            
        }
        else
        {
            $passing =0;
            $incomplete =0;
            $ud = 0;
            $od = 0;
            $failing =0;
            $lineOfOne = 0;
            $lifeOfTwo = 0;
            $lineOfThree = 0;
            $totalUD = 0;
            $totalFailed = 0;
            $st = array();
            
            foreach($students as $student)
            { 
                $student['registered'] = $this->data_fetcher->checkRegistered($student['intID'],$this->data['classlist']['strAcademicYear']);
                $st[] = $student;
                $ave = getAve($student['floatPrelimGrade'],$student['floatMidtermGrade'],$student['floatFinalsGrade']);
                $eq = getEquivalent($ave);
                //$eq = getEquivalent($student['floatFinalGrade']);
                if($eq>=5 && $student['enumStatus'] == "drp")
                    $failing++;
                else if ($student['enumStatus'] == "drp")
                    $ud++;
                else if ($student['enumStatus'] == "odrp")
                    $od++;
                else if ($student['enumStatus'] == "inc")
                    $incomplete++;
                else
                    $passing++;
                    
                if($eq >=1.00 && $eq <= 1.75 && $student['enumStatus'] != "inc" && $student['enumStatus'] != "drp" && $student['enumStatus'] != "odrp")
                    $lineOfOne++;
                else if($eq >= 2.00 && $eq <= 2.75 && $student['enumStatus'] != "inc" && $student['enumStatus'] != "drp" && $student['enumStatus'] != "odrp")
                    $lifeOfTwo++;
                else if($eq == 3.00 && $student['enumStatus'] != "inc" && $student['enumStatus'] != "drp" && $student['enumStatus'] != "odrp")
                    $lineOfThree++;
                else if ($student['enumStatus'] == "act" && $student['strRemarks'] == "Failed")
                    $totalFailed++;
                else if ($student['enumStatus'] == "drp" && $student['strRemarks'] == "Failed(U.D.)")
                    $totalUD++;
                
                
            }
            $this->data['students'] = $st;
            $this->data['passing'] = $passing;
            $this->data['ud'] = $ud;
            $this->data['od'] = $od;
            $this->data['incomplete'] = $incomplete;
            $this->data['failing'] = $failing;
            $this->data['lineOfOne'] = $lineOfOne;
            $this->data['lineOfTwo'] = $lifeOfTwo;
            $this->data['lineOfThree'] = $lineOfThree;
            $this->data['totalFailed'] = $totalFailed;
            $this->data['totalUD'] = $totalUD;
            $this->data['total'] = $incomplete + $lineOfOne + $lifeOfTwo + $lineOfThree + $totalFailed + $totalUD + $od;
            
            $pdf->AddPage();
            $html = $this->load->view('classlist_view_back',$this->data,true);
            $pdf->writeHTML($html, true, false, true, false, '');

        }
        $pdf->Output("classlist.pdf", 'I');
    
    }
    
    function registration_viewer_account_data_print($orNumber,$studID)
    {
        if($this->is_admin() || $this->is_accounting()){
            
            $this->data['transactions'] = $this->data_fetcher->getTransactionsOR($orNumber);
            $transactions = $this->data['transactions'];
            $this->data['student'] = $this->data_fetcher->getStudent($studID);
            $student = $this->data['student'];
            //print_r($this->data['spouse']);
            tcpdf();
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(4.5,8.5), true, 'UTF-8', false, true);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle($transactions[0]['intORNumber'] . "-" . $student['strLastname'].', '.$student['strFirstname'].' '.$student['strMiddlename']);
    
    
        // set margins
        $pdf->SetMargins(.3, .1, .25);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //$pdf->SetAutoPageBreak(TRUE, 6);

       //font setting
        //$pdf->SetFont('calibril_0', '', 10, '', 'false');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
            

// Add a page
            // This method has several options, check the source code documentation for more information.

            $pdf->AddPage();

            $html = $this->load->view("print_or",$this->data,true);
            //$html = $pdf->unhtmlentities($html);

            $pdf->writeHTML($html, true, false, true, false, '');


            $pdf->Output("request-form.pdf", 'I');
        }
    }
    
    
    
    function student_viewer_blank_rog_print($id, $sem = null)
    {
        $this->load->view("print_view_student_blank_rog",$this->data);
    }
   
    function portal_login_data($id)
    {
        if($this->is_admin()){
            $student = $this->data_fetcher->getStudent($id);
            $this->data['student'] = $student;

            //print_r($this->data['spouse']);
            tcpdf();
            // create new PDF document
            //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            //$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
           // create new PDF document
            $pdf = new TCPDF("P", PDF_UNIT, array(8.5, 11), true, 'UTF-8', false);
            //$pdf = new TCPDF("P", PDF_UNIT, array(8.5, 13), false, 'UTF-8', false);
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle("Student Letter");
            $this->data['request_form'] = "";

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, 6);

           //font setting
            //$pdf->SetFont('calibril_0', '', 15, '', 'false');
            // set default font subsetting mode
            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.

            $pdf->SetAutoPageBreak(false, PDF_MARGIN_FOOTER);


            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            // Add a page
            // This method has several options, check the source code documentation for more information.

            $pdf->AddPage();

            $html = $this->load->view("student_letter",$this->data,true);
            //$html = $pdf->unhtmlentities($html);

            $pdf->writeHTML($html, true, false, true, false, '');


            $pdf->Output("request-form.pdf", 'I');
        }
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

    public function is_accounting()
    {
        $admin = $this->session->userdata('intUserLevel');
        if($admin == 6)
            return true;
        else
            return false;
        
    }

}