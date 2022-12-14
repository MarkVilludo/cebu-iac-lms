<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpexcel/PHPExcel.php');

class Excel extends CI_Controller {

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
        
        $this->data['img_dir'] = base_url()."assets/themes/".$theme."/images/";	
        $this->data['student_pics'] = base_url()."assets/photos/";
        $this->data['css_dir'] = base_url()."assets/themes/".$theme."/css/";
        $this->data['js_dir'] = base_url()."assets/themes/".$theme."/js/";
        $this->data['title'] = "CCT Unity";
        $this->load->library("email");	
        $this->load->helper("cms_form");	
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
        $this->data["subjects"] = $this->data_fetcher->fetch_table('tb_mas_subjects');
        $this->data["students"] = $this->data_fetcher->fetch_table('tb_mas_users',array('strLastname','asc'));
        $this->data["user"] = $this->session->all_userdata();
        $this->data['unread_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intRead'=>'0','intTrash'=>0,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['all_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intTrash'=>0,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['trashed_messages'] = $this->data_fetcher->count_table_contents('tb_mas_message_user',null,array('intTrash'=>1,'intFacultyID'=>$this->session->userdata('intID')));
        
        $this->data['sent_messages'] = $this->data_fetcher->count_sent_items($this->session->userdata('intID'));
    }
    public function index(){
        echo "php excel module";
    }
    public function download_classlists_archive()
    {
        $post = $this->input->post();
        $ids = $post['ids'];
        
        $sheet = 0;
        $title = date('Ymdhis').'-classlists';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle($title)
                                     ->setSubject("Classlist Download")
                                     ->setDescription("Classlist Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Classlist");
        foreach($ids as $id){
            $classlist = $this->data_fetcher->fetch_classlist_by_id(null,$id);
            $sy = $this->data_fetcher->get_sem_by_id($classlist['strAcademicYear']);
            $students = $this->data_fetcher->getClassListStudents($id);
            $subject = $this->data_fetcher->getSubjectNoCurr($classlist['intSubjectID']);

            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            if (PHP_SAPI == 'cli')
                die('This example should only be run from a Web Browser');


            
            
            
            $objPHPExcel->createSheet($sheet);

            


            $objPHPExcel->setActiveSheetIndex($sheet)
                        ->setCellValue('B1', $subject['strCode']." ".$classlist['strSection'])
                        ->setCellValue('C1', $sy['enumSem']." Sem")
                        ->setCellValue('D1', "A.Y. " . $sy['strYearStart']."-".$sy['strYearEnd']);

             $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                );

            // Add some datat
            $objPHPExcel->setActiveSheetIndex($sheet)
                        //->setCellValue('A2', 'Name')
                        ->setCellValue('A3', 'No.')
                        ->setCellValue('B3', 'Last Name')
                        ->setCellValue('C3', 'First Name')
                        ->setCellValue('D3', 'Middle Name')
                        ->setCellValue('E3', 'Program')
                        ->setCellValue('F3', 'Student Number')
                        ->setCellValue('G3', 'Final Grade')
                        ->setCellValue('H3', 'Remarks')
                        ->setCellValue('I3', 'GSuite Email')
                        ->setCellValue('J3', 'GMeet Display Name');

            $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray);
                unset($styleArray);

            $i = 4;
            $ctr = 1;
            foreach($students as $student)
            {
                // Add some datat
                $objPHPExcel->setActiveSheetIndex($sheet)
                        //->setCellValue('A'.$i, $student['strLastname'].", ".$student['strFirstname'])
                        ->setCellValue('A'.$i,$ctr)
                        ->setCellValue('B'.$i, $student['strLastname'])
                        ->setCellValue('C'.$i, $student['strFirstname'])
                        ->setCellValue('D'.$i, $student['strMiddlename'])    
                        ->setCellValue('E'.$i, $student['strProgramCode'])
                        ->setCellValue('F'.$i, $student['strStudentNumber'])
                        ->setCellValue('G'.$i, $student['floatFinalGrade'])
                        ->setCellValue('H'.$i, $student['strRemarks'])
                        ->setCellValue('I'.$i, $student['strGSuiteEmail'])
                        ->setCellValue('J'.$i, ucwords(strtolower($student['strFirstname'])) . " " . ucwords(strtolower($student['strLastname'])));

                  $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                );

                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($styleArray);
                unset($styleArray);

    //            if($student['strRemarks'] == "Failed")
    //                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(
    //                    array(
    //                        'fill' => array(
    //                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //                            'color' => array('rgb' => 'dd6666')
    //                        )
    //                    )
    //                );
                $i++;
                $ctr++;
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            
            $objPHPExcel->getActiveSheet()->setTitle($subject['strCode']." ".$classlist['strSection']);

            
            
             $sheet++;
            
        }
        
         // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
//        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        //$objWriter->save('assets/excel/'.date("Ymdhis").'-classlist.xlsx');
        //unlink('assets/excel/'.date("Ymdhis").'-classlist.xlsx');
        exit;
        
        
    }
    public function download_classlist($id)
    {
        $classlist = $this->data_fetcher->fetch_classlist_by_id(null,$id);
        $sy = $this->data_fetcher->get_sem_by_id($classlist['strAcademicYear']);
        $students = $this->data_fetcher->getClassListStudents($id);
        $subject = $this->data_fetcher->getSubjectNoCurr($classlist['intSubjectID']);
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle($subject['strCode']." ".$classlist['strSection'])
                                     ->setSubject("Classlist Download")
                                     ->setDescription("Classlist Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Classlist");


        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B1', $subject['strCode']." ".$classlist['strSection'])
                    ->setCellValue('C1', $sy['enumSem']." Sem")
                    ->setCellValue('D1', "A.Y. " . $sy['strYearStart']."-".$sy['strYearEnd']);
        
         $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
            );
        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    //->setCellValue('A2', 'Name')
                    ->setCellValue('A3', 'No.')
                    ->setCellValue('B3', 'Last Name')
                    ->setCellValue('C3', 'First Name')
                    ->setCellValue('D3', 'Middle Name')
                    ->setCellValue('E3', 'Program')
                    ->setCellValue('F3', 'Student Number')
                    ->setCellValue('G3', 'Final Grade')
                    ->setCellValue('H3', 'Remarks')
                    ->setCellValue('I3', 'GSuite Email')
                    ->setCellValue('J3', 'GMeet Display Name');
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray);
            unset($styleArray);
        
        $i = 4;
        $ctr = 1;
        foreach($students as $student)
        {
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    //->setCellValue('A'.$i, $student['strLastname'].", ".$student['strFirstname'])
                    ->setCellValue('A'.$i,$ctr)
                    ->setCellValue('B'.$i, $student['strLastname'])
                    ->setCellValue('C'.$i, $student['strFirstname'])
                    ->setCellValue('D'.$i, $student['strMiddlename'])    
                    ->setCellValue('E'.$i, $student['strProgramCode'])
                    ->setCellValue('F'.$i, $student['strStudentNumber'])
                    ->setCellValue('G'.$i, $student['floatFinalGrade'])
                    ->setCellValue('H'.$i, $student['strRemarks'])
                    ->setCellValue('I'.$i, $student['strGSuiteEmail'])
                    ->setCellValue('J'.$i, ucwords(strtolower($student['strFirstname'])) . " " . ucwords(strtolower($student['strLastname'])));

              $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
            );
        
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($styleArray);
            unset($styleArray);
            
//            if($student['strRemarks'] == "Failed")
//                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'dd6666')
//                        )
//                    )
//                );
            $i++;
            $ctr++;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        
        $objPHPExcel->getActiveSheet()->setTitle($subject['strCode']." ".$classlist['strSection']);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$subject['strCode']."-".$classlist['strSection'].'-classlist.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        //$objWriter->save('assets/excel/'.$subject['strCode']."-".$classlist['strSection'].'-classlist.xlsx');
        //unlink('assets/excel/'.$subject['strCode']."-".$classlist['strSection'].'-classlist.xlsx');
        exit;
    }
    
    public function download_schedules($id)
    {
        $sched = 
            $this->db->select('tb_mas_room_schedule.*,tb_mas_faculty.intID as facultyID,tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname')
                    ->from('tb_mas_room_schedule')
                    ->join('tb_mas_classlist','tb_mas_room_schedule.strScheduleCode = tb_mas_classlist.intID')
                    ->join('tb_mas_faculty','tb_mas_classlist.intFacultyID = tb_mas_faculty.intID')
                    ->where(array('intSem'=>$id))
                    ->get()
                    ->result_array();
            
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle('Schedules')
                                     ->setSubject("Schedule Download")
                                     ->setDescription("Schedule Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Classlist");


        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    //->setCellValue('A2', 'Name')
                    ->setCellValue('A1', 'Name')
                    ->setCellValue('B1', 'id')
                    ->setCellValue('C1', 'Day')
                    ->setCellValue('D1', 'Start')
                    ->setCellValue('E1', 'End');
        
        $i = 2;
        foreach($sched as $student)
        {
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    //->setCellValue('A'.$i, $student['strLastname'].", ".$student['strFirstname'])
                    ->setCellValue('A'.$i, $student['strFirstname']." ".$student['strLastname'])
                    ->setCellValue('B'.$i, $student['facultyID'])
                    ->setCellValue('C'.$i, $student['strDay'])
                    ->setCellValue('D'.$i, $student['dteStart'])
                    ->setCellValue('E'.$i, $student['dteEnd']);
            
            
//            if($student['strRemarks'] == "Failed")
//                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(
//                    array(
//                        'fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('rgb' => 'dd6666')
//                        )
//                    )
//                );
            $i++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        
        $objPHPExcel->getActiveSheet()->setTitle("Schedules");


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="schedules.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function download_repeated_subject_per_student($id){
        //$reg_status = $this->data_fetcher->getRegistrationStatus($id,$this->data['selected_ay']);
        $student = $this->data_fetcher->getStudent($id);        
        $sy = $this->data_fetcher->fetch_table('tb_mas_sy');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');
       
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'Student Number')
                    ->setCellValue('C1', 'Last Name')
                    ->setCellValue('D1', 'Firstname Name')
                    ->setCellValue('E1', 'Program Code')                    
                    ->setCellValue('F1', 'Subject Code')
                    ->setCellValue('G1', 'Sem')
                    ->setCellValue('H1', 'Year Start')
                    ->setCellValue('I1', 'Year End')                    
                    ->setCellValue('J1', 'Units')
                    ->setCellValue('K1', 'amount');
        $i = 2;       

        
        foreach($sy as $s)
        {
            $reg = $this->data_fetcher->getRegistrationInfo($student['intID'],$s['intID']);
            $tuition = $this->data_fetcher->getTuition($student['intID'],$s['intID'],$this->data['unit_fee'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$reg['enumScholarship']);                
                    
            if(count($tuition['repeated'])){                  
                foreach($tuition['repeated'] as $tData){
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$i, $student['intID'])
                            ->setCellValue('B'.$i, $student['strStudentNumber'])
                            ->setCellValue('C'.$i, $student['strLastname'])
                            ->setCellValue('D'.$i, $student['strFirstname'])
                            ->setCellValue('E'.$i, $student['strProgramCode'])
                            ->setCellValue('F'.$i, $tData['subjectCode'])
                            ->setCellValue('G'.$i, $s['enumSem'])
                            ->setCellValue('H'.$i, $s['strYearStart'])
                            ->setCellValue('I'.$i, $s['strYearEnd'])
                            ->setCellValue('J'.$i, $tData['strUnits'])
                            ->setCellValue('K'.$i, $tData['amount']);                            
                    
                    
                    $i++;
                }
            }
        }
        
        // $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($tuition))
        // ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);        
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Repeated');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');       
        header('Content-Disposition: attachment;filename="repeated_subjects.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    public function download_repeated_subjects($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0){
        
        $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,1,$sem);
        $sy = $this->data_fetcher->fetch_table('tb_mas_sy');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'Student Number')
                    ->setCellValue('C1', 'Last Name')
                    ->setCellValue('D1', 'Firstname Name')
                    ->setCellValue('E1', 'Program Code')                    
                    ->setCellValue('F1', 'Subject Code')
                    ->setCellValue('G1', 'Sem')
                    ->setCellValue('H1', 'Year Start')
                    ->setCellValue('I1', 'Year End')                    
                    ->setCellValue('J1', 'Units')
                    ->setCellValue('K1', 'amount');
        $i = 2;
        foreach($students as $student)
        {
            // Add some datat            
            //$newPass = password_hash($oldPass_unhash, PASSWORD_DEFAULT);
            
            foreach($sy as $s)
            {
                $reg = $this->data_fetcher->getRegistrationInfo($student['intID'],$s['intID']);
                $tuition = $this->data_fetcher->getTuition($student['intID'],$s['intID'],$this->data['unit_fee'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$reg['enumScholarship']);                
            

                foreach($tuition['repeated'] as $tData){
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$i, $student['intID'])
                            ->setCellValue('B'.$i, $student['strStudentNumber'])
                            ->setCellValue('C'.$i, $student['strLastname'])
                            ->setCellValue('D'.$i, $student['strFirstname'])
                            ->setCellValue('E'.$i, $student['strProgramCode'])
                            ->setCellValue('F'.$i, $tData['subjectCode'])
                            ->setCellValue('G'.$i, $s['enumSem'])
                            ->setCellValue('H'.$i, $s['strYearStart'])
                            ->setCellValue('I'.$i, $s['strYearEnd'])
                            ->setCellValue('J'.$i, $tData['strUnits'])
                            //->setCellValue('K'.$i, pw_unhash($student['strPass']))
                            //->setCellValue('L'.$i, password_hash($oldPass_unhash, PASSWORD_DEFAULT))
                            //->setCellValue('K'.$i, pw_hash(date("mdY",strtotime($student['dteBirthDate']))))
                            ->setCellValue('K'.$i, $tData['amount']);                            
                    
                    
                    $i++;
                }
            }
        }
        // $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($students))
        // ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
       // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        //$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);        
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        
        $objPHPExcel->getActiveSheet()->setTitle('Students Repeated');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="registered_students.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function download_students_new($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0, $studNumStart, $studNumEnd)
    {
        
        $students = $this->data_fetcher->getStudentsNew($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem, $studNumStart, $studNumEnd);
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        if($sem!=0){
             $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        }
        else
        {
            $active_sem = $this->data_fetcher->get_active_sem();

        }
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Student Number')
                    ->setCellValue('B1', 'Last Name')
                    ->setCellValue('C1', 'First Name')
                    ->setCellValue('D1', 'Middle Name')
                    ->setCellValue('E1', 'Gender')
                    ->setCellValue('F1', 'Course')
                    ->setCellValue('G1', 'Scholarship')
                    ->setCellValue('H1', 'Birthdate')
                    ->setCellValue('I1', 'Address')
                    ->setCellValue('K1', 'GSuiteEmail')
                    ->setCellValue('L1', 'intID');
        $i = 2;
        foreach($students as $student)
        {
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $student['strStudentNumber'])
                    ->setCellValue('B'.$i, $student['strLastname'])
                    ->setCellValue('C'.$i, $student['strFirstname'])
                    ->setCellValue('D'.$i, $student['strMiddlename'])
                    ->setCellValue('E'.$i, $student['enumGender'])
                    ->setCellValue('F'.$i, $student['strProgramCode'])
                    ->setCellValue('G'.$i, strtoupper($student['enumScholarship']))
                    ->setCellValue('H'.$i, date("m-d-Y", strtotime($student['dteBirthDate'])))
                    ->setCellValue('I'.$i, $student['strAddress'])
                    ->setCellValue('J'.$i, pw_hash(date("mdY",strtotime($student['dteBirthDate']))))
                    ->setCellValue('K'.$i, $student['strGSuiteEmail'])
                    ->setCellValue('L'.$i, $student['intID']); 
            
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($students))
        ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        if($course!=0 && $year!=0)
            $objPHPExcel->getActiveSheet()->setTitle($student['strProgramCode'], "-", $student['intStudentYear']);
        else
            $objPHPExcel->getActiveSheet()->setTitle('Students');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($registered != 0)
            header('Content-Disposition: attachment;filename="registered_students'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xlsx"');
        else
            header('Content-Disposition: attachment;filename="student_list.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    public function download_students($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0)
    {
        
        $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem);
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        if($sem!=0){
             $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        }
        else
        {
            $active_sem = $this->data_fetcher->get_active_sem();

        }
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Student Number')
                    ->setCellValue('B1', 'Last Name')
                    ->setCellValue('C1', 'First Name')
                    ->setCellValue('D1', 'Middle Name')
                    ->setCellValue('E1', 'Gender')
                    ->setCellValue('F1', 'Course')
                    ->setCellValue('G1', 'Year Level')
                    ->setCellValue('H1', 'Scholarship')
                    ->setCellValue('I1', 'Birthdate')
                    ->setCellValue('J1', 'Address')
                    //->setCellValue('K1', 'Password-unhashed')
                    //->setCellValue('L1', 'Password')
                    ->setCellValue('K1', 'GSuiteEmail')
                    ->setCellValue('L1', 'intID');
        $i = 2;
        foreach($students as $student)
        {
            // Add some datat
            $oldPass_unhash = pw_unhash($student['strPass']);
            //$newPass = password_hash($oldPass_unhash, PASSWORD_DEFAULT);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $student['strStudentNumber'])
                    ->setCellValue('B'.$i, $student['strLastname'])
                    ->setCellValue('C'.$i, $student['strFirstname'])
                    ->setCellValue('D'.$i, $student['strMiddlename'])
                    ->setCellValue('E'.$i, $student['enumGender'])
                    ->setCellValue('F'.$i, $student['strProgramCode'])
                    ->setCellValue('G'.$i, $student['intStudentYear'])
                    ->setCellValue('H'.$i, strtoupper($student['enumScholarship']))
                    ->setCellValue('I'.$i, date("m-d-Y", strtotime($student['dteBirthDate'])))
                    ->setCellValue('J'.$i, $student['strAddress'])
                    //->setCellValue('K'.$i, pw_unhash($student['strPass']))
                    //->setCellValue('L'.$i, password_hash($oldPass_unhash, PASSWORD_DEFAULT))
                    //->setCellValue('K'.$i, pw_hash(date("mdY",strtotime($student['dteBirthDate']))))
                    ->setCellValue('K'.$i, $student['strGSuiteEmail'])
                    ->setCellValue('L'.$i, $student['intID']); 
            
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($students))
        ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
       // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        //$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        if($course!=0 && $year!=0)
            $objPHPExcel->getActiveSheet()->setTitle($student['strProgramCode'], "-", $student['intStudentYear']);
        else
            $objPHPExcel->getActiveSheet()->setTitle('Students');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($registered != 0)
            header('Content-Disposition: attachment;filename="registered_students'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xlsx"');
        else
            header('Content-Disposition: attachment;filename="student_list.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function download_faculty()
    {
        
        $facultyLists = $this->data_fetcher->getFacultyList();
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Faculty List")
                                     ->setSubject("Faculty List Download")
                                     ->setDescription("Faculty List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Faculty List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Faculty ID')
                    ->setCellValue('B1', 'Username')
                    ->setCellValue('C1', 'Last Name')
                    ->setCellValue('D1', 'First Name')
                    ->setCellValue('E1', 'Middle Name')
                    ->setCellValue('F1', 'Email')
                    ->setCellValue('G1', 'Mobile Number')
                    ->setCellValue('H1', 'Adderss')
                    ->setCellValue('I1', 'Date Created')
                    ->setCellValue('J1', 'User Level')
                    ->setCellValue('K1', 'Password-unhashed')
                    ->setCellValue('L1', 'Password')
                    ->setCellValue('M1', 'School')
                    ->setCellValue('N1', 'Department');
        $i = 2;
        foreach($facultyLists as $facultyList)
        {
            // Add some datat
            //$oldPass_unhash = pw_unhash($facultyList['strPass']);
            //$newPass = password_hash($oldPass_unhash, PASSWORD_DEFAULT);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $facultyList['intID'])
                    ->setCellValue('B'.$i, $facultyList['strUsername'])
                    ->setCellValue('C'.$i, $facultyList['strLastname'])
                    ->setCellValue('D'.$i, $facultyList['strFirstname'])
                    ->setCellValue('E'.$i, $facultyList['strMiddlename'])
                    ->setCellValue('F'.$i, $facultyList['strEmail'])
                    ->setCellValue('G'.$i, $facultyList['strMobileNumber'])
                    ->setCellValue('H'.$i, $facultyList['strAddress'])
                    ->setCellValue('I'.$i, date("m-d-Y", strtotime($facultyList['dteCreated'])))
                    ->setCellValue('J'.$i, $facultyList['intUserLevel'])
                    ->setCellValue('K'.$i, pw_unhash($facultyList['strPass']))
                    ->setCellValue('L'.$i, password_hash(pw_unhash($facultyList['strPass']), PASSWORD_DEFAULT))
                    //->setCellValue('K'.$i, pw_hash(date("mdY",strtotime($student['dteBirthDate']))))
                    ->setCellValue('M'.$i, $facultyList['strSchool'])
                    ->setCellValue('N'.$i, $facultyList['strDepartment']); 
            
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($facultyList))
        ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Faculty');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // if($registered != 0)
        //     header('Content-Disposition: attachment;filename="registered_students'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xlsx"');
        //else
            header('Content-Disposition: attachment;filename="faculty_list.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    public function free_he_billing_details($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0)
    {
        
        $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem);
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        if($sem!=0){
             $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        }
        else
        {
            $active_sem = $this->data_fetcher->get_active_sem();

        }
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Free HE Billing Details")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        $s_array = array();
        
        foreach($students as $student)
        {
            
            $student['registration'] = $this->data_fetcher->getRegistrationInfo($student['intID'],$sem);
            $tuition = $this->data_fetcher->getTuition($student['intID'],$sem,$this->data['unit_fee'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$student['registration']['enumScholarship']);
            
            //$student['total'] = $tuition['athletic'] + $tuition['srf'] + $tuition['sfdf'] + $tuition['misc_fee']['Guidance Fee'] + $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'] + $tuition['lab'] + $tuition['misc_fee']['Medical and Dental Fee'] + $tuition['misc_fee']['Entrance Exam Fee'] + $tuition['misc_fee']['Registration'] + $tuition['id_fee'] + $tuition['misc_fee']['Library Fee'];
            
            $student['total'] = $tuition['athletic'] + $tuition['srf'] + $tuition['sfdf'] + $tuition['misc_fee']['Guidance Fee'] + $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'] + $tuition['lab'] + $tuition['misc_fee']['Medical and Dental Fee'] + $tuition['misc_fee']['Registration'] + $tuition['id_fee'] + $tuition['misc_fee']['Library Fee'];
            
            $exam_fee =$tuition['misc_fee']['Entrance Exam Fee'];
            $student['efee'] = $exam_fee;
            $student['tuition'] = $tuition;
            $student['has_nstp'] = true;
            
            $records = $this->data_fetcher->checkClasslistStudentNSTP($student['intID'],$sem);
            if(empty($records))
                $student['has_nstp'] = false;
            
            $s_array[] = $student;
        }                
        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '5-digit Control Number')
                    ->setCellValue('B1', 'Student Number')
                    ->setCellValue('C1', 'Learner\'s Reference Number')
                    ->setCellValue('D1', 'Last Name')
                    ->setCellValue('E1', 'First Name')
                    ->setCellValue('F1', 'Middle Initial')
                    ->setCellValue('G1', 'Sex at Birth (M/F)')
                    ->setCellValue('H1', 'Degree/Program')
                    ->setCellValue('I1', 'Year Level')
                    ->setCellValue('J1', 'Scholarship')
                    ->setCellValue('K1', 'Birthdate')
                    ->setCellValue('L1', 'Zip Code')
                    ->setCellValue('M1', 'Email Address')
                    ->setCellValue('N1', 'Phone Number')
                    ->setCellValue('O1', 'Academic Units Enrolled (credit and non-credit courses)')
                    ->setCellValue('P1', 'Academic Units of NSTP Enrolled (credit and non-credit courses)')
                    ->setCellValue('Q1', '')
                    ->setCellValue('R1', 'Tuition Fee Based on enrolled academic units')
                    ->setCellValue('S1', 'Tuition Fee Based on enrolled NSTP units')
                    ->setCellValue('T1', '')
                    ->setCellValue('U1', 'Address');
        
        $i = 2;
        
        foreach($s_array as $student)
        {
            $registration = $student['registration'];
            $tuition = $student['tuition'];
            $total = $student['total'];
            $units = $tuition['tuition']/$this->data['unit_fee'];
            
            if($student['has_nstp']){
                $units -= 3;
                $nstp_units = 3;
                $nstp_fee = $this->data['unit_fee'] * 3;
                $tuition['tuition'] -= $nstp_fee; 
            }
            else{
                $nstp_units = 0;
                $nstp_fee = 0;
            }
            
            $middle_initial = isset($student['strMiddlename'][0])?strtoupper($student['strMiddlename'][0]).".":'';
            
            $sex = isset($student['enumGender'][0])?strtoupper($student['enumGender'][0]):'';
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, '')
                    ->setCellValue('B'.$i, $student['strStudentNumber'])
                    ->setCellValue('C'.$i, $student['strLRN'])
                    ->setCellValue('D'.$i, $student['strLastname'])
                    ->setCellValue('E'.$i, $student['strFirstname'])
                    ->setCellValue('F'.$i, $middle_initial)
                    ->setCellValue('G'.$i, $sex)
                    ->setCellValue('H'.$i, $student['strProgramCode'])
                    ->setCellValue('I'.$i, $student['intStudentYear'])
                    ->setCellValue('J'.$i, strtoupper($student['enumScholarship']))
                    ->setCellValue('K'.$i, date("m/d/Y",strtotime($student['dteBirthDate'])))
                    ->setCellValue('L'.$i, $student['strZipCode'])
                    ->setCellValue('M'.$i, $student['strEmail'])
                    ->setCellValue('N'.$i, $student['strMobileNumber'])
                    ->setCellValue('O'.$i, $units)
                    ->setCellValue('P'.$i, $nstp_units)
                    ->setCellValue('Q'.$i, '')
                    ->setCellValue('R'.$i, $tuition['tuition'])
                    ->setCellValue('S'.$i, $nstp_fee)
                    ->setCellValue('T'.$i, '')
                    ->setCellValue('U'.$i, $student['strAddress']);
                    
            
            
            $i++;
        }
       

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(35);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Form 2');
        
        //SHEET 2
        $objPHPExcel->createSheet(1);
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('A1', '5-digit Control Number')
                    ->setCellValue('B1', 'Student Number')
                    ->setCellValue('C1', 'Learner\'s Reference Number')
                    ->setCellValue('D1', 'Last Name')
                    ->setCellValue('E1', 'First Name')
                    ->setCellValue('F1', 'Middle Initial')
                    ->setCellValue('G1', 'Degree Program')
                    ->setCellValue('H1', 'Athletic Fees')
                    ->setCellValue('I1', 'Computer Fees')
                    ->setCellValue('J1', 'Cultural Fees')
                    ->setCellValue('K1', 'Development Fees')
                    ->setCellValue('L1', 'Guidance Fees')
                    ->setCellValue('M1', 'Handbook Publication Fees')
                    ->setCellValue('N1', 'Publication Fees')
                    ->setCellValue('O1', 'Laboratory Fees')
                    ->setCellValue('P1', 'Library Fee')
                    ->setCellValue('Q1', 'Medical and Dental Fees')
                    ->setCellValue('R1', 'Registration Fees')
                    ->setCellValue('S1', 'School ID Fees')
                    ->setCellValue('T1', 'TOTAL OSF (A)');
        
        $i = 2;
        foreach($s_array as $student)
        {
            $registration = $student['registration'];
            $tuition = $student['tuition'];
            $total = $student['total'];
                
            $middle_initial = isset($student['strMiddlename'][0])?strtoupper($student['strMiddlename'][0])."":'';
            $sex = isset($student['enumGender'][0])?strtoupper($student['enumGender'][0]):'';
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('A'.$i, '')
                    ->setCellValue('B'.$i, $student['strStudentNumber'])
                    ->setCellValue('C'.$i, $student['strLRN'])
                    ->setCellValue('D'.$i, $student['strLastname'])
                    ->setCellValue('E'.$i, $student['strFirstname'])
                    ->setCellValue('F'.$i, $middle_initial)
                    ->setCellValue('G'.$i, $student['strProgramCode'])
                    ->setCellValue('H'.$i, $tuition['athletic'])
                    ->setCellValue('I'.$i, '')
                    ->setCellValue('J'.$i, $tuition['srf'])
                    ->setCellValue('K'.$i, $tuition['sfdf'])
                    ->setCellValue('L'.$i, $tuition['misc_fee']['Guidance Fee'])
                    ->setCellValue('M'.$i, $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'])
                    ->setCellValue('N'.$i, '')
                    ->setCellValue('O'.$i, $tuition['lab'])
                    ->setCellValue('P'.$i, $tuition['misc_fee']['Library Fee'])
                    ->setCellValue('Q'.$i, $tuition['misc_fee']['Medical and Dental Fee'])
                    ->setCellValue('R'.$i, $tuition['misc_fee']['Registration'])
                    ->setCellValue('S'.$i, $tuition['id_fee'])
                    ->setCellValue('T'.$i, $total);
                    
            
            
            $i++;
        }
       

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Form 2a');
        
        
        //SHEET 2
        $objPHPExcel->createSheet(2);
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue('A1', '5-digit Control Number')
                    ->setCellValue('B1', 'Student Number')
                    ->setCellValue('C1', 'Learner\'s Reference Number')
                    ->setCellValue('D1', 'Last Name')
                    ->setCellValue('E1', 'First Name')
                    ->setCellValue('F1', 'Middle Initial')
                    ->setCellValue('G1', 'Sex at Birth (M/F)')
                    ->setCellValue('H1', 'Birthdate')
                    ->setCellValue('I1', 'Degree/Program')
                    ->setCellValue('J1', 'Year Level')
                    ->setCellValue('K1', 'Zip Code')
                    ->setCellValue('L1', 'Email Address')
                    ->setCellValue('M1', 'Phone Number')
                    ->setCellValue('N1', 'Admission Fee')
                    ->setCellValue('O1', 'Entrance Fee')
                    ->setCellValue('P1', 'TOTAL OSF (B)');
        
        $i = 2;
        foreach($s_array as $student)
        {
            $registration = $student['registration'];
            $tuition = $student['tuition'];
            $total = $student['total'];
                
            $middle_initial = isset($student['strMiddlename'][0])?strtoupper($student['strMiddlename'][0]).".":'';
            $sex = isset($student['enumGender'][0])?strtoupper($student['enumGender'][0]):'';
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(2)
                    ->setCellValue('A'.$i, '')
                    ->setCellValue('B'.$i, $student['strStudentNumber'])
                    ->setCellValue('C'.$i, $student['strLRN'])
                    ->setCellValue('D'.$i, $student['strLastname'])
                    ->setCellValue('E'.$i, $student['strFirstname'])
                    ->setCellValue('F'.$i, $middle_initial)
                    ->setCellValue('G'.$i, $sex)
                    ->setCellValue('H'.$i, date("m/d/Y",strtotime($student['dteBirthDate'])))
                    ->setCellValue('I'.$i, $student['strProgramCode'])
                    ->setCellValue('J'.$i, $student['intStudentYear'])
                    ->setCellValue('K'.$i, $student['strZipCode'])
                    ->setCellValue('L'.$i, $student['strEmail'])
                    ->setCellValue('M'.$i, $student['strMobileNumber'])
                    ->setCellValue('N'.$i, '')
                    ->setCellValue('O'.$i, $student['efee'])
                    ->setCellValue('P'.$i, $student['efee']);
                    
            
            
            $i++;
        }
       

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Form 2b');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($registered != 0)
            header('Content-Disposition: attachment;filename="free-he-billing-details'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xls"');
        else
            header('Content-Disposition: attachment;filename="student_list.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter->save('php://output');
        exit;
    }
    
     public function download_cor_data($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0)
    {
        
       $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem);
       
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("COR Data Elements")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some data
        
        if($sem == 0 )
        {
            $s = $this->data_fetcher->get_active_sem();
            $sem = $s['intID'];
        }
        
        $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        
        
            
        $objPHPExcel->setActiveSheetIndex(0)
            
                    ->setCellValue('A1', '5-digit Control Number')
                    ->setCellValue('B1', 'Student ID Number')
                    ->setCellValue('C1', 'Learner\'s Reference Number')
                    ->setCellValue('D1', 'Last Name')
                    ->setCellValue('E1', 'First Name')
                    ->setCellValue('F1', 'Middle Name')
                    ->setCellValue('G1', 'Degree Program / Course')
                    ->setCellValue('H1', 'Year Level')
                    ->setCellValue('I1', 'Subject Code')
                    ->setCellValue('J1', 'Subject Name')
                    ->setCellValue('K1', 'Number of Units')
                    ->setCellValue('L1', 'Subject Cost per unit')
                    ->setCellValue('M1', 'Tuition Cost per subject')
                    ->setCellValue('N1', 'Entrance Exam Fee')
                    ->setCellValue('O1', 'Medical Screening')
                    ->setCellValue('P1', 'Documentary Fee')
                    ->setCellValue('Q1', 'Personality/Psychological Test')
                    ->setCellValue('R1', 'Total Admission Fee')
                    ->setCellValue('S1', 'Use of Sports Facilities and Equipment')
                    ->setCellValue('T1', 'Participation')
                    ->setCellValue('U1', 'College and Universities')
                    ->setCellValue('V1', 'Total Athletic Fees')
                    ->setCellValue('W1', 'Access and Use of ICT Services')
                    ->setCellValue('X1', 'Computer Laboratory Fee')
                    ->setCellValue('Y1', 'Total Computer Fees')
                    ->setCellValue('Z1', 'Socio-cultural Activities')
                    ->setCellValue('AA1', 'Leadership Training')
                    ->setCellValue('AB1', 'Off-campus experiental Learning')
                    ->setCellValue('AC1', 'Students\' participation')
                    ->setCellValue('AD1', 'Student Publication/newsletter')
                    ->setCellValue('AE1', 'Life-long Learning Activities')
                    ->setCellValue('AF1', 'Spiritual, Social')
                    ->setCellValue('AG1', 'Bridging remedial programs')
                    ->setCellValue('AH1', 'Total Development Fees')
                    ->setCellValue('AI1', 'Entrance Fee')
                    ->setCellValue('AJ1', 'Student training and seminars')
                    ->setCellValue('AK1', 'Career guidance and counseling')
                    ->setCellValue('AL1', 'General student counseling')
                    ->setCellValue('AM1', 'Psychological Testing')
                    ->setCellValue('AN1', 'Career Assessment')
                    ->setCellValue('AO1', 'Career Development')
                    ->setCellValue('AP1', 'Employment Placement Services')
                    ->setCellValue('AQ1', 'Total Guidance Fee')
                    ->setCellValue('AR1', 'Handbook Fees')
                    ->setCellValue('AS1', 'Laboratory Fees')
                    ->setCellValue('AT1', 'Use of library services')
                    ->setCellValue('AU1', 'License Fee to cover')
                    ->setCellValue('AV1', 'Total Library Fees')
                    ->setCellValue('AW1', 'Mental Health')
                    ->setCellValue('AX1', 'Dental Health')
                    ->setCellValue('AY1', 'Student Insurance')
                    ->setCellValue('AZ1', 'Total Medical and Dental Fees')
                    ->setCellValue('BA1', 'Registration Fees')
                    ->setCellValue('BB1', 'School ID Fees')
                    ->setCellValue('BC1', 'Total Tuition')
                    ->setCellValue('BD1', 'Total Tuition and Other School Fees (TOSF 1)')
                    ->setCellValue('BE1', 'All Other School Fees (AOSF)')
                    ->setCellValue('BF1', 'Total Amount of Fees (TOSF 2 = (TOSF 1 + AOSF))');   
        $i = 2;
        //print_r($students);
        //die();
        foreach($students as $student)
        {
            
            $student['registration'] = $this->data_fetcher->getRegistrationInfo($student['intID'],$sem);
            $tuition = $this->data_fetcher->getTuition($student['intID'],$sem,$this->data['unit_fee'],$this->data['misc_fee'],$this->data['lab_fee'],$this->data['athletic'],$this->data['id_fee'],$this->data['srf'],$this->data['sfdf'],$this->data['csg'],$student['registration']['enumScholarship']);
            
            //$student['total'] = $tuition['athletic'] + $tuition['srf'] + $tuition['sfdf'] + $tuition['misc_fee']['Guidance Fee'] + $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'] + $tuition['lab'] + $tuition['misc_fee']['Medical and Dental Fee'] + $tuition['misc_fee']['Entrance Exam Fee'] + $tuition['misc_fee']['Registration'] + $tuition['id_fee'] + $tuition['misc_fee']['Library Fee'];
            
            $student['total'] = $tuition['athletic'] + $tuition['srf'] + $tuition['sfdf'] + $tuition['misc_fee']['Guidance Fee'] + $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'] + $tuition['lab'] + $tuition['misc_fee']['Medical and Dental Fee'] + $tuition['misc_fee']['Registration'] + $tuition['id_fee'] + $tuition['misc_fee']['Library Fee'] + $tuition['misc_fee']['Entrance Exam Fee'];
            
            $exam_fee =$tuition['misc_fee']['Entrance Exam Fee'];
            
            $cl2 = $this->data_fetcher->getClassListStudentsSt($student['intID'],$sem);
            
            $middle_initial = isset($student['strMiddlename'][0])?strtoupper($student['strMiddlename'][0])."":'';
            // Add some datat
            $k = 0;
            foreach ($cl2 as $classlists) 
            {
                
            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, '');
                
            if($k==0){    
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, $student['strStudentNumber'])
                    ->setCellValue('C'.$i, $student['strLRN'])
                    ->setCellValue('D'.$i, $student['strLastname'])
                    ->setCellValue('E'.$i, $student['strFirstname'])
                    ->setCellValue('F'.$i, $middle_initial)
                    ->setCellValue('G'.$i, $student['strProgramCode'])
                    ->setCellValue('H'.$i, $student['intStudentYear']);
            }
            else
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, '')
                    ->setCellValue('C'.$i, '')
                    ->setCellValue('D'.$i, '')
                    ->setCellValue('E'.$i, '')
                    ->setCellValue('F'.$i, '')
                    ->setCellValue('G'.$i, '')
                    ->setCellValue('H'.$i, '');
            }
                
                
//                    ->setCellValue('H'.$i, $tuition['athletic'])
//                    ->setCellValue('I'.$i, '')
//                    ->setCellValue('J'.$i, $tuition['srf'])
//                    ->setCellValue('K'.$i, $tuition['sfdf'])
//                    ->setCellValue('L'.$i, $tuition['misc_fee']['Guidance Fee'])
//                    ->setCellValue('M'.$i, $tuition['csg']['student_handbook']+$tuition['csg']['student_publication'])
//                    ->setCellValue('N'.$i, $tuition['lab'])
//                    ->setCellValue('O'.$i, $tuition['misc_fee']['Library Fee'])
//                    ->setCellValue('P'.$i, $tuition['misc_fee']['Medical and Dental Fee'])
//                    ->setCellValue('Q'.$i, $tuition['misc_fee']['Registration'])
//                    ->setCellValue('R'.$i, $tuition['id_fee'])
//                    ->setCellValue('S'.$i, $total);
            $objPHPExcel->setActiveSheetIndex(0)
                    
                    ->setCellValue('I'.$i, $classlists['strCode'])
                    ->setCellValue('J'.$i, $classlists['strDescription'])
                    ->setCellValue('K'.$i, $classlists['strUnits'])
                    ->setCellValue('L'.$i, '175')
                    ->setCellValue('M'.$i, $classlists['strUnits'] * 175)
                    ->setCellValue('N'.$i, '')
                    ->setCellValue('O'.$i, '')
                    ->setCellValue('P'.$i, '')
                    ->setCellValue('Q'.$i, '');
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('R'.$i, $exam_fee);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('R'.$i, '');
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('S'.$i, '')
                    ->setCellValue('T'.$i, '')
                    ->setCellValue('U'.$i, '');
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('V'.$i, $tuition['athletic']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('V'.$i, '');
                
                $objPHPExcel->setActiveSheetIndex(0)                
                    ->setCellValue('W'.$i, '')
                    ->setCellValue('X'.$i, '');
            
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('Y'.$i, $tuition['lab']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('Y'.$i, '');
            
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('Z'.$i, $tuition['srf']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('Z'.$i, '');
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AA'.$i, '')
                    ->setCellValue('AB'.$i, '')
                    ->setCellValue('AC'.$i, '')
                    ->setCellValue('AD'.$i, '')
                    ->setCellValue('AE'.$i, '')
                    ->setCellValue('AF'.$i, '')
                    ->setCellValue('AG'.$i, '');
                
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0) 
                    ->setCellValue('AH'.$i, $tuition['sfdf']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AH'.$i, '');
                
                 $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AI'.$i, '')
                    ->setCellValue('AJ'.$i, '')
                    ->setCellValue('AK'.$i, '')
                    ->setCellValue('AL'.$i, '')
                    ->setCellValue('AM'.$i, '')
                    ->setCellValue('AN'.$i, '')
                    ->setCellValue('AO'.$i, '')
                    ->setCellValue('AP'.$i, '');
                     
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AQ'.$i, $tuition['misc_fee']['Guidance Fee']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AQ'.$i, '');
            
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AR'.$i, $tuition['csg']['student_handbook']+$tuition['csg']['student_publication']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AR'.$i, '');
                
                 $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AS'.$i, '')
                    ->setCellValue('AT'.$i, '')
                    ->setCellValue('AU'.$i, '');
                
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AV'.$i, $tuition['misc_fee']['Library Fee']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AV'.$i, '');
                
                 $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AW'.$i, '')
                    ->setCellValue('AX'.$i, '')
                    ->setCellValue('AY'.$i, '');
                
            if($k==0)
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AZ'.$i, $tuition['misc_fee']['Medical and Dental Fee'])
                    ->setCellValue('BA'.$i, $tuition['misc_fee']['Registration'])
                    ->setCellValue('BB'.$i, $tuition['id_fee'])
                    ->setCellValue('BC'.$i, $tuition['tuition'])
                    ->setCellValue('BD'.$i, $tuition['tuition']+$student['total'])
                    ->setCellValue('BE'.$i, '')
                    ->setCellValue('BF'.$i, $tuition['tuition']+$student['total']);
            else
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AZ'.$i, '')
                    ->setCellValue('BA'.$i, '')
                    ->setCellValue('BB'.$i, '')
                    ->setCellValue('BC'.$i, '')
                    ->setCellValue('BD'.$i, '')
                    ->setCellValue('BE'.$i, '')
                    ->setCellValue('BF'.$i, '');
                
                $i++;
                $k++;
            }
            
            
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        
        
        
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

         // Rename worksheet
       
            $objPHPExcel->getActiveSheet()->setTitle('COR Data Elements');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($registered != 0)
            header('Content-Disposition: attachment;filename="COR_Data_Elements'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xlsx"');
        else
            header('Content-Disposition: attachment;filename="COR_Data_Elements.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    
    public function download_students_with_grades($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem = 0)
    {
        
        $students = $this->data_fetcher->getStudents($course,$regular,$year,$gender,$graduate,$scholarship,$registered,$sem);
       //$student['intStudentYear'])
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        
        if($sem == 0 )
        {
            $s = $this->data_fetcher->get_active_sem();
            $sem = $s['intID'];
        }
        
        $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', switch_num($year)." Year ".$active_sem['enumSem']." Sem ".$active_sem['strYearStart']." - ".$active_sem['strYearEnd']);
        
            
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'Student Number')
                    ->setCellValue('B2', 'Last Name')
                    ->setCellValue('C2', 'First Name')
                    ->setCellValue('D2', 'Middle Name')
                    ->setCellValue('E2', 'Gender')
                    ->setCellValue('F2', 'Year Level')
                    ->setCellValue('G2', 'Program')
                    ->setCellValue('H2', 'GSuite Email');
                    
        
        $i = 3;
        
        foreach($students as $student)
        {
            $cl = $this->data_fetcher->getClassListStudentsSt($student['intID'],$sem);
            
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $student['strStudentNumber'])
                    ->setCellValue('B'.$i, $student['strLastname'])
                    ->setCellValue('C'.$i, $student['strFirstname'])
                    ->setCellValue('D'.$i, $student['strMiddlename'])
                    ->setCellValue('E'.$i, $student['enumGender'])
                    ->setCellValue('F'.$i, $student['intStudentYear'])
                    ->setCellValue('G'.$i, $student['strProgramCode'])
                    ->setCellValue('H'.$i, $student['strGSuiteEmail']);
                    
            $col = 'I';
            foreach($cl as $c)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col."2", "Course Code");
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col.$i, $c['strCode']);
                
                $col++;
                
                 $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col."2", "Course Title");
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col.$i, $c['strDescription']);
                
                $col++;
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col.$i, $c['strUnits']);
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col."2", "Units");
                
                $col++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col."2", "FacultyID");
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col.$i, $c['facID']);
                
                $col++;
            }
            
            $i++;
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        
        
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

         // Rename worksheet
        if($course!=0 && $year!=0)
            $objPHPExcel->getActiveSheet()->setTitle($student['strProgramCode'], "-", $student['intStudentYear']);
        else
            $objPHPExcel->getActiveSheet()->setTitle('Students');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($registered != 0)
            header('Content-Disposition: attachment;filename="enrolment-list-'.$active_sem['enumSem'].'sem'."-".$active_sem['strYearStart']."-".$active_sem['strYearEnd'].'.xls"');
        else
            header('Content-Disposition: attachment;filename="student_list.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        
        $objWriter->save('php://output');
        exit;
    }
    
    
    
    public function download_transactions($start=null,$end=null)
    {
        $trans = $this->data_fetcher->fetch_transactions($start,$end);
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Transactions Table")
                                     ->setSubject("Transactions Table Download")
                                     ->setDescription("Transactions Table Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Transactions Table");

        
        
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'transactions '.$start."-".$end);
        
        
         // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ORNumber')
                    ->setCellValue('B1', 'Transaction Type')
                    ->setCellValue('C1', 'Date Paid')
                    ->setCellValue('D1', 'Payee')
                    ->setCellValue('E1', 'Amount Paid');
        
        $i = 2;
        foreach($trans as $tran)
        {
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $tran['intORNumber'])
                    ->setCellValue('B'.$i, $tran['strTransactionType'])
                    ->setCellValue('C'.$i, date("M j,Y",strtotime($tran['dtePaid'])))
                    ->setCellValue('D'.$i, $tran['strLastname'].", ".$tran['strFirstname'])
                    ->setCellValue('E'.$i, "P".$tran['intAmountPaid']);
                    
            $i++;
        }
        
        
          // Rename worksheet
        
        $objPHPExcel->getActiveSheet()->setTitle('Transactions');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
       
        header('Content-Disposition: attachment;filename="transactions.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    
    
    }
    
    function upload_classlist()
    {
       
        $post = $this->input->post();
    
        $config['upload_path'] = './assets/excel';
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size']	= '4096';
        $config['file_name'] = 'temp';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload("excelupload"))
        {
         $this->session->set_flashdata('message',$this->upload->display_errors());
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file = $this->upload->data();
            $inputFileName = $file['full_path'];

            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }

            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            
            
             $this->data_poster->deleteFromClassList($post['intClasslistID']);
            //  Loop through each row of the worksheet in turn
            for ($row = 3; $row <= $highestRow; $row++){ 
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                
               
                //  Insert row data array into your database of choice here
                foreach($rowData as $d)
                {
                    if($d[0] == "" || $d[1] == "" || $d[2] == "" || $d[4] == "" || $d[5] == "")
                        break;
                    
                    $student = $this->data_fetcher->getStudentStudentNumber($d[3]);
                    
                    if(empty($student))
                    {
                        $student = $this->data_fetcher->getStudentByName($d[0],$d[1],$d[2]);   
                    }
                    
                    if(!empty($student)){
                        $data_s['intStudentID'] = $student['intID'];
                        $data_s['intClasslistID'] = $post['intClasslistID'];
                        $data_s['strUnits'] = $post['strUnits'];
                        $data_s['floatFinalGrade'] = $d[4];
                        $data_s['strRemarks'] = $d[5];
                        $this->data_poster->post_data('tb_mas_classlist_student',$data_s);
                    }
                }
                   
            }
            unlink($inputFileName);
            
            
        }
        redirect(base_url().'unity/classlist_viewer/'.$post['intClasslistID']);
    }
    public function download_applicants($course = 0,$appdate=0,$gender = 0,$sem = 0)
    {
        
        $applicants = $this->data_fetcher->getApplicantsExcel($course,$appdate,$gender,$sem);
        
//        $applicants['courseCode1'] = $this->data_fetcher->getCourseCode($applicants['enumCourse1']);
//        $applicants['courseCode2'] = $this->data_fetcher->getCourseCode($applicants['enumCourse2']);
//        $course3 = $this->data_fetcher->getCourseCode($applicants['enumCourse3']);
//        $applicants['courseCode3'] = ($course3=="")?'None':$course3;

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        if($sem!=0){
             $active_sem = $this->data_fetcher->get_sem_by_id($sem);
        }
        else
        {
            $active_sem = $this->data_fetcher->get_active_sem();

        }
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Jec Castillo")
                                     ->setLastModifiedBy("Jec Castillo")
                                     ->setTitle("Student List")
                                     ->setSubject("Student List Download")
                                     ->setDescription("Student List Download.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Student List");

        
        // Add some datat
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Applicant Number')
                    ->setCellValue('B1', 'Last Name')
                    ->setCellValue('C1', 'First Name')
                    ->setCellValue('D1', 'Middle Name')
                    ->setCellValue('E1', 'LRN')
                    ->setCellValue('F1', 'Email Address')
                    ->setCellValue('G1', 'Phone Number')
                    ->setCellValue('H1', '1st Choice Course')
                    ->setCellValue('I1', '2nd Choice Course')
                    ->setCellValue('J1', '3rd Choice Course')
                    ->setCellValue('K1', 'Province')
                    ->setCellValue('L1', 'City/Municipality')
                    ->setCellValue('M1', 'Barangay')
                    ->setCellValue('N1', 'Home Address')
                    ->setCellValue('O1', 'Last School Attended')
                    ->setCellValue('P1', 'Birthdate')
                    ->setCellValue('Q1', 'Gender')
                    ->setCellValue('R1', 'Civil Status')
                    ->setCellValue('S1', 'Father\'s Name')
                    ->setCellValue('T1', 'Mother\'s Name')
                    ->setCellValue('U1', 'Spouse')
                    ->setCellValue('V1', 'Religion')
                    ->setCellValue('W1', 'Application Date/Time')
                    ->setCellValue('X1', 'Date of Exam');
            
            
                    
        $i = 2;
        foreach($applicants as $applicant)
        {
            // Add some datat
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $applicant['strAppNumber'])
                    ->setCellValue('B'.$i, strtoupper($applicant['strLastname']))
                    ->setCellValue('C'.$i, strtoupper($applicant['strFirstname']))
                    ->setCellValue('D'.$i, strtoupper($applicant['strMiddlename']))
                    ->setCellValue('E'.$i, $applicant['strAppLRN'])
                    ->setCellValue('F'.$i, $applicant['strAppEmail'])
                    ->setCellValue('G'.$i, $applicant['strAppPhoneNumber'])
                    ->setCellValue('H'.$i, $applicant['enumCourse1'])
                    ->setCellValue('I'.$i, $applicant['enumCourse2'])
                    ->setCellValue('J'.$i, $applicant['enumCourse3'])
//                    ->setCellValue('H'.$i, $applicant['courseCode1'])
//                    ->setCellValue('I'.$i, $applicant['courseCode2'])
//                    ->setCellValue('J'.$i, $applicant['courseCode3'])
                    ->setCellValue('K'.$i, ucwords(strtolower($applicant['provDesc'])))
                    ->setCellValue('L'.$i, ucwords(strtolower($applicant['citymunDesc'])))
                    ->setCellValue('M'.$i, ucwords(strtolower($applicant['brgyDesc'])))
                    ->setCellValue('N'.$i, $applicant['strAppAdress'])
                    ->setCellValue('O'.$i, $applicant['strAppLastSchool'])
                    ->setCellValue('P'.$i, date("m-d-Y", strtotime($applicant['dteAppBirthdate'])))
                    ->setCellValue('Q'.$i, $applicant['strAppGender'])
                    ->setCellValue('R'.$i, $applicant['strAppCivilStatus'])
                    ->setCellValue('S'.$i, $applicant['strAppFather'])
                    ->setCellValue('T'.$i, $applicant['strAppMother'])  
                    ->setCellValue('U'.$i, $applicant['strAppSpouse'])
                    ->setCellValue('V'.$i, $applicant['strAppReligion'])
                    ->setCellValue('W'.$i, $applicant['strAppDate'])
                    ->setCellValue('X'.$i, $applicant['dteScheduleExam']);
            
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('I2:I'.count($applicant))
        ->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
        
        // Miscellaneous glyphs, UTF-8
        //$objPHPExcel->setActiveSheetIndex(0)
        //          ->setCellValue('A4', 'Miscellaneous glyphs')
        //          ->setCellValue('A5', '??????????????????????????????????');

        // Rename worksheet
//        if($course!=0 && $year!=0)
//            $objPHPExcel->getActiveSheet()->setTitle(applicant['strProgramCode'], "-", $student['intStudentYear']);
        //else
        $objPHPExcel->getActiveSheet()->setTitle('Applicants');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client???s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //if($registered != 0)
            header('Content-Disposition: attachment;filename="list_of_applicants_'.$active_sem['enumSem'].'sem'.$active_sem['strYearStart'].$active_sem['strYearEnd'].'.xlsx"');
        //else
           // header('Content-Disposition: attachment;filename="applicants.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
}