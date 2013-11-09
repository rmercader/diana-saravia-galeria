<?php

class Sitio extends CI_Controller {

	/**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mensajes_model');
    }

	public function index(){
		//load the view
		$data['main_content'] = 'publico/index';
        $this->load->view('publico/template', $data);
	}

	public function la_galeria(){
		//load the view
		$data['main_content'] = 'publico/galeria';
        $this->load->view('publico/template', $data);
	}

	public function diana(){
		//load the view
		$data['main_content'] = 'publico/diana';
        $this->load->view('publico/template', $data);
	}

	public function contacto(){
		//load the view
		$data['main_content'] = 'publico/contacto';
        $this->load->view('publico/template', $data);
	}

}