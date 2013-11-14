<?php

include_once('public_controller.php');

class Eventos extends PublicController {

	/**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        // Models
        $this->load->model('eventos_model');

        // Helpers
        $this->load->helper('file');
    }

	public function index(){
		//load the view
        date_default_timezone_set('America/Montevideo');
		$data['eventos'] = $this->eventos_model->get_eventos(null, 'fecha');
        $data["imgDestacadas"] = $this->imgDestacadas;
		$data['main_content'] = 'publico/eventos';
        $this->load->view('publico/template', $data);
	}

    public function preview(){
		$id = $this->uri->segment(3);
		$fileName = "./uploads/eventos/" . $id . ".prv.jpg";
        $imgBytes = read_file($fileName);
        if($imgBytes){
        	$this->output
	            ->set_content_type("image/jpeg")
	            ->set_output($imgBytes);
        }
        else {
        	echo "Imagen de evento no encontrada: $fileName";
        }
	}

}