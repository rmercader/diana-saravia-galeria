<?php

class PublicController extends CI_Controller {

	protected $imgDestacadas;

	public function __construct()
    {
        parent::__construct();
        $this->load->model('obras_model');
        $this->imgDestacadas = $this->obras_model->obtener_destacadas_ordenadas();
    }
}