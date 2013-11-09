<?php

class Artistas extends CI_Controller {

	/**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('artistas_model');
        $this->load->model('obras_model');
    }

	public function index(){
		//load the view
		$data['artistas'] = $this->artistas_model->get_lista_artistas('nombre_artista');
		$data['main_content'] = 'publico/artistas';
        $this->load->view('publico/template', $data);
	}

	public function detalle(){
		$id = $this->uri->segment(3);
		// Cargo los datos del artista
		$artista = $this->artistas_model->get_artista_by_id($id);
		$artista = $artista[0];
		$data['nombre_artista'] = $artista['nombre_artista'];
		$data['detalles'] = $artista['detalles'];
		$data['obrasArtista'] = $this->obras_model->obras_por_artista($id);
		$data['main_content'] = 'publico/detalle_artista';
        $this->load->view('publico/template', $data);
	}

}