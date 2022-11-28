<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_fetcher extends CI_Model {
	
	
	function fetch_table($table,$order=null,$limit=null,$where=null)
	{				
		
		if($order!=null)
			$this->db->order_by($order[0],$order[1]);
		elseif($table == 'tb_mas_content')
			$this->db->order_by('dteStart','desc');
        elseif($table == 'tb_mas_programs')
			$this->db->where('enumEnabled !=',0);
        if($limit!=null)
			$this->db->limit($limit);
			
		if($where!=null)
			$this->db->where($where);
		
		$data =  $this->db
						->get($table)
						->result_array();
						
		return $data;
						
	}

    function fetch_single_entry($table,$id,$label = "intID"){
        return $this->db->where(array($label => $id))->get($table)->first_row('array');
    }
    
    function getCourseName($id)
    {
        $course = $this->db->where(array('intProgramID'=>$id))->get('tb_mas_programs')->first_row('array');
        
        $desc = $course['strProgramDescription'];
            
        if($course['strMajor']!="")
            $desc .=" ".$course['strMajor'];
        
        return $desc;
    }

    function getDefaultTuitionYearID()
    {
        $tuition = $this->db->where(array('isDefault'=>1))->get('tb_mas_tuition_year')->first_row('array');        
        return $tuition['intID'];
    }
    
    function getDefaultTuitionYear()
    {
        return $this->db->where(array('isDefault'=>1))->get('tb_mas_tuition_year')->first_row('array');        
        
    }
    
    function getCourseCode($id)
    {
        $course = $this->db->where(array('intProgramID'=>$id))->get('tb_mas_programs')->first_row('array');
        
        //$desc = $course['strProgramDescription'];
        $courseCode = $course['strProgramCode'];
//        
//        if($course['strMajor']!="")
//            $desc .=" ".$course['strMajor'];
//        
        return $courseCode;
    }
    
    
    function getGeneralDesc($table,$id,$key,$field)
    {
        $item = $this->db->where(array($key=>$id))->get($table)->first_row('array');
        $desc = $item[$field];
        return $desc;
    }
    
    function getFacultyOnlineUsername()
    {
        $ret = array();
        
        $users = $this->db
                    ->select('intID,strFirstname,strLastname,intIsOnline')
                    ->from('tb_mas_faculty')
                    ->where(array('intIsOnline !='=>'0000-00-00 00:00:00'))
                    ->get()
                    ->result_array();
        
        foreach($users as $user)
        {
            $datetime1 = strtotime($user['intIsOnline']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);
            $minutes   = round($interval / 60);
            
            if($minutes < 1)
                $ret[] = $user;
        }
        
        return $ret;
        
    }   
    
    function getClassListStudentsStPortal($id,$classlist) 
    {
               
        return  $this->db
                     ->select("tb_mas_classlist_student.intCSID,strCode,strSection , intLab, floatPrelimGrade, floatMidtermGrade, floatFinalsGrade, tb_mas_subjects.strDescription,tb_mas_classlist_student.floatFinalGrade as v3,intFinalized,enumStatus,strRemarks,tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_subjects.strUnits, tb_mas_subjects.intBridging, tb_mas_classlist.intID as classlistID, tb_mas_subjects.intID as subjectID")
                     ->from("tb_mas_classlist_student")
            
                    ->where(array("intStudentID"=>$id,"strAcademicYear"=>$classlist))
                        
                        
                     ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->order_by('strCode','asc')   
                     ->get()
                     ->result_array();
        
    }
    function getSyStudentEnrolled($id,$enrolled=1) 
    {
               
        return  $this->db
                     ->select("tb_mas_sy.intID,tb_mas_sy.enumSem,tb_mas_sy.strYearStart , tb_mas_sy.strYearEnd, tb_mas_registration.intStudentID,tb_mas_registration.intROG")
                     ->from("tb_mas_sy")
                    ->where(array("intStudentID"=>$id,"intROG"=>$enrolled))

                     ->join('tb_mas_registration', 'tb_mas_registration.intAYID = tb_mas_sy.intID')
                     ->order_by('intID','asc')   
                     ->get()
                     ->result_array();
        
    }
    
    function count_classlist($submitted=1)
    {
        $sem  = $this->get_active_sem();
        return $this->db
                    ->get_where('tb_mas_classlist',array('strAcademicYear'=>$sem['intID'],'intFinalized'=>$submitted))
                    ->num_rows();
    }   
    
    function fetch_table_fields($fields,$table)
    {
        return $this->db
                    ->select($fields)
                    ->from($table)
                    ->get()
                    ->result_array();
    }
    
    function messageExists($messageID,$userID)
    {
        $array = $this->db
             ->get_where('tb_mas_message_user',array('intFacultyID'=>$userID,'intMessageID'=>$messageID))
             ->result_array();
        
        if(empty($array))
            return false;
        else
            return true;
    }
    
    function getMessage($id)
    {
      $data =  $this->db
             ->select('tb_mas_system_message.intID as intID, strMessage, strSubject, dteDate,intFacultyIDSender, strFirstname, strLastname, intMessageID')
            ->join('tb_mas_system_message','tb_mas_message_user.intMessageID = tb_mas_system_message.intID')
             ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_message_user.intFacultyIDSender')
             ->where(array('intMessageUserID'=>$id))
             ->get('tb_mas_message_user')
             ->result_array();
        
        return current($data);
                     
    }
    
    function getMessages($id)
    {
      $data =  $this->db
             ->select('tb_mas_system_message.intID as intID, strMessage, strSubject, dteDate,intFacultyIDSender, strFirstname, strLastname, intMessageID,intMessageUserID')
             ->join('tb_mas_system_message','tb_mas_message_user.intMessageID = tb_mas_system_message.intID')
             ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_message_user.intFacultyIDSender')
             ->where(array('intFacultyID'=>$id,'intRead'=>0,'intTrash'=>0))
             ->order_by('dteDate','desc ')
             ->limit(8)
             ->get('tb_mas_message_user')
             ->result_array();
        
        return $data;
                     
    }
    
    function getReplyThread($id)
    {
         $reply = $this->db
                     ->select('strReplyMessage, dteReplied, strFirstname, strLastname , tb_mas_faculty.intID as intFacultyID, intReplyThreadID')
                     ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_reply_thread.intFacultyID')
                     ->where(array('intMessageID'=>$id))
                     ->order_by('dteReplied','asc')
                     ->get('tb_mas_reply_thread')
                     ->result_array();
        return $reply;
    }
    
    function getItem($table,$id,$label ="intID")
    {
        if($table == "tb_mas_faculty")
            return  current($this->db->get_where($table,array('intEmpID'=>$id))->result_array());
        
        return  current($this->db->get_where($table,array($label=>$id))->result_array());
                     
    }
    
    
    function getSubjectsNotInCurriculum($id)
    {
        $bucket = "SELECT intID,strCode,strDescription FROM tb_mas_subjects WHERE intID NOT IN (SELECT intSubjectID from tb_mas_curriculum_subject WHERE intCurriculumID = ".$id.") ORDER BY strCode ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        return $subjects;
    }
    
    function getRoomsNotSelected($id)
    {
        $bucket = "SELECT tb_mas_classrooms.* FROM tb_mas_classrooms WHERE intID NOT IN (SELECT intRoomID from tb_mas_room_subject WHERE intSubjectID = ".$id.") ORDER BY strRoomCode ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        return $subjects;
    }
    
    function getRoomsSelected($id,$type=null)
    {
        $bucket = "SELECT tb_mas_classrooms.* FROM tb_mas_classrooms WHERE intID IN (SELECT intRoomID from tb_mas_room_subject WHERE intSubjectID = ".$id.")";  
        
        if($type!=null)
            $bucket .= " AND enumType = '".$type."' ";
        
        $bucket .= "ORDER BY strRoomCode ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        return $subjects;
    }
    
    function getSelectedDays($id)
    {
        return $this->db
                    ->select('strDays')
                    ->from('tb_mas_days')
                    ->where(array('intSubjectID'=>$id))
                    ->get()
                    ->result_array();
    }   
    
    function getPrereq($id,$type=null)
    {
        $bucket = "SELECT tb_mas_subjects.* FROM tb_mas_subjects WHERE intID IN (SELECT intPrerequisiteID from tb_mas_prerequisites WHERE intSubjectID = ".$id.")";  
        
        if($type!=null)
            $bucket .= " AND enumType = '".$type."' ";
        
        $bucket .= "ORDER BY strCode ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        return $subjects;
    }
    
    function getSubjectsNotSelected($id,$type=null)
    {
        $bucket = "SELECT tb_mas_subjects.* FROM tb_mas_subjects WHERE intID NOT IN (SELECT intPrerequisiteID from tb_mas_prerequisites WHERE intSubjectID = ".$id.")";  
        
        if($type!=null)
            $bucket .= " AND enumType = '".$type."' ";
        
        $bucket .= "ORDER BY strCode ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        return $subjects;
    }
    
    function getRequiredSubjects($studentID,$curriculumID,$sem=null,$year=null)
    {
        $bucket = "SELECT tb_mas_subjects.intID,strCode,strDescription FROM tb_mas_subjects JOIN tb_mas_curriculum_subject ON tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID JOIN tb_mas_curriculum on tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID WHERE tb_mas_subjects.intID NOT IN (SELECT intSubjectID from tb_mas_classlist_student  JOIN tb_mas_classlist ON intClassListID = tb_mas_classlist.intID WHERE intStudentID = ".$studentID." AND strRemarks = 'Passed') AND tb_mas_subjects.intID NOT IN (SELECT intSubjectID from tb_mas_credited_grades WHERE intStudentID =".$studentID.") AND  tb_mas_curriculum.intID = '".$curriculumID."' ";
            
    //
        
      
        
        
        if($sem!=null && $year!=null)
            $bucket .= "AND tb_mas_curriculum_subject.intYearLevel = ".$year." AND tb_mas_curriculum_subject.intSem = ".$sem." ";
        
        
        
        $bucket .= "ORDER BY tb_mas_curriculum_subject.intYearLevel ASC, tb_mas_curriculum_subject.intSem ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        //print_r($subjects);
        $ret = array();
        
        //PREREQUISITES CODE----------------------------------------------------------------------------
        /*
        foreach($subjects as $subj)
        {
            $add = true;
            
            $r = $this->db
                      ->get_where('tb_mas_prerequisites',array('intSubjectID'=>$subj['intID']))
                      ->result_array();
            
            if(!empty($r))
            {
                foreach($r as $res){
                    $s = $this->db
                      ->select('tb_mas_classlist_student.intCSID')
                      ->from('tb_mas_classlist_student')
                      ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_classlist_student.intClassListID')
                      ->where(array('intSubjectID'=>$res['intPrerequisiteID'],'strRemarks'=>'Passed','intStudentID'=>$studentID))
                      ->get()
                      ->result_array();
                    
                    if(empty($s))
                    {
                        $add = false;
                        break;
                    }
                    
                }
                
                
            }
            
            if($add)
                $ret[] = $subj;
                    
        }
        return $ret;
        */
        return $subjects;
    }

    function countStudentsInClasslist($id)
    {
        return $this->db
                    ->select('intCSID')
                    ->from('tb_mas_classlist_student')
                    ->where('intClassListID',$id)
                    ->get()
                    ->num_rows();
    }
    
    function getSubjectsInCurriculum($id)
    {
        $subjects = $this->db
                         ->select( 'tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strCode,tb_mas_subjects.strUnits,tb_mas_subjects.intID as intSubjectID,tb_mas_subjects.strDescription')
                         ->from('tb_mas_curriculum_subject')
                         ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                         ->where('tb_mas_curriculum_subject.intCurriculumID',$id)
                         ->order_by('intYearLevel asc,intSem asc, strCode asc')
                         ->get()
                         ->result_array();
        
        return $subjects;
    }

    function getSubjectsInCurriculumMain($id)
    {
        $subjects = $this->db
                        ->select( 'tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strCode,tb_mas_subjects.strUnits,tb_mas_subjects.intID as intSubjectID,tb_mas_subjects.strDescription')
                        ->from('tb_mas_curriculum_subject')
                        ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID')
                        ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$id, 'tb_mas_subjects.intBridging'=>0))
                        ->order_by('intYearLevel asc,intSem asc, strCode asc')
                        ->get()
                        ->result_array();        
        
        
        return $subjects;
    }

    function getSubjectsInCurriculumEqu($id){

        $equivalent = $this->db
                        ->select('tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intSubjectID as mainSubjectID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strCode,tb_mas_subjects.strUnits,tb_mas_subjects.intID as intSubjectID,tb_mas_subjects.strDescription')
                        ->from('tb_mas_curriculum_subject')
                        ->join('tb_mas_subjects','tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                        ->where('tb_mas_curriculum_subject.intCurriculumID',$id)
                        ->order_by('intYearLevel asc,intSem asc, strCode asc')
                        ->get()
                        ->result_array();

        return $equivalent;

    }
     
    function getSectionsSubject($subjid,$sem)
    {
        $sections = $this->db
                         ->select('strSection,intID')
                         ->from('tb_mas_classlist')
                         ->where(array('intSubjectID'=>$subjid,'strAcademicYear'=>$sem))
                         ->order_by('strSection asc')
                         ->get()
                         ->result_array();
        
        return $sections;
    }
    
    function getSubjectsInCurriculumWithSections($curriculumID,$sem,$studentID)
    {
        
        $bucket = "SELECT tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strCode,tb_mas_subjects.strUnits,tb_mas_subjects.intID as intSubjectID,tb_mas_subjects.strDescription FROM tb_mas_subjects JOIN tb_mas_curriculum_subject ON tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID JOIN tb_mas_curriculum on tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID JOIN tb_mas_classlist ON tb_mas_subjects.intID = tb_mas_classlist.intSubjectID WHERE tb_mas_subjects.intID NOT IN (SELECT intSubjectID from tb_mas_classlist_student  JOIN tb_mas_classlist ON intClassListID = tb_mas_classlist.intID WHERE intStudentID = ".$studentID." AND strRemarks = 'Passed') AND tb_mas_subjects.intID NOT IN (SELECT intSubjectID from tb_mas_credited_grades WHERE intStudentID =".$studentID.") AND  tb_mas_curriculum.intID = '".$curriculumID."' AND tb_mas_classlist.strAcademicYear = ".$sem." ";
            
        //for prerquisites
        //AND (tb_mas_subjects.intPrerequisiteID IN (SELECT `intSubjectID` from tb_mas_classlist_student  JOIN tb_mas_classlist ON intClassListID = tb_mas_classlist.intID WHERE intStudentID = ".$studentID." AND strRemarks = 'Passed') OR tb_mas_subjects.intPrerequisiteID = 0 )
        
        $bucket .= "GROUP BY tb_mas_classlist.intSubjectID ORDER BY tb_mas_curriculum_subject.intYearLevel ASC, tb_mas_curriculum_subject.intSem ASC"; 
        
        $subjects = $this->db
             ->query($bucket)
             ->result_array();
        
        //echo $this->db->last_query();
        //print_r($subjects);
        return $subjects;
    }
    
    function countUnitsInCurriculum($id)
    {
        $subjects = $this->db
                         ->select( 'SUM(tb_mas_subjects.strUnits) as totalUnits')
                         ->from('tb_mas_subjects')
                         ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                         ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$id, 'tb_mas_subjects.intBridging'=>'0'))
                         ->group_by('tb_mas_curriculum_subject.intCurriculumID')
                         ->get()
                         ->result_array();
        
        return $subjects[0]['totalUnits'];
    }
    
    function count_table_contents($table,$category = null,$where=null,$group=null)
    {
            if($category!=null)
                $this->db
				     ->where('enumCat',$category);		
            if($where!=null)
                $this->db
				     ->where($where);		
            if($group!=null)
                $this->db->group_by($group); 
        
            return $this->db
                        ->count_all_results($table);
        
        
        
    }
    
    function count_sent_items($user)
    {
            	
            $this->db->select("intMessageUserID")
                     ->where(array("intTrash"=>"0","intFacultyIDSender"=>$user))
                      ->group_by("intMessageID"); 
        
            $result = $this->db
                  ->get("tb_mas_message_user")->result_array();
        
            return count($result);
        
        
        
    }
    
    function fetch_student_data($table,$order=null,$limit=null,$where=null)
	{				
		
        $this->db->select('intID,strFirstname,strMiddlename,strLastname,strCourse,dteCreated,strSection');
		if($order!=null)
			$this->db->order_by($order[0],$order[1]);
		elseif($table == 'tb_mas_content')
			$this->db->order_by('dteStart','desc');
		
		if($limit!=null)
			$this->db->limit($limit);
			
		if($where!=null)
			$this->db->where($where);
		
		$data =  $this->db
						->get($table)
						->result_array();
						
		return $data;
						
	}
    function search_for_students($search_string)
    {
        $this->db->where('strFirstname',$search_string)
                 ->or_where('strLastname',$search_string)
                 ->get('tb_mas_users');
        
        return $this->db->result_array();
        
        
    }
    
    function fetch_students($table,$order=null,$limit=20,$where=null,$offset)
	{				
		
		if($order!=null)
			$this->db->order_by($order[0],$order[1]);
		elseif($table == 'tb_mas_content')
			$this->db->order_by('dteStart','desc');
		
		if($limit!=null)
			$this->db->limit($limit,$offset);
			
		if($where!=null)
			$this->db->where($where);
		
		$data =  $this->db
						->get($table)
						->result_array();
						
		return $data;
						
	}
    
    
    function fetch_logs($start,$end)
    {
        $this->db
             ->select('strFirstname,strLastname,strAction,strCategory,dteLogDate,strColor')
             ->from('tb_mas_logs')
             ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_logs.intFacultyID');
           
        if($start == null)    
            $this->db->limit(20);
        else{
            $end .=" 23:59:59";
           $this->db->where(array('dteLogDate >='=>$start,'dteLogDate <='=>$end));
        }
        return    $this->db
                ->order_by('dteLogDate','desc')
                ->get()
                ->result_array();
    }
    
    function fetch_transactions($start,$end)
    {
        if($start != null)
        {
            $this->db
                 ->select('tb_mas_transactions.*,tb_mas_users.strFirstname,tb_mas_users.strLastname,tb_mas_users.intID as studentID')
                 ->from('tb_mas_transactions')
                 ->join('tb_mas_registration','tb_mas_registration.intRegistrationID = tb_mas_transactions.intRegistrationID')
                 ->join('tb_mas_users','tb_mas_users.intID = tb_mas_registration.intStudentID');
            
            
            
                $end .=" 23:59:59";
               $this->db->where(array('dtePaid >='=>$start,'dtePaid <='=>$end));
            
            return    $this->db
                    ->order_by('dtePaid desc,intORNumber asc')
                    ->get()
                    ->result_array();
        }
        else
            return array();
    }
    
    function get_active_sem()
    {
        return current($this->db->get_where('tb_mas_sy',array('enumStatus'=>'active'))->result_array());
        
    }
    
    function get_processing_sem()
    {
        return current($this->db->get_where('tb_mas_sy',array('intProcessing'=>1))->result_array());
        
    }

    function get_active_PrelimPeriod($id)
    {
        return current($this->db->get_where('tb_mas_sy',array('enumGradingPeriod'=>'active', 'intID'=>$id))->result_array());
    }
    function get_active_MidtermPeriod($id)
    {
        return current($this->db->get_where('tb_mas_sy',array('enumMGradingPeriod'=>'active', 'intID'=>$id))->result_array());
    }
    function get_active_FinalsPeriod($id)
    {
        return current($this->db->get_where('tb_mas_sy',array('enumFGradingPeriod'=>'active', 'intID'=>$id))->result_array());
    }
  
    function get_prev_sem($sem = null)
    {
        if($sem == null)
            $active = current($this->db->get_where('tb_mas_sy',array('enumStatus'=>'active'))->result_array());
        else     
            $active = current($this->db->get_where('tb_mas_sy',array('intID'=>$sem))->result_array());
        
        if($active['enumSem'] != "1st")
        {
            $sem = switch_num(switch_num_rev($active['enumSem']) - 1);
            $yearStart = $active['strYearStart'];
            $yearEnd = $active['strYearEnd'];
        }
        else
        {
            $sem = "2nd";
            $yearStart = $active['strYearStart'] - 1;
            $yearEnd = $active['strYearEnd'] - 1;
        }
        
        return current($this->db->get_where('tb_mas_sy',array('enumSem'=>$sem,'strYearStart'=>$yearStart,'strYearEnd'=>$yearEnd))->result_array());
        
        
    }
    
    function getClassroom($id)
    {
        return current($this->db->get_where('tb_mas_classrooms',array('intID'=>$id))->result_array());
        
    }
    
     function get_sem_by_id($id)
    {
        return current($this->db->get_where('tb_mas_sy',array('intID'=>$id))->result_array());
        
    }
    
    function fetch_classlists($limit=null,$sem = false,$sem_sel=null)
    {
        $faculty_id = $this->session->userdata("intID");
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd,count(tb_mas_classlist_student.intCSID) as numStudents")
                     ->from("tb_mas_classlist")
                     ->where(array("intFacultyID"=>$faculty_id));
                    
                    if($sem_sel!=null)
                        $this->db->where(array('strAcademicYear'=>$sem_sel));
        
                     $this->db->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                    ->join('tb_mas_classlist_student','tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID','left outer');
                if($limit != null)
                    $this->db->limit($limit);
                 return $this->db
                        ->group_by('tb_mas_classlist.intID')
                        ->get()
                        ->result_array();
        
    }
    
    
    
    function fetch_classlists_all($limit=null,$sem_sel=null)
    {
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,strFirstname,strLastname,strYearStart,strYearEnd,enumSem, COUNT(tb_mas_classlist_student.intStudentID) as students")
                     ->from("tb_mas_classlist")
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_classlist_student', 'tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID', 'Left')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear');
                    
                if($sem_sel!=null)
                        $this->db->where(array('strAcademicYear'=>$sem_sel));
                if($limit != null)
                    $this->db->limit($limit);
                 
                return $this->db
                        ->group_by('tb_mas_classlist.intID')
                        ->get()
                        ->result_array();
    }
    
    function fetch_classlists_dept($dept,$admin=false,$limit=null,$sem_sel=null)
    {
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,strFirstname,strLastname,strYearStart,strYearEnd,enumSem")
                     ->from("tb_mas_classlist")
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear');
                $where = array();
                if($sem_sel!=null)
                        $where['strAcademicYear'] = $sem_sel;
                if($limit != null)
                    $this->db->limit($limit);
                if(!$admin)
                    $where['tb_mas_subjects.strDepartment'] = $dept;
                 
                $this->db->where($where);
                return $this->db 
                        ->get()
                        ->result_array();
    }
    
    function fetch_classlists_unassigned($sem_sel=null,$limit=null,$dept)
    {
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID,strClassName,strCode,strDescription,intFinalized,strAcademicYear,strFirstname,strLastname,strYearStart,strYearEnd,enumSem")
                     ->from("tb_mas_classlist")
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear');
               
                    //$this->db->where(array('strAcademicYear'=>$sem_sel,'intFacultyID'=>999,'tb_mas_subjects.strDepartment'=>$dept));
                    $this->db->where(array('strAcademicYear'=>$sem_sel,'intFacultyID'=>999));
                    $this->db->order_by('strCode','asc');
            if($limit != null)
                    $this->db->limit($limit);
                 
                return $this->db 
                        ->get()
                        ->result_array();
    }
    
    function fetch_classlist_by_id($limit=null,$id)
    {
        $faculty_id = $this->session->userdata("intID");
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,strFirstname,strLastname,strYearStart,strYearEnd,enumSem,tb_mas_classlist.strUnits,strSignatory1Name,strSignatory2Name,strSignatory1Title,strSignatory2Title,tb_mas_subjects.strDepartment,intWithPayment")
                     ->from("tb_mas_classlist")
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear');
                
                $this->db->where(array('tb_mas_classlist.intID'=>$id));
                
                if($limit != null)
                    $this->db->limit($limit);
                 return current($this->db 
                        ->get()
                        ->result_array());
    }
    
    function fetch_classlist_by_subject($subject_id,$sem)
    {
        return $this->db
                    ->select('tb_mas_classlist.*')
                    ->from('tb_mas_classlist')
                    
                    ->where(array('intSubjectID'=>$subject_id,'strSection !='=>'','strAcademicYear'=>$sem))
                   
                    ->get()
                    ->result_array();
    }
    
    function fetch_classlist_by_subject_no_count($subject_id,$sem)
    {
        return $this->db
                    ->select('tb_mas_classlist.*')
                    ->from('tb_mas_classlist')
                    ->where(array('intSubjectID'=>$subject_id,'strSection !='=>'','strAcademicYear'=>$sem))
                    ->order_by('strSection','asc')
                    ->get()
                    
                    ->result_array();
    }
    
    
    function fetch_classlist_id($id)
    {
        return current($this->db
                    ->get_where('tb_mas_classlist',array('intID'=>$id))
                    ->result_array());
    }
    
    
    function getAverageGrade($id)
    {
        $score = $this->db->get_where('tb_mas_classlist_student',array('intCSID'=>$id))->first_row();
        $average = ($score->floatPrelimGrade+$score->floatMidtermGrade+$score->floatFinalGrade)/3;
        return $average;
    }
    
    function getCS($studentId,$classListId)
    {
        return  $this->db->get_where('tb_mas_classlist_student',array('intStudentID'=>$studentId,'intClassListID'=>$classListId))->result_array();
                     
    }   
    
    function getStudentSection($course,$year,$section)
    {
        return  $this->db->get_where('tb_mas_users',array('intProgramID'=>$course,'dteCreated'=>$year."-01-01",'strSection'=>$section))->result_array();
                     
    }
    
    function getStudents($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem=0)
    {
        
        $this->db
            ->select('tb_mas_users.*,strProgramCode')
            ->from('tb_mas_users')
            ->join('tb_mas_programs','tb_mas_users.intProgramID = tb_mas_programs.intProgramID')
            ->order_by('strLastname','asc');
        if($registered!=0 && $sem!=0){
            $this->db
                 ->join('tb_mas_registration','tb_mas_registration.intStudentID = tb_mas_users.intID');
            switch($registered)
            {
                case 1:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>0));
                break;
                case 2:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>1));
                break;
                case 3:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>2));
                break;
            }
        }
        
         if($course!=0)
            $this->db->where('tb_mas_users.intProgramID',$course);
        if($regular!=0)
           if($regular == 1)
                $this->db->where('strAcademicStanding','regular');
            else if ($regular == 2)
                $this->db->where('strAcademicStanding','irregular');
            else
                $this->db->where('strAcademicStanding','new');
        
        if($gender!=0)
           if($gender == 1)
                $this->db->where('enumGender','male');
            else
                $this->db->where('enumGender','female');
        
        if($year!=0)
            $this->db->where('intStudentYear',$year);
        
        if($graduate!=0)
            if($graduate == 1)
                    $this->db->where('isGraduate',1);
                else
                    $this->db->where('isGraduate',0);
        
        
        
        if($scholarship!=0)
            if($scholarship == 1)
                $this->db->where('enumScholarship','paying');
            elseif($scholarship == 2)
                $this->db->where('enumScholarship','resident scholar');
            elseif($scholarship == 3)
                    $this->db->where('enumScholarship','7th district');
            elseif($scholarship == 4)
                    $this->db->where('enumScholarship','DILG scholar');
            elseif($scholarship == 5)
                    $this->db->where('enumScholarship','FREE HIGHER EDUCATION PROGRAM (R.A. 10931)');
        
        return $this->db
             ->get()
             ->result_array();
            
            
                     
    }
    function getStudentsNew($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem=0, $studNumStart=0, $studNumEnd=0)
    {
        
        $this->db
            ->select('tb_mas_users.*,strProgramCode')
            ->from('tb_mas_users')
            ->join('tb_mas_programs','tb_mas_users.intProgramID = tb_mas_programs.intProgramID')
            ->order_by('strLastname','asc');
        if($registered!=0 && $sem!=0){
            $this->db
                 ->join('tb_mas_registration','tb_mas_registration.intStudentID = tb_mas_users.intID');
            switch($registered)
            {
                case 1:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>0));
                break;
                case 2:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>1));
                break;
                case 3:
                $this->db->where(array('tb_mas_registration.intAYID'=>$sem,'tb_mas_registration.intROG'=>2));
                break;
            }
        }
        
         if($course!=0)
            $this->db->where('tb_mas_users.intProgramID',$course);
        if($regular!=0)
           if($regular == 1)
                $this->db->where('strAcademicStanding','regular');
            else if ($regular == 2)
                $this->db->where('strAcademicStanding','irregular');
            else
                $this->db->where('strAcademicStanding','new');
        
        if($gender!=0)
           if($gender == 1)
                $this->db->where('enumGender','male');
            else
                $this->db->where('enumGender','female');
        
        if($year!=0)
            $this->db->where('intStudentYear',$year);
        
        if($graduate!=0)
            if($graduate == 1)
                    $this->db->where('isGraduate',1);
                else
                    $this->db->where('isGraduate',0);
        
        
        
        if($scholarship!=0)
            if($scholarship == 1)
                $this->db->where('enumScholarship','paying');
            elseif($scholarship == 2)
                $this->db->where('enumScholarship','resident scholar');
            elseif($scholarship == 3)
                    $this->db->where('enumScholarship','7th district');
            elseif($scholarship == 4)
                    $this->db->where('enumScholarship','DILG scholar');
            elseif($scholarship == 5)
                    $this->db->where('enumScholarship','FREE HIGHER EDUCATION PROGRAM (R.A. 10931)');
        
         if($studNumStart!=0 && $studNumEnd!=0)
        
            $this->db->where(array('tb_mas_users.strStudentNumber >='=>$studNumStart,'tb_mas_users.strStudentNumber <='=>$studNumEnd));
        
        return $this->db
             ->get()
             ->result_array();
            
            
                     
    }
    
    function getApplicantsExcel($course = 0,$appdate = 0,$gender = 0,$sem=0)
    {
        
        $this->db
            ->select('tb_mas_applications.*,refbrgy.brgyDesc,refcitymun.citymunDesc,refprovince.provDesc')
            ->from('tb_mas_applications')
            ->join('refbrgy','refbrgy.brgyCode = tb_mas_applications.strAppBrgy')
            ->join('refcitymun','refcitymun.citymunCode = tb_mas_applications.strAppCity')
            ->join('refprovince','refprovince.provCode = tb_mas_applications.strAppProvince')
                
            ->order_by('strLastname','asc');
        
         if($course!=0)
            $this->db->where('tb_mas_applications.enumCourse1',$course);

        if($gender!=0)
           if($gender == 1)
                $this->db->where('enumGender','male');
            else
                $this->db->where('enumGender','female');
        return $this->db
             ->get()
             ->result_array();
             
    }
    
    function getRegisteredStudents($ay)
    {
       
        return
        $this->db
             ->select('tb_mas_users.*,strProgramCode')
             ->from('tb_mas_users')
             ->join('tb_mas_programs','tb_mas_users.intProgramID = tb_mas_programs.intProgramID')
             ->join('tb_mas_registration','tb_mas_registration.intStudentID = tb_mas_users.intID')
             ->where(array('intAYID'=>$ay))
             ->get()
             ->result_array();
            
    }
    
    function countStudentsByCourse($course)
    {
        return  count($this->db->get_where('tb_mas_users',array('intProgramID'=>$course,'isGraduate'=>0))->result_array());   
    }
    
    function getScholars($programID,$type,$ay)
    {
        return 
            count($this->db
             ->select('intRegistrationID')
             ->from('tb_mas_registration')
             ->where('intAYID = '.$ay.' AND tb_mas_registration.enumScholarship = \''.$type.'\' AND tb_mas_registration.intROG = 1 AND tb_mas_users.intProgramID = '.$programID)
             ->join('tb_mas_users','tb_mas_registration.intStudentID = tb_mas_users.intID')
             ->get()
             ->result_array());
    }
    
    function getSubjects($post = null)
    {
        if($post!=null){
            $courses =
                $this->db
                     ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem')
                     ->from('tb_mas_subjects')
                     ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                     ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID')
                     ->where(array('tb_mas_curriculum.intID'=>$post['intCurriculumID'],'tb_mas_curriculum_subject.intYearLevel'=>$post['intYearLevel'],'tb_mas_curriculum_subject.intSem'=>$post['intSem']))
                     ->get()
                     ->result_array();
                
                
               // $this->db->get_where('tb_mas_subjects',array('intYearLevel'=>$post['intYearLevel'],'intProgramID'=>$post['strCourse'],'intSem'=>$post['intSem']))->result_array();

            
        }
        else
        {
            $courses =  $this->db
                             ->get('tb_mas_subjects')
                             ->result_array();
        }
        return $courses;
        
    }
    
    
    
    function getSubjectsCurriculum($post = null)
    {
        if($post!=null){
            $courses =
                $this->db
                     ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem')
                     ->from('tb_mas_subjects')
                     ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                     ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID')
                     ->where(array('tb_mas_curriculum.intID'=>$post['intCurriculumID']))
                     ->get()
                     ->result_array();
                
                
               // $this->db->get_where('tb_mas_subjects',array('intYearLevel'=>$post['intYearLevel'],'intProgramID'=>$post['strCourse'],'intSem'=>$post['intSem']))->result_array();

            
        }
        return $courses;
        
    }
    
    function getSubjectsCurriculumSem($id,$sem,$year)
    {
            $courses =
                $this->db
                     ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem')
                     ->from('tb_mas_subjects')
                     ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                     ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID')
                     ->where(array('tb_mas_curriculum.intID'=>$id,'tb_mas_curriculum_subject.intSem'=>$sem,'tb_mas_curriculum_subject.intYearLevel'=>$year))
                     ->get()
                     ->result_array();
                
                
               // $this->db->get_where('tb_mas_subjects',array('intYearLevel'=>$post['intYearLevel'],'intProgramID'=>$post['strCourse'],'intSem'=>$post['intSem']))->result_array();

        return $courses;
        
    }
    
    function getFromSchedule($scode,$sem,$lab='lect',$day=null)
	{
        $this->db
		 	 ->where(array('strScheduleCode'=>$scode,'intSem'=>$sem,'enumClassType'=>$lab));
        
            if($day!=null)
                $this->db->where('strDay',$day);
        $d =
            $this->db
                 ->get('tb_mas_room_schedule')
                 ->first_row();
        
        if(empty($d))
            return 0;
        else
            return $d->intRoomSchedID;
	}
    
    function getSelectedSubjects($id)
    {
        
        $subjects =  $this->db
                         ->select('intSubjectID')
                         ->from('tb_mas_subjects_faculty')
                         ->where(array('intFacultyID'=>$id))   
                         ->get()
                         ->result_array();
        
        return $subjects;
        
    }
    
    function getStudent($id)
    {
        return  current(
                $this->db
                     ->select('tb_mas_users.*,tb_mas_programs.*,tb_mas_curriculum.strName')
                     ->from('tb_mas_users')
                     ->join('tb_mas_programs','tb_mas_programs.intProgramID = tb_mas_users.intProgramID')   
                     ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_users.intCurriculumID')
                     ->where(array('tb_mas_users.intID'=>$id))
                     ->get()
                     ->result_array());
                     
    }

    function getCurriculumIDByCourse($id){
        $curriculum =  $this->db->select('intID')->from('tb_mas_curriculum')->where('intProgramID',$id)->get()->first_row();
        return $curriculum->intID;
    }
    
    function getApplicant($id)
    {
        return  current(
                $this->db
                     ->select('tb_mas_applications.*,refbrgy.brgyDesc,refcitymun.citymunDesc,refprovince.provDesc')
                     ->from('tb_mas_applications')
                     ->join('refbrgy','refbrgy.brgyCode = tb_mas_applications.strAppBrgy')
                     ->join('refcitymun','refcitymun.citymunCode = tb_mas_applications.strAppCity')
                     ->join('refprovince','refprovince.provCode = tb_mas_applications.strAppProvince')
                     ->where(array('tb_mas_applications.intApplicationID'=>$id))
                     ->get()
                     ->result_array());
                     
    }
    
    function getApplicationByCode($code)
    {
        
        $conf = $this->db->get_where('tb_mas_applications',array('strConfirmationCode LIKE'=>$code))->result_array();
        return current($conf);
        
    }
    
    function getExamInfo($id)
    {
        return  current(
                $this->db
                     ->select('*')
                     ->from('tb_mas_exam_info')
                     ->where(array('intApplicationID'=>$id))
                     ->get()
                     ->result_array());
                     
    }
    
    function assessCurriculum($studentID,$curriculumID)
    {
        $subjects =  $this->db
                    ->select('tb_mas_subjects.strCode,tb_mas_subjects.strDescription,tb_mas_classlist_student.floatPrelimGrade, tb_mas_classlist_student.floatMidtermGrade,tb_mas_classlist_student.floatFinalsGrade, min(floatFinalGrade) as floatFinalGrade,strRemarks,tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strUnits,tb_mas_classlist.strAcademicYear, tb_mas_sy.enumSem,tb_mas_sy.strYearStart,tb_mas_sy.strYearEnd,tb_mas_sy.intID as syID, tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_classlist.intID as classListID')
                    ->from('tb_mas_curriculum_subject')
                    ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID')
                    ->join('tb_mas_classlist','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID','inner')
                    ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID','inner')
                    ->join('tb_mas_sy','tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                    ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                    ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID, 'tb_mas_subjects.intBridging'=>0))
                    ->order_by('strYearStart asc,enumSem asc, strCode asc')
                    ->group_by('tb_mas_subjects.strCode')
                    ->get()
                    ->result_array();

        $equivalent_grades =  $this->db
                        ->select('tb_mas_subjects.strCode,tb_mas_subjects.strDescription,tb_mas_classlist_student.floatPrelimGrade, tb_mas_classlist_student.floatMidtermGrade,tb_mas_classlist_student.floatFinalsGrade, min(floatFinalGrade) as floatFinalGrade,strRemarks,tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strUnits,tb_mas_sy.enumSem,tb_mas_sy.strYearStart,tb_mas_sy.strYearEnd,tb_mas_sy.intID as syID, tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_classlist.intID as classListID')
                        ->from('tb_mas_curriculum_subject')
                        ->join('tb_mas_subjects','tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                        ->join('tb_mas_classlist','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID','inner')
                        ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID','inner')
                        ->join('tb_mas_sy','tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                        ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                        ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID, 'tb_mas_subjects.intBridging'=>0))
                        ->order_by('strYearStart asc,enumSem asc, strCode asc')
                        ->group_by('tb_mas_subjects.strCode')
                        ->get()
                        ->result_array();
        
        $credited = $this->db
                     ->select('tb_mas_subjects.strCode,tb_mas_credited_grades.floatFinalGrade,tb_mas_credited_grades.strRemarks,tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strUnits,tb_mas_credited_grades.intSYID as enumSem,tb_mas_credited_grades.intSYID as strYearStart,tb_mas_credited_grades.intSYID as strYearEnd,tb_mas_credited_grades.intSYID  as syID')
                     ->from('tb_mas_credited_grades')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_credited_grades.intSubjectID')
                     ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intCurriculumID = tb_mas_credited_grades.intCurriculumID','inner')
                     ->where(array('tb_mas_credited_grades.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID))
                     ->group_by('tb_mas_credited_grades.intID')
                     ->order_by('strYearStart asc,enumSem asc')
                     ->get()
                     ->result_array();
                     
        $merged = array_merge($subjects,$credited);
        $merged = array_merge($merged,$equivalent_grades);
        
        return $merged;
    }

    function assessCurriculumDept($studentID,$curriculumID)
    {
        $subjects =  $this->db
                     ->select('tb_mas_subjects.strCode,tb_mas_subjects.strDescription,tb_mas_subjects.intBridging,tb_mas_classlist_student.floatPrelimGrade, tb_mas_classlist_student.floatMidtermGrade,tb_mas_classlist_student.floatFinalsGrade, min(floatFinalGrade) as floatFinalGrade,strRemarks,tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strUnits,tb_mas_sy.enumSem,tb_mas_sy.strYearStart,tb_mas_sy.strYearEnd,tb_mas_sy.intID as syID, tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_classlist.intID as classListID')
                     ->from('tb_mas_curriculum_subject')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                     ->join('tb_mas_classlist','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID','inner')
                     ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID','inner')
                     ->join('tb_mas_sy','tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
//                     ->join('tb_mas_registration','tb_mas_registration.intAYID = tb_mas_sy.intID and tb_mas_registration.intStudentID = tb_mas_classlist_student.intStudentID')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'tb_mas_classlist_student.intStudentID'=>$studentID))
                     ->order_by('strYearStart asc,enumSem asc, strCode asc')
                     ->group_by('tb_mas_classlist.intID')
                     ->get()
                     ->result_array();
        
        $credited = $this->db
                     ->select('tb_mas_subjects.strCode,tb_mas_credited_grades.floatFinalGrade,tb_mas_credited_grades.strRemarks,tb_mas_curriculum_subject.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strUnits,tb_mas_credited_grades.intSYID as enumSem,tb_mas_credited_grades.intSYID as strYearStart,tb_mas_credited_grades.intSYID as strYearEnd,tb_mas_credited_grades.intSYID  as syID')
                     ->from('tb_mas_credited_grades')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_credited_grades.intSubjectID')
                     ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intCurriculumID = tb_mas_credited_grades.intCurriculumID','inner')
                     ->where(array('tb_mas_credited_grades.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID))
                     ->group_by('tb_mas_credited_grades.intID')
                     ->order_by('strYearStart asc,enumSem asc')
                     ->get()
                     ->result_array();
                     
        return array_merge($subjects,$credited);
    }
    
    function getCreditedSubjects($studentID,$curriculumID)
    {
        return $this->db
                     ->select('tb_mas_credited_grades.intID,tb_mas_subjects.strCode,tb_mas_credited_grades.floatFinalGrade,tb_mas_credited_grades.strRemarks,tb_mas_subjects.strUnits')
                     ->from('tb_mas_credited_grades')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_credited_grades.intSubjectID')
                     ->where(array('tb_mas_credited_grades.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID))
                     ->order_by('intYearLevel asc,intSem asc')
                     ->get()
                     ->result_array();
    }
    
    
    function unitsEarned($studentID,$curriculumID)
    {
        $ret =  $this->db
                     ->select('SUM(tb_mas_subjects.strUnits) AS TotalUnitsEarned')
                     ->from('tb_mas_curriculum_subject')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                     ->join('tb_mas_classlist','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID','inner')
                     ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID','inner')
                     ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID,'strRemarks'=>'Passed','floatFinalGrade !='=>'5','tb_mas_subjects.intBridging'=>'0'))
                     ->group_by('tb_mas_curriculum_subject.intCurriculumID')
                     ->get()
                     ->result_array();
        
        $credited = $this->db
                     ->select('SUM(tb_mas_subjects.strUnits) AS TotalUnitsEarned')
                     ->from('tb_mas_curriculum_subject')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID')
                     ->join('tb_mas_credited_grades','tb_mas_credited_grades.intSubjectID = tb_mas_subjects.intID','inner')
                     ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID,'strRemarks'=>'Passed','floatFinalGrade !='=>'5'))
                     ->group_by('tb_mas_curriculum_subject.intCurriculumID')
                     ->get()
                     ->result_array();
        
        $return = 0;
        if(!empty($ret))
            $return += $ret[0]['TotalUnitsEarned'];
        if(!empty($credited))
            $return +=$credited[0]['TotalUnitsEarned'];
       
        return $return;
    }
    
    function getGPA($studentID,$curriculumID)
    {
        $st =  $this->db
                     ->select('SUM(floatFinalGrade * tb_mas_subjects.strUnits) as gpa, SUM(tb_mas_subjects.strUnits) as num')
                     ->from('tb_mas_curriculum_subject')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID1 = tb_mas_curriculum_subject.intSubjectID OR tb_mas_subjects.intEquivalentID2 = tb_mas_curriculum_subject.intSubjectID')
                     ->join('tb_mas_classlist','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID','inner')
                     ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID','inner')
                     ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'tb_mas_subjects.intBridging'=>0,'intStudentID'=>$studentID,'floatFinalGrade !='=>'0','floatFinalGrade !='=>'3.5'))
                     ->group_by('tb_mas_curriculum_subject.intCurriculumID')
                     ->get()
                     ->first_row();
        
        $credited =  $this->db
                     ->select('SUM(floatFinalGrade * tb_mas_subjects.strUnits) as gpa, SUM(tb_mas_subjects.strUnits) as num')
                     ->from('tb_mas_curriculum_subject')
                     ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_curriculum_subject.intSubjectID')
                     ->join('tb_mas_credited_grades','tb_mas_credited_grades.intSubjectID = tb_mas_subjects.intID','inner')
                     ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$curriculumID,'intStudentID'=>$studentID,'floatFinalGrade !='=>'0','floatFinalGrade !='=>'3.5'))
                     ->group_by('tb_mas_curriculum_subject.intCurriculumID')
                     ->get()
                     ->first_row();
        
        $return = 0;
        $div = 0;
        if(!empty($st)){
            $return += $st->gpa;
            $div += $st->num;
        }
     
        
        if(!empty($credited))
        {
            $return += $credited->gpa;
            $div += $credited->num;
        }
        
        
        
        //if($div!=0)
          //  $return  = $return/$div;
        
        return $return;
    }
    
    function getUserData($id)
    {
        return $this->db
             ->select('intID,strFirstname,strLastname')
             ->from('tb_mas_faculty')
             ->where('intID',$id)
             ->get()
             ->first_row();
    }
    
    
    function getUnitsPerYear($id)
    {
        $subjects = $this->db
                         ->select( 'SUM(tb_mas_subjects.strUnits) as totalUnits, tb_mas_curriculum_subject.intYearLevel')
                         ->from('tb_mas_subjects')
                         ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                         ->where(array('tb_mas_curriculum_subject.intCurriculumID'=>$id, 'tb_mas_subjects.intBridging'=>'0'))
                         ->group_by('tb_mas_curriculum_subject.intYearLevel, tb_mas_curriculum_subject.intSem')
                         ->get()
                         ->result_array();
        
        return $subjects;
    }
    
    
    function executeAcademicSync()
    {
        $sem = $this->get_active_sem();
        $stud = $this->getRegisteredStudents($sem['intID']);
        
        /*$this->db
             ->where(array('isGraduate !='=>'1'))
             ->get('tb_mas_users')
             ->result_array();*/
        
        foreach($stud as $s)
        {
            $standing = $this->getAcademicStanding($s['intID'],$s['intCurriculumID']);
            $data['intStudentYear'] = $standing['year'];
            $data['strAcademicStanding'] = $standing['status'];
            $this->db
                 ->where('intID',$s['intID'])
                 ->update('tb_mas_users',$data);
            
        }
             
    }
    
    function getFailedSubject($studentID)
    {
        return $this->db
             ->select('intCSID')
             ->from('tb_mas_classlist_student')
             ->where(array('intStudentID'=>$studentID,'strRemarks != '=>'Passed','floatFinalGrade != '=>0))
             ->get()
             ->result_array();
    }
    
    function getAcademicStanding($studentID,$curriculumID)
    {
        $units = $this->unitsEarned($studentID,$curriculumID);
        $standing['year'] = 1;
        $standing['status'] = "regular";
        $t = 0;
        $i = 1;
        foreach($this->getUnitsPerYear($curriculumID) as $year_level)
        {
            $t += $year_level['totalUnits'];
            if($units >= $t && ($i % 2) == 0)
                $standing['year'] = $year_level['intYearLevel']+1;
            
            $i++;
        }
        
        if(!empty($this->getFailedSubject($studentID)))
            $standing['status'] = "irregular";
        elseif($units == 0)
            $standing['status'] = "new";
        
        
        return $standing;
    }
    
    
    function getStudentStudentNumber($id)
    {
        return  current(
                $this->db
                     ->select('*')
                     ->from('tb_mas_users')
                     ->where(array('strStudentNumber'=>$id))
                     ->join('tb_mas_programs','tb_mas_programs.intProgramID = tb_mas_users.intProgramID')    
                     ->get()
                     ->result_array());
                     
    }
    function getStudentByName($lname,$fname,$code)
    {
        return  current(
                $this->db
                     ->select('*')
                     ->from('tb_mas_users')
                     ->where(array('strFirstname LIKE'=>$fname,'strLastname LIKE'=>$lname,'strProgramCode LIKE'=>$code))
                     ->join('tb_mas_programs','tb_mas_programs.intProgramID = tb_mas_users.intProgramID')    
                     ->get()
                     ->result_array());
                     
    }
    function getFaculty($id)
    {
        return  current($this->db->get_where('tb_mas_faculty',array('intID'=>$id))->result_array());
                     
    }
    function getFacultyList()
    {
        return $this->db->get_where('tb_mas_faculty')->result_array();
                     
    }
    
    function getAy($id)
    {
        return  current($this->db->get_where('tb_mas_sy',array('intID'=>$id))->result_array());
                     
    }
    
    function getSubject($id)
    {
        
            return  current(
                     $this->db
                         ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.intPrerequisiteID')
                         ->from('tb_mas_subjects')
                         ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                         ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID')
                         ->where(array('tb_mas_subjects.intID'=>$id))
                         ->get()
                         ->result_array());
                     
    }
    
    function getSubjectCurr($id,$program)
    {
        
            return  current(
                     $this->db
                         ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.intPrerequisiteID')
                         ->from('tb_mas_subjects')
                         ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                         ->join('tb_mas_curriculum','tb_mas_curriculum.intID = tb_mas_curriculum_subject.intCurriculumID')
                         ->where(array('tb_mas_subjects.intID'=>$id,'tb_mas_curriculum.intProgramID'=>$program))
                         ->get()
                         ->result_array());
                     
    }
    
    function getAdvisedStudentsCourse($sem,$course)
    {
        
            return  
                     $this->db
                         ->select( 'tb_mas_users.intID,tb_mas_users.strLastname,tb_mas_users.strFirstname')
                         ->from('tb_mas_users')
                         ->join('tb_mas_advised','tb_mas_advised.intStudentID = tb_mas_users.intID')
                         ->where(array('tb_mas_advised.intSYID'=>$sem,'tb_mas_users.intProgramID'=>$course))
                         ->order_by('strLastname asc, strFirstname asc')
                         ->get()
                         ->result_array();
                     
    }
    
    function getSubjectNoCurr($id)
    {
        
            return  current(
                     $this->db
                         ->select( 'tb_mas_subjects.intID,intProgramID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee,tb_mas_subjects.intPrerequisiteID')
                         ->from('tb_mas_subjects')
                         ->where(array('tb_mas_subjects.intID'=>$id))
                         ->get()
                         ->result_array());
                     
    }
    
    function getSubjectPlain($id)
    {
        return current($this->db->get_where('tb_mas_subjects',array('intID'=>$id))->result_array());
    }
   
    function getProgram($id)
    {
        return  current($this->db->get_where('tb_mas_programs',array('intProgramID'=>$id))->result_array());
                     
    }
         
    
    function checkSubjectTaken($studentID,$subjectID)
    {
        
        $arr = $this->db
             ->select('intStudentID')
             ->from('tb_mas_classlist_student')
             ->where(array("intStudentID"=>$studentID,"intSubjectID"=>$subjectID,'floatFinalGrade'=>'!= 5','enumStatus'=>'!= \'odrp\''))
             ->join('tb_mas_classlist', 'tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID')             
             ->get()->result_array();
        if(empty($arr))
            return false;
        else
            return true;
    }
    
    function checkSubjectAdvised($studentID,$subjectID,$sem)
    {
        
        $arr = $this->db
             ->select('intStudentID')
             ->from('tb_mas_advised')
             ->where(array("intStudentID"=>$studentID,"intSubjectID"=>$subjectID,'intSYID'=>$sem))
             ->join('tb_mas_advised_subjects', 'tb_mas_advised_subjects.intAdvisedID = tb_mas_advised.intAdvisedID')             
             ->get()->result_array();        
        
             print_r($arr);
        if(empty($arr))
            return false;
        else
            return true;
    }
    
    function checkStudentAdvised($studentID,$sem)
    {
        
        $arr = $this->db
             ->select('intStudentID')
             ->from('tb_mas_advised')
             ->where(array("intStudentID"=>$studentID,'intSYID'=>$sem))
             ->get()->result_array();
        if(empty($arr))
            return false;
        else
            return true;
    }
    
    function getAdvisedID($studentID,$sem)
    {
        $arr = $this->db
             ->select('intAdvisedID')
             ->from('tb_mas_advised')
             ->where(array("intStudentID"=>$studentID,'intSYID'=>$sem))
             ->get()->first_row();
        
        return $arr->intAdvisedID;
        
    }
    
    function getAdvisedSubjects($studentID,$sem)
    {
        $arr = $this->db
             ->select('intSubjectID,strCode')
             ->from('tb_mas_advised')
             ->join('tb_mas_advised_subjects','tb_mas_advised.intAdvisedID = tb_mas_advised_subjects.intAdvisedID')
             ->join('tb_mas_subjects','tb_mas_advised_subjects.intSubjectID = tb_mas_subjects.intID')
             ->where(array("intStudentID"=>$studentID,'intSYID'=>$sem))
             ->get()->result_array();
        
        return $arr;
    }
    
    function getAdvisedSubjectsReg($post)
    {
            $courses =
               $this->db
                    ->select( 'tb_mas_subjects.intID,intAthleticFee,strCode,strDescription,strUnits,intLab,intAthleticFee')
                    ->from('tb_mas_advised')
                    ->join('tb_mas_advised_subjects','tb_mas_advised.intAdvisedID = tb_mas_advised_subjects.intAdvisedID')
                    ->join('tb_mas_subjects','tb_mas_advised_subjects.intSubjectID = tb_mas_subjects.intID')
                    ->where(array("intStudentID"=>$post['intStudentID'],'intSYID'=>$post['sem']))
                    ->get()
                    ->result_array();
                
                
               // $this->db->get_where('tb_mas_subjects',array('intYearLevel'=>$post['intYearLevel'],'intProgramID'=>$post['strCourse'],'intSem'=>$post['intSem']))->result_array();

        return $courses;
        
    }
    
    
    function checkRegistered($studentID,$AYID)
    {
        
        $arr = $this->db
             ->select('intRegistrationID')
             ->from('tb_mas_registration')
             ->where(array("intStudentID"=>$studentID,"intAYID"=>$AYID,'intROG'=>1))
             ->get()->result_array();
        if(empty($arr))
            return false;
        else
            return true;
    }
    function checkClasslistExists($subject,$ay,$course,$new=null)
    {
        
            
       $classlists = $this->db->where(array('intSubjectID'=>$subject,'strAcademicYear'=>$ay,'strSection LIKE '=>$course.'%'))
           ->order_by('strSection asc')
           ->get('tb_mas_classlist')
           ->result_array();
        
        $cl_ret = "";
        
        if(!empty($classlists))
        {
            
            if($new!=null)
            {
                return "new-".count($classlists);
            }
           
            $limit = 30;
            
            foreach($classlists as $cl)
            {
                 if( count($this->db
                             ->select('intCSID')
                             ->from('tb_mas_classlist_student')
                             ->where(array('intClassListID'=>$cl['intID']))
                             ->get()
                             ->result_array()) < $limit
                    )
                    {
                       $cl_ret = $cl;
                       break;
                    }
                    else
                    {
                        $cl_ret = "new-".count($classlists);
                    }
                
            }
            return $cl_ret;
        }
        else
        {
            return "1";
        }
       
    }
    function checkClasslistExistsGen($subject,$ay,$course)
    {
        
            
       $classlists = $this->db->where(array('intSubjectID'=>$subject,'strAcademicYear'=>$ay,'strSection LIKE '=>$course.'%'))
           ->order_by('strSection asc')
           ->get('tb_mas_classlist')
           ->result_array();
        
        if(!empty($classlists))
            return count($classlists)+1;
        else
            return 1;
        
       
    }
    function getRegistrationInfo($id,$sem)
    {
        return  current($this->db->get_where('tb_mas_registration',array('intStudentID'=>$id,'intAYID'=>$sem))->result_array());
    }
    
    function getRegistrationData($sem)
    {
        $data['enrolled'] = $this->db->get_where('tb_mas_registration',array('intAYID'=>$sem,'intROG'=>1))->num_rows();
        $data['registered'] = $this->db->get_where('tb_mas_registration',array('intAYID'=>$sem,'intROG'=>0))->num_rows();
        $data['cleared'] = $this->db->get_where('tb_mas_registration',array('intAYID'=>$sem,'intROG'=>2))->num_rows();
        
        return $data;
    }
    
    
    
        
    function getRegistrationStatus($id,$sem)
    {
         
        if($this->db
             ->select('intRegistrationID')
             ->from('tb_mas_registration')
             ->where(array('intStudentID'=>$id,'intAYID'=>$sem))
             ->get()
             ->num_rows() > 0)
        {
            $r = $this->db
             ->select('intRegistrationID,intROG')
             ->from('tb_mas_registration')
             ->where(array('intStudentID'=>$id,'intAYID'=>$sem))
             ->get()
             ->first_row();
            
            if($r->intROG == 0)
                return "Registered";
            if($r->intROG == 1)
                return "Enrolled";
            if($r->intROG == 2)
                return "Cleared";
        }
        elseif($this->db
             ->select('intID')
             ->from('tb_mas_classlist')
             ->join('tb_mas_classlist_student','tb_mas_classlist.intID = tb_mas_classlist_student.intClassListID')
             ->where(array('intStudentID'=>$id,'strAcademicYear'=>$sem))
             ->get()
             ->num_rows() > 0)
            return "For Registration";
        elseif($this->db
             ->select('intAdvisedID')
             ->from('tb_mas_advised')
             ->where(array('intStudentID'=>$id,'intSYID'=>$sem))
             ->get()
             ->num_rows() > 0)
            return "For Sectioning";
        else
            return "For Advising";
    }
    
    function getTuition($id,$sem,$misc_fee,$lab_fee,$athletic_fee,$id_fee,$srf,$sfdf,$csg,$scholarship)
    {
        
        $tuition = 0;
        $total_lab = 0;
        $afee = 0;
        $data['id_fee'] = 0;
        $data['csg']['student_handbook'] = 0;
        $data['csg']['student_publication'] = 0;
        $lab_list = array();
        $data['misc_fee']['Registration'] = 0;
        $data['misc_fee']['Guidance Fee'] = 0;
        $data['misc_fee']['Entrance Exam Fee'] = 0;
        $data['misc_fee']['Medical and Dental Fee'] = 0;
        $data['misc_fee']['Library Fee'] = 0;
        $data['srf'] = 0;
        $data['sfdf'] = 0;
        $data['repeated'] = [];
        $data['total_for_repeated'] = 0;

        $student = $this->db->where('intID',$id)->get('tb_mas_users')->first_row('array');
        $tuition_year = $this->db->where('intID',$student['intTuitionYear'])->get('tb_mas_tuition_year')->first_row('array');

        $unit_fee = $tuition_year['pricePerUnit'];

        $ay = $this->getAy($sem);
        
        
        if($scholarship != "resident scholar" ) {//&& $scholarship != "FREE HIGHER EDUCATION PROGRAM (R.A. 10931)"){
            
            $data['misc_fee']['Registration'] = $misc_fee['registration'];

            if($ay['enumSem'] != "3rd")
            {
                $data['misc_fee']['Medical and Dental Fee'] = $misc_fee['medical_fee'];
                $data['misc_fee']['Guidance Fee'] = $misc_fee['guidance_fee'];
                $data['misc_fee']['Library Fee'] = $misc_fee['library_fee'];
                $data['csg']['student_publication'] = $csg['student_publication'];
            }
            
            $reg = current($this->db->get_where('tb_mas_registration',array('intStudentID'=>$id,'intAYID'=>$sem))->result_array());
            
            if($scholarship != "DILG scholar")
            {
                $classes =  $this->db
                                 ->select("tb_mas_classlist_student.intCSID,tb_mas_subjects.strUnits,tb_mas_subjects.intLab, tb_mas_classlist.intSubjectID, tb_mas_subjects.strCode, 
                                 tb_mas_subjects.intAthleticFee, tb_mas_subjects.strTuitionUnits, tb_mas_subjects.strLabClassification")
                                 ->from("tb_mas_classlist_student")
                                 ->where(array("intStudentID"=>$id,"strAcademicYear"=>$sem,"tb_mas_classlist.intWithPayment"=>"0"))
                                 ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                                 ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                                 ->get()
                                 ->result_array();




                foreach($classes as $class)
                {
                    $firstTake = $this->db                                      
                                      ->select_min('strAcademicYear')
                                      ->from('tb_mas_classlist_student')
                                      ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                                      ->where(array('tb_mas_classlist.intSubjectID'=> $class['intSubjectID'], "intStudentID"=>$id))
                                      ->get()
                                      ->row();

                    $isRepeated = $this->db
                                        ->select('tb_mas_classlist_student.intCSID, tb_mas_subjects.strCode, tb_mas_subjects.strUnits')
                                        ->from('tb_mas_classlist_student')
                                        ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                                        ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                                        ->where(array('tb_mas_classlist.intSubjectID'=> $class['intSubjectID'], "intStudentID"=>$id))                                        
                                        ->get()
                                        ->result_array();
                                        
                    if(count($isRepeated) > 1 && $firstTake->strAcademicYear != $sem){                        
                        $repeated = current($isRepeated);
                        $amountRep = intval($class['strUnits'])*$unit_fee;                        
                        // if($class['intLab'] != 0){
                        //
                        //     $amountRep += $lab_fee*($class['intLab']/3);
                        // }
                        $data['repeated'][] = array('amount' => $amountRep, 'subjectCode' => $repeated['strCode'], 'strUnits' => $repeated['strUnits'] );
                        $data['total_for_repeated'] += $amountRep;
                        
                    }                        
                    
                    $tuition += intval($class['strTuitionUnits'])*$unit_fee;
                    
                    if($class['strLabClassification'] != "none"){
                        $lab_list[$class['strCode']] = $tuition_year[$class['strLabClassification']];
                        $total_lab += $lab_list[$class['strCode']];
                    }

                    if($class['intAthleticFee'] != 0){
                        $afee += $athletic_fee;
                    }



                }
            }
            else
            {
                $classes =  $this->db
                                 ->select("tb_mas_classlist_student.intCSID,tb_mas_subjects.strUnits,tb_mas_subjects.intLab, tb_mas_classlist.intSubjectID, tb_mas_subjects.strCode, tb_mas_subjects.intAthleticFee, tb_mas_classlist_student")
                                 ->from("tb_mas_classlist_student")
                                 ->where(array("intStudentID"=>$id,"strAcademicYear"=>$sem))
                                 ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                                 ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                                 ->get()
                                 ->result_array();




                foreach($classes as $class)
                {                                       
                    
                    $class['intLab'] = $class['intLab']/3;
                    if($class['intLab'] != 0){
                        $lab_list[$class['strCode']] = $lab_fee*$class['intLab'];
                        $total_lab += $lab_list[$class['strCode']];
                    }

                    if($class['intAthleticFee'] != 0){
                        $afee += $athletic_fee;
                    }



                }
            }

            if($ay['enumSem'] != "3rd")
            {
                if($reg['enumStudentType'] != "old")
                {
                    $data['id_fee'] = $id_fee;
                    $data['csg']['student_handbook'] = $csg['student_handbook'];
                    $data['misc_fee']['Entrance Exam Fee'] = $misc_fee['entrance_exam_fee'];
                    $data['misc_fee']['Medical and Dental Fee'] = $misc_fee['medical_fee'];
                }
                else
                {
                    $data['misc_fee']['Medical and Dental Fee'] = $misc_fee['medical_fee_old'];
                }
                $data['srf'] = $srf;
                $data['sfdf'] = $sfdf;
            }
        
        }
    
        $data['total'] = $tuition + $total_lab + $data['id_fee'] + $afee + $data['misc_fee']['Registration'] + $data['misc_fee']['Medical and Dental Fee']+ $data['misc_fee']['Entrance Exam Fee'] + $data['misc_fee']['Guidance Fee'] + $data['misc_fee']['Library Fee'] +  $data['srf'] +  $data['sfdf'] + $data['csg']['student_publication'] + $data['csg']['student_handbook'];
        $data['lab'] = $total_lab;
        $data['lab_list'] = $lab_list;
        $data['tuition'] = $tuition;
        $data['athletic'] = $afee;
        
        
        return $data;
        
    }

    function getMaxCurrentStudentNumber($sem){
        $term = switch_num($sem['enumSem']);
        $year = $sem['strYearStart'];

        return $this->db->where(array(
            'strStudentNumber LIKE' => 'C%'.$year.$term.'%'
        ))
        ->get('tb_mas_users')
        ->order_by('strStudentNumber desc')
        ->first_row('array');
    }
    
    function getTuitionSubjects($stype,$unit_fee,$misc_fee,$lab_fee,$athletic_fee,$id_fee,$srf,$sfdf,$csg,$scholarship,$subjects)
    {
        $tuition = 0;
        $total_lab = 0;
        $afee = 0;
        $data['id_fee'] = 0;
        $data['csg']['student_handbook'] = 0;
        $data['csg']['student_publication'] = 0;
        $lab_list = array();
        $data['misc_fee']['Registration'] = 0;
        $data['misc_fee']['Guidance Fee'] = 0;
        $data['misc_fee']['Entrance Exam Fee'] = 0;
        $data['misc_fee']['Medical and Dental Fee'] = 0;
        $data['misc_fee']['Library Fee'] = 0;
        $data['srf'] = 0;
        $data['sfdf'] = 0;
        
        if($scholarship != "resident scholar") { //&& $scholarship != "FREE HIGHER EDUCATION PROGRAM (R.A. 10931)"){
            
            $data['misc_fee']['Registration'] = $misc_fee['registration'];
            $data['misc_fee']['Guidance Fee'] = $misc_fee['guidance_fee'];
            $data['misc_fee']['Library Fee'] = $misc_fee['library_fee'];
            $data['csg']['student_publication'] = $csg['student_publication'];
            
            if($scholarship != "DILG scholar")
            {
            foreach($subjects as $sid)
            {
                $class =  current($this->db
                             ->select("*")
                             ->from("tb_mas_subjects")
                             ->where(array("intID"=>$sid))
                             ->get()
                             ->result_array());

            


            
                $tuition += intval($class['strUnits'])*$unit_fee;
                $class['intLab'] = $class['intLab']/3;
                if($class['intLab'] != 0){
                    $lab_list[$class['strCode']] = $lab_fee*$class['intLab'];
                    $total_lab += $lab_list[$class['strCode']];
                }

                if($class['intAthleticFee'] != 0){
                    $afee += $athletic_fee;
                }



            }
            }

            if($stype != "old")
            {
                $data['id_fee'] = $id_fee;
                $data['csg']['student_handbook'] = $csg['student_handbook'];
                $data['misc_fee']['Entrance Exam Fee'] = $misc_fee['entrance_exam_fee'];
                $data['misc_fee']['Medical and Dental Fee'] = $misc_fee['medical_fee'];
            }
            else
            {
                $data['misc_fee']['Medical and Dental Fee'] = $misc_fee['medical_fee_old'];
            }
            
            $data['srf'] = $srf;
            if($scholarship != "tagaytay resident")
            {
                $data['sfdf'] = $sfdf;
            }
        }
    
        $data['total'] = $tuition + $total_lab + $data['id_fee'] + $afee + $data['misc_fee']['Registration'] + $data['misc_fee']['Medical and Dental Fee'] + $data['misc_fee']['Entrance Exam Fee'] + $data['misc_fee']['Guidance Fee'] + $data['misc_fee']['Library Fee'] +  $data['srf'] +  $data['sfdf'] + $data['csg']['student_publication'] + $data['csg']['student_handbook'];
        $data['lab'] = $total_lab;
        $data['lab_list'] = $lab_list;
        $data['tuition'] = $tuition;
        $data['athletic'] = $afee;
        
        
        return $data;
        
    }
    
    
    function getTransactions($id,$sem)
    {
        return  $this->db
                     ->select('intTransactionID, SUM(intAmountPaid) as totalAmountPaid, dtePaid, intORNumber')
                     ->from('tb_mas_transactions')
                     ->where(array('intRegistrationID'=>$id,'intAYID'=>$sem))
                     ->group_by('intORNumber')
                     ->get()
                     ->result_array();
    }
    
    function getTransactionsOR($or)
    {
        return  $this->db
                     ->select('intTransactionID, intAmountPaid, dtePaid, strTransactionType, intORNumber')
                     ->from('tb_mas_transactions')
                     ->where(array('intORNumber'=>$or))
                     ->get()
                     ->result_array();
    }
    
    function getTransactionsPayment($id,$sem)
    {
        return  $this->db
                     ->select('intTransactionID, intAmountPaid, strTransactionType')
                     ->from('tb_mas_transactions')
                     ->where(array('intRegistrationID'=>$id,'intAYID'=>$sem))
                     ->get()
                     ->result_array();
    }
    
    function getSemStudent($id)
    {
        return  $this->db
                     ->select("intID,enumSem,strYearStart,strYearEnd,enumStatus,enumFinalized")
                     ->from("tb_mas_sy")
                     //->group_by("intSubjectID")
                     ->where(array("intStudentID"=>$id))
                     ->join('tb_mas_registration', 'tb_mas_registration.intAYID = tb_mas_sy.intID')
                     ->get()
                     ->result_array();
    }
    
    function generateStudentNumber($year)
    {
        $st = true;
        $sem = current($this->db->get_where('tb_mas_sy',array('enumStatus'=>'active'))->result_array());
        while($st){       
            $snum = $year."0".$sem['enumSem'][0].rand(1000,9999);
            
            $array = $this->db->get_where('tb_mas_users',array('strStudentNumber'=>$snum))->result_array();
            if(empty($array))
                $st = false;
            
        }
        
        return $snum;
    }
    
    function generateAppNumber($year)
    {
        $st = true;
        $sem = current($this->db->get_where('tb_mas_sy',array('enumStatus'=>'active'))->result_array());
        while($st){       
            $snum = $year."0".$sem['enumSem'][0].rand(1000,9999);
            
            $array = $this->db->get_where('tb_mas_applications',array('strAppNumber'=>$snum))->result_array();
            if(empty($array))
                $st = false;
            
        }
        
        return $snum;
    }
    
    function generateConfirmationCode($year)
    {
        $st = true;
        $sem = current($this->db->get_where('tb_mas_sy',array('enumStatus'=>'active'))->result_array());
        while($st){       
            $snum = $year."0".$sem['enumSem'][0].rand(1000,9999);
            $snum = md5($snum);
            $snum = substr($snum,0,20);
            
            $array = $this->db->get_where('tb_mas_applications',array('strConfirmationCode'=>$snum))->result_array();
            if(empty($array))
                $st = false;
            
        }
        
        return $snum;
    }
    
    function generatePassword()
    {
        $snum = substr(pw_hash(date("hisY")),5,5).rand(1000,9999);
        return $snum;
    }
    
    function getProgramDetails($id)
    {
        return current($this->db->get_where('tb_mas_programs',array('intProgramID'=>$id))->result_array());
       /* return  $this->db
                     ->select("*")
                     ->from("tb_mas_programs")
                     //->group_by("intSubjectID")
                     ->where(array("intProgramID"=>$id))
                     ->join('tb_mas_users', 'tb_mas_users.intID = tb_mas_classlist_student.intStudentID')
                     ->order_by('strLastName','asc')
                     ->get()
                     ->result_array();*/
        
    }
     function getSectionDetails($id)
    {
        return current($this->db->get_where('tb_mas_classlist',array('strSection'=>$id))->result_array());
       /* return  $this->db
                     ->select("*")
                     ->from("tb_mas_programs")
                     //->group_by("intSubjectID")
                     ->where(array("intProgramID"=>$id))
                     ->join('tb_mas_users', 'tb_mas_users.intID = tb_mas_classlist_student.intStudentID')
                     ->order_by('strLastName','asc')
                     ->get()
                     ->result_array();*/
        
    }
    
    
    function generateOR()
    {
        $st = true;
        $date = date("njy");
        while($st){
            $or = $date.rand(1000,9999);
            $array = $this->db->get_where('tb_mas_transactions',array('intORNumber'=>$or))->result_array();
            if(empty($array))
                $st = false;
            
        }
        
        return $or;
    }

    function getCompletion($id){
        $query = "SELECT * from tb_mas_completion WHERE intClasslistStudentID = ".$id;
        
        return current($this->db
                    ->query($query)
                    ->result_array());
    }

    function getCompletionByID($id){
        $query = "SELECT * from tb_mas_completion WHERE intCompletionID = ".$id;
        
        return current($this->db
                    ->query($query)
                    ->result_array());
    }

    function getClasslistStudent($id){
        return  current($this->db
                     ->select("tb_mas_classlist_student.intCSID, tb_mas_classlist.intID, tb_mas_classlist.strSignatory1Name, tb_mas_classlist.strSignatory2Name, tb_mas_faculty.strFirstname as facFname,tb_mas_faculty.strLastname as facLname, tb_mas_users.strFirstname,tb_mas_users.strMiddlename,tb_mas_users.strLastname,strStudentNumber, strCode, strDescription, tb_mas_classlist_student.floatFinalGrade,floatPrelimGrade,floatMidtermGrade,floatFinalsGrade,tb_mas_classlist_student.enumStatus,strRemarks, strProgramCode, strMajor, tb_mas_sy.enumSem, tb_mas_sy.strYearStart, tb_mas_sy.strYearEnd")
                     ->from("tb_mas_classlist_student")
                     //->group_by("intSubjectID")
                     ->where(array("intCSID"=>$id))
                     ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_classlist_student.intClassListID')
                     ->join('tb_mas_subjects','tb_mas_classlist.intSubjectID = tb_mas_subjects.intID')
                     ->join('tb_mas_users', 'tb_mas_users.intID = tb_mas_classlist_student.intStudentID')
                     ->join('tb_mas_programs','tb_mas_programs.intProgramID = tb_mas_users.intProgramID')
                     ->join('tb_mas_sy','tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->get()
                     ->result_array());
    }
    
    function getClassListStudents($id)
    {
        $faculty_id = $this->session->userdata("intID");
        return  $this->db
                     ->select("tb_mas_classlist_student.intCSID,intID, strFirstname,strMiddlename,strLastname,strStudentNumber, strGSuiteEmail, tb_mas_classlist_student.floatFinalGrade,floatPrelimGrade,floatMidtermGrade,floatFinalsGrade,enumStatus,strRemarks, strUnits,strProgramCode")
                     ->from("tb_mas_classlist_student")
                     //->group_by("intSubjectID")
                     ->where(array("intClassListID"=>$id))
                     ->join('tb_mas_users', 'tb_mas_users.intID = tb_mas_classlist_student.intStudentID')
                     ->join('tb_mas_programs','tb_mas_programs.intProgramID = tb_mas_users.intProgramID')
                     ->order_by('strLastName asc, strFirstname asc')
                     ->get()
                     ->result_array();
    }
    
    function checkStudentSubject($sem,$subjectId,$studentId)
    {
        return  current($this->db
                     ->select("tb_mas_classlist.*,tb_mas_subjects.strCode")
                     ->from("tb_mas_classlist_student")
                     ->where(array("tb_mas_classlist_student.intStudentID"=>$studentId,"tb_mas_classlist.intSubjectID"=>$subjectId,"tb_mas_classlist.strAcademicYear"=>$sem))
                     ->join('tb_mas_classlist', 'tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID')
                     ->join('tb_mas_subjects','tb_mas_classlist.intSubjectID = tb_mas_subjects.intID')
                     ->get()
                     ->result_array());
    }
    function checkStudentSubjectTaken($subjectId,$studentId)
    {
        return  current($this->db
                     ->select("tb_mas_classlist.*,tb_mas_subjects.strCode")
                     ->from("tb_mas_classlist_student")
                     ->where(array("tb_mas_classlist_student.intStudentID"=>$studentId,"tb_mas_classlist.intSubjectID"=>$subjectId,))
                        ->where("tb_mas_classlist_student.floatFinalGrade !=",'5')
                     ->join('tb_mas_classlist', 'tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID')
                     ->join('tb_mas_subjects','tb_mas_classlist.intSubjectID = tb_mas_subjects.intID')
                     ->get()
                     ->result_array());
    }
    
    function checkClasslistStudentNSTP($id,$year) 
    {
               
        return  $this->db
                     ->select("strCode,strAcademicYear")
                     ->from("tb_mas_classlist_student")
                     ->where(array("intStudentID"=>$id,"strAcademicYear"=>$year,"strCode regexp"=>'NSTP'))                    
                     ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')                     
                     ->group_by('strAcademicYear')   
                     
                     ->get()
                     ->result_array();
        
    }
    
    
    function getClassListStudentsSt($id,$classlist) 
    {
               
        return  $this->db
                     ->select("tb_mas_classlist_student.intCSID,strCode,strSection , intLab, tb_mas_subjects.strDescription,tb_mas_classlist_student.floatFinalGrade as v3,intFinalized,enumStatus,strRemarks,tb_mas_faculty.intID as facID, tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_subjects.strUnits, tb_mas_subjects.intBridging, tb_mas_classlist.intID as classlistID, tb_mas_subjects.intID as subjectID")
                     ->from("tb_mas_classlist_student")
            
                    ->where(array("intStudentID"=>$id,"strAcademicYear"=>$classlist,))
                        
                        
                     ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->order_by('strCode','asc')   
                     ->get()
                     ->result_array();
        
    }
    
    function getClassListStudentsAndInfo($course = 0,$regular= 0, $year=0,$gender = 0,$graduate=0,$scholarship=0,$registered=0,$sem=0) 
    {
               
        return  $this->db
                     ->select("tb_mas_classlist_student.intCSID,strCode,strSection , intLab, tb_mas_subjects.strDescription,tb_mas_classlist_student.floatFinalGrade as v3,intFinalized,enumStatus,strRemarks,tb_mas_faculty.strFirstname,tb_mas_faculty.strLastname, tb_mas_subjects.strUnits, tb_mas_classlist.intID as classlistID, tb_mas_subjects.intID as subjectID")
                     ->from("tb_mas_classlist_student")
                        
                     ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->order_by('strCode','asc')   
                     ->get()
                     ->result_array();
        
    }
    
    function getClasslistDetails($id)
    {
        $d = $this->db
             ->select('strSection,intLab,intSubjectID,intLectHours,intFacultyID')
             ->from('tb_mas_classlist')
             ->join('tb_mas_subjects','intSubjectID = tb_mas_subjects.intID')
             ->where('tb_mas_classlist.intID',$id)
             ->get()
             ->first_row();
        
        return $d;
    }
    
    function getAllClasslist($sem,$dept = null,$admin=false)
    {
         $this->db
             ->select('tb_mas_classlist.*,tb_mas_subjects.strCode')
             ->from('tb_mas_classlist')
             ->join('tb_mas_subjects','intSubjectID = tb_mas_subjects.intID');
            
             $where['tb_mas_classlist.strAcademicYear'] = $sem;
             
            if($dept!=null && !$admin)
                 $where['tb_mas_subjects.strDepartment'] = $dept;
        
             $this->db->where($where);
            
           $d =
            $this->db
                 ->get()
                 ->result_array();
        
        return $d;
    }
    
    function getAllClasslistAssigned($sem,$dept = null,$admin=false)
    {
         $this->db
             ->select('tb_mas_classlist.*,tb_mas_subjects.strCode')
             ->from('tb_mas_classlist')
             ->join('tb_mas_subjects','intSubjectID = tb_mas_subjects.intID');
            
             $where['tb_mas_classlist.strAcademicYear'] = $sem;
             //$where['tb_mas_classlist.intFacultyID !='] = 999; filter for unassigned
            // if($dept!=null && !$admin)
            //      $where['tb_mas_subjects.strDepartment'] = $dept;
        
             $this->db->where($where);
            
           $d =
            $this->db
                 ->get()
                 ->result_array();
        
        return $d;
    }
    
    
    function getScheduleByCode($schedCode)
    {
       $ret = array();
       $sched = $this->db
                        ->select('intRoomSchedID,strDay, dteStart, dteEnd, strRoomCode,strSection,strCode')
                        ->from('tb_mas_room_schedule')
                        ->where(array('strScheduleCode'=>$schedCode))
                        ->join('tb_mas_classrooms','tb_mas_room_schedule.intRoomID = tb_mas_classrooms.intID')
                        ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode')
                        ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                        ->get()
                        ->result_array();
       foreach($sched as $s)
        {
            $s['strDay'] = get_day($s['strDay']);
            $ret[] = $s;
        }
        
        return $ret;
        
        
        
    }
    
    function getScheduleBySection($section,$sem)
    {
       $ret = array();
       $sched = $this->db
                        ->select('intRoomSchedID,strDay, dteStart, dteEnd, strRoomCode,strSection,strCode,strLastname,strFirstname')
                        ->from('tb_mas_room_schedule')
                        ->join('tb_mas_classrooms','tb_mas_room_schedule.intRoomID = tb_mas_classrooms.intID')
                        ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode')
                        ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                        ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                        ->where(array('tb_mas_classlist.strSection'=>$section,'tb_mas_room_schedule.intSem'=>$sem))
                        ->order_by('strDay ASC, dteStart ASC')
                        ->get()
                        ->result_array();
       foreach($sched as $s)
        {
            $s['strDay'] = get_day($s['strDay']);
            $ret[] = $s;
        }
        
        return $ret;
        
        
        
    }
    
    function getScheduleByRoomID($id,$sem)
    {
         $ret = array();
       $sched = $this->db
                        ->select('tb_mas_room_schedule.*,strSection,strCode,tb_mas_faculty.strLastname,tb_mas_faculty.strFirstname')
                        ->from('tb_mas_room_schedule')
                        ->where(array('intRoomID'=>$id,'tb_mas_room_schedule.intSem'=>$sem))
                        ->join('tb_mas_classrooms','tb_mas_room_schedule.intRoomID = tb_mas_classrooms.intID')
                        ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode')
                        ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                        ->join('tb_mas_faculty','tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                        ->order_by('strDay ASC, dteStart ASC')
                        ->get()
                        ->result_array();
       foreach($sched as $s)
        {
            $s['strDay'] = get_day($s['strDay']);
            $ret[] = $s;
        }
        
        return $ret;
    }
    
    function getSchedule($id,$dept=null,$admin=false)
    {
        
        $this->db
            ->select('tb_mas_room_schedule.*,strSection,strCode,tb_mas_subjects.strDepartment')
            ->from('tb_mas_room_schedule');
            
        $where = array('intRoomSchedID'=>$id);
        
        if($dept!=null && !$admin)
            $where['tb_mas_subjects.strDepartment'] = $dept;
        
        $this->db->where($where);

        $sched =
            $this->db
            ->join('tb_mas_classrooms','tb_mas_room_schedule.intRoomID = tb_mas_classrooms.intID')
            ->join('tb_mas_classlist','tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode')
            ->join('tb_mas_subjects','tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
            ->get()
            ->result_array();

         $sched = current($sched);
        if(!empty($sched))
            $sched['strDay'] = get_day($sched['strDay']);
        return $sched;
        
        
        
    }
    
    function get_subjects_by_course($curriculum)
    {
       $subjects = $this->db
                         ->select( 'tb_mas_subjects.intID,tb_mas_curriculum_subject.intYearLevel,tb_mas_curriculum_subject.intSem,tb_mas_subjects.strCode, tb_mas_subjects.strDescription')
                         ->from('tb_mas_subjects')
                         ->join('tb_mas_curriculum_subject','tb_mas_curriculum_subject.intSubjectID = tb_mas_subjects.intID')
                         ->where('tb_mas_curriculum_subject.intCurriculumID',$curriculum)
                         ->order_by('intYearLevel asc,intSem asc')
                         ->get()
                         ->result_array();
        
        return $subjects;
    }
    
    function get_curriculum_by_course($course)
    {
        $query = "SELECT * from tb_mas_curriculum WHERE intProgramID = ".$course;
        
        return $this->db
                    ->query($query)
                    ->result_array();
    }
   
    function fetch_classlist($id)
    {
        $faculty_id = $this->session->userdata("intID");
        return  $this->db
                     ->select("tb_mas_classlist.intID as intID, strSection, intFacultyID,intSubjectID, strClassName,strCode,intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd, tb_mas_classlist.strUnits as strUnits")
                     ->from("tb_mas_classlist")
                     //->group_by("intSubjectID")
                     ->where(array("intFacultyID"=>$faculty_id,"tb_mas_classlist.intID"=>$id))
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->get()
                     ->result_array();
    }
    
    function fetch_classlist_delete($id)
    {
        return  $this->db
                     ->select("intFinalized,intFacultyID")
                     ->from("tb_mas_classlist")
                     //->group_by("intSubjectID")
                     ->where(array("tb_mas_classlist.intID"=>$id))
                     ->get()
                     ->result_array();
    }
    
  
    /*
    newly added function - 10/23/2015 -
        fetching classlists by faculty
    */
    function fetch_classlist_by_faculty($id, $classlist)
    {
        $faculty_id = $id;
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, intFacultyID,intSubjectID, strClassName,strCode,strDescription, strSection, intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd, tb_mas_subjects.strUnits")
                     ->from("tb_mas_classlist")
                     ->where(array("intFacultyID"=>$faculty_id, "strAcademicYear"=>$classlist));

                     $this->db->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->order_by('strSection','asc');
                 return $this->db 
                        ->get()
                        ->result_array();
    }
    
    function fetch_classlist_by_sujects($id,$sem=null)
    {
        $subject_id = $id;
                    $this->db
                     ->select("tb_mas_classlist.intID as intID, intFacultyID,intSubjectID,strClassName,strCode,strSection,intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd, tb_mas_faculty.strLastname, tb_mas_faculty.strFirstname")
                     ->from("tb_mas_classlist")
                     ->where(array("intSubjectID"=>$subject_id,'strAcademicYear'=>$sem));
//                    if($sem)
//                        $this->db->where(array('enumStatus'=>'active'));
//                    
//                    if($sem_sel!=null)
//                        $this->db->where(array('strAcademicYear'=>$sem_sel));
                     $this->db->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                    ->order_by('strSection','asc');
                   
//                if($limit != null)
//                    $this->db->limit($limit);
                 return $this->db 
                        ->get()
                        ->result_array();
    }
     function fetch_sections_by_program($id,$sem=null)
    {
         $program_id= $id;
                    $this->db
                     ->select("tb_mas_classlist.intID, tb_mas_classlist.strSection")
                     ->from("tb_mas_classlist");
         
                     $this->db->where(array("tb_mas_classlist.strSection LIKE"=>$program_id . "%",'strAcademicYear'=>$sem))
                    ->group_by("strSection")
                    ->order_by('strSection','asc');
                   
                 return $this->db 
                        ->get()
                        ->result_array();
    }
     function fetch_classlist_by_section($id,$sem=null)
    {
         $sectionID = $id;
                    $this->db
                     ->select("tb_mas_classlist.intID, tb_mas_classlist.strClassName,tb_mas_classlist.intSubjectID, tb_mas_subjects.strDescription, COUNT(tb_mas_classlist_student.intStudentID) as intNumOfStudents, strLastname, strFirstname")
                     ->from("tb_mas_classlist")
                     ->where(array("tb_mas_classlist.strSection"=>$sectionID,'strAcademicYear'=>$sem))
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_classlist_student', 'tb_mas_classlist_student.intClassListID = tb_mas_classlist.intID', 'Left')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->order_by('strClassName','asc');
         
                 return $this->db 
                        ->group_by('tb_mas_classlist.intID')
                        ->get()
                        ->result_array();
    }
     /**********************************************
     newly added function -11-04-2014 ^______^
    *********************************************/
    function getTotalUnits($id) 
    {
                    return $this->db->select_sum('tb_mas_classlist_student.strUnits')
                     ->from("tb_mas_classlist_student")
                     ->where(array("intStudentID"=>$id))
                     ->join('tb_mas_classlist', 'tb_mas_classlist.intID = tb_mas_classlist_student.intClasslistID')
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_faculty', 'tb_mas_faculty.intID = tb_mas_classlist.intFacultyID')
                     ->get()
                     ->result_array();

    }

    function fetch_classlist_all($id)
    {
        //$faculty_id = $this->session->userdata("intID");
        return  $this->db
                     ->select("tb_mas_classlist.intID as intID, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd")
                     ->from("tb_mas_classlist")
                     //->group_by("intSubjectID")
                     ->where(array("tb_mas_classlist.intID"=>$id))
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->get();
                    
    }
    
    function fetch_classlist_section($subject,$section)
    {
        //$faculty_id = $this->session->userdata("intID");
        return  current($this->db
                     ->select("tb_mas_classlist.intID as intID, intFacultyID,intSubjectID,strClassName,strCode,intFinalized,strAcademicYear,enumSem,strYearStart,strYearEnd")
                     ->from("tb_mas_classlist")
                     //->group_by("intSubjectID")
                     ->where(array("tb_mas_classlist.strClassName"=>$subject,"tb_mas_classlist.intSubjectID"=>$section))
                     ->join('tb_mas_subjects', 'tb_mas_subjects.intID = tb_mas_classlist.intSubjectID')
                     ->join('tb_mas_sy', 'tb_mas_sy.intID = tb_mas_classlist.strAcademicYear')
                     ->get()
                     ->result_array()
                       );
                    
    }
    
    //Room Conflict
    function schedule_conflict($post,$id=null,$sem,$d=null)
    {
        $res = array();
        
        if($post['intRoomID'] != 99999){ //IF room is TBA
            $query ="SELECT intRoomSchedID,strCode,strSection
                    FROM tb_mas_room_schedule
                    JOIN tb_mas_classlist ON tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode
                    JOIN tb_mas_subjects ON tb_mas_classlist.intSubjectID = tb_mas_subjects.intID
                    WHERE
                    (
                    (dteStart >= '".$post['dteStart']."' AND dteEnd <= '".$post['dteEnd']."') OR
                    (dteStart < '".$post['dteEnd']."' AND dteEnd >= '".$post['dteEnd']."') OR 
                    (dteStart <= '".$post['dteStart']."' AND dteEnd > '".$post['dteStart']."') 
                    )";
            if($id!=null)
            {
                $query .= " AND intRoomSchedID != ".$id;
            }

            if($d == null)
                $query .=" AND strDay = '".$post['strDay']."' AND intRoomID = '".$post['intRoomID']."' AND tb_mas_room_schedule.intSem = ".$sem."
                    ";
            else
            {
                $query .=" AND ( ";
                for($i=0;$i<count($d);$i++)
                {
                    if($i == count($d)-1)
                        $query .="strDay = ".$d[$i]." ) ";
                    else
                        $query .="strDay = ".$d[$i]." OR ";
                }

                $query .= "AND intRoomID = '".$post['intRoomID']."' AND tb_mas_room_schedule.intSem = ".$sem." ";
            }

            // echo $query."<br />";
            //print_r($this->db->query($query)->result_array());
            //die();
            $res = $this->db->query($query)->result_array();
        }
        
        return $res;
    }
    
    function section_conflict($post,$id=null,$section,$sem,$d=null)
    {
        $query ="SELECT intRoomSchedID,strCode,strSection
                FROM tb_mas_room_schedule
                JOIN tb_mas_classlist ON tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode
                JOIN tb_mas_subjects ON tb_mas_classlist.intSubjectID = tb_mas_subjects.intID
                WHERE
                (
                (dteStart >= '".$post['dteStart']."' AND dteEnd <= '".$post['dteEnd']."') OR
                (dteStart < '".$post['dteEnd']."' AND dteEnd >= '".$post['dteEnd']."') OR 
                (dteStart <= '".$post['dteStart']."' AND dteEnd > '".$post['dteStart']."')
                )";
        if($id!=null)
        {
            $query .= " AND intRoomSchedID != ".$id;
        }
        if($d == null)
            $query .=" AND strDay = ".$post['strDay']." AND strSection = '".$section."' AND tb_mas_room_schedule.intSem = ".$sem;
        else
        {
            $query .=" AND ( ";
            for($i=0;$i<count($d);$i++)
            {
                if($i == count($d)-1)
                    $query .="strDay = ".$d[$i]." ) ";
                else
                    $query .="strDay = ".$d[$i]." OR ";
            }
            $query .= "AND strSection = '".$section."' AND tb_mas_room_schedule.intSem = ".$sem;
        }
        // echo $query."<br />";
        //print_r($this->db->query($query)->result_array());
        //die();
        return $this->db->query($query)->result_array();
    }
    
    function faculty_conflict($post,$id=null,$faculty_id,$sem,$d=null)
    {
        $query ="SELECT intRoomSchedID,strCode,strSection
                FROM tb_mas_room_schedule
                JOIN tb_mas_classlist ON tb_mas_classlist.intID = tb_mas_room_schedule.strScheduleCode
                JOIN tb_mas_subjects ON tb_mas_classlist.intSubjectID = tb_mas_subjects.intID
                WHERE
                (
                (dteStart >= '".$post['dteStart']."' AND dteEnd <= '".$post['dteEnd']."') OR
                (dteStart < '".$post['dteEnd']."' AND dteEnd >= '".$post['dteEnd']."') OR 
                (dteStart <= '".$post['dteStart']."' AND dteEnd > '".$post['dteStart']."')
                )";
        if($id!=null)
        {
            $query .= " AND intRoomSchedID != ".$id;
        }
        if($d == null)
            $query .=" AND strDay = '".$post['strDay']."' AND `intFacultyID` = ".$faculty_id." AND tb_mas_room_schedule.intSem = ".$sem." ";
        else
        {
            $query .=" AND ( ";
            for($i=0;$i<count($d);$i++)
            {
                if($i == count($d)-1)
                    $query .="strDay = ".$d[$i]." ) ";
                else
                    $query .="strDay = ".$d[$i]." OR ";
            }
            
            $query .= "AND `intFacultyID` = ".$faculty_id." AND tb_mas_room_schedule.intSem = ".$sem." ";
        }
        // echo $query."<br />";
        //print_r($this->db->query($query)->result_array());
        //die();
        return $this->db->query($query)->result_array();
    }
}