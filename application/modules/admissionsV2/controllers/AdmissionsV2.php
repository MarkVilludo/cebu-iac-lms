<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

	
class Admissionsv2 extends CI_Controller {

	
    function __construct() {
        parent::__construct();
		/*--------------THEMES-----------------------*/
		// $this->config->load('themes');
		// $theme = $this->config->item('users');
		// $theme = 'unity';
			
		// $this->data['img_dir'] = base_url()."assets/themes/".$theme."/images/";
		// $this->data['css_dir'] = base_url()."assets/themes/".$theme."/css/";
		// $this->data['js_dir'] = base_url()."assets/themes/".$theme."/js/";		
		// $this->theme = $theme;
		// $this->data['logged_in'] = $this->session->userdata('user_logged');

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
        $this->data['title'] = "iACADEMY - Cebu";
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
        $this->data['page'] = "subjects";
		

    }
	
	
    public function index() {
        
        $this->load->view('common/header',$this->data);        
		$this->load->view('studentApplication',$this->data);
		$this->load->view('common/footer',$this->data);
    }

	 public function lead_list() {
        
        $this->load->view('common/header',$this->data);        
		$this->load->view('leadList',$this->data);
		$this->load->view('common/footer',$this->data);
    }
    public function calendar() {
        
        $this->load->view('common/header',$this->data);        
		$this->load->view('calendar',$this->data);
		$this->load->view('common/footer',$this->data);
    }

   }

?>