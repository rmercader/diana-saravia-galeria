<?php

include_once('public_controller.php');

class Obras extends PublicController {

	/**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        // Models
        $this->load->model('obras_model');
        $this->load->model('categorias_obras_model');

        // Helpers
        $this->load->helper('file');
    }

	public function index(){
		//load the view
        $categorias = $this->categorias_obras_model->get_categorias_con_obras(null, 'orden');
        $catsToPage = array();
        foreach ($categorias as $c) {
            array_push($catsToPage, array(
                'class'=>'',
                'nombre_categoria_obra'=>$c['nombre_categoria_obra'],
                'id_categoria_obra'=>$c['id_categoria_obra']
            ));
        }
        if(count($catsToPage) > 0){
            $catsToPage[0]['class'] = 'selected';
        }
		$data['categorias'] = $catsToPage;
        $idCategoria = $catsToPage[0]['id_categoria_obra'];        
        $data['nombre_categoria_obra'] = $catsToPage[0]['nombre_categoria_obra'];
        $data['obras'] = $this->obras_model->get_obras_categoria($idCategoria, 'nombre_artista');

        $data["imgDestacadas"] = $this->imgDestacadas;
		$data['main_content'] = 'publico/exposiciones';
        $this->load->view('publico/template', $data);
	}

    public function imagen(){
        $id = $this->uri->segment(3);
        $fileName = "./uploads/obras/" . $id . ".jpg";
        $imgBytes = read_file($fileName);
        if($imgBytes){
            $this->output
                ->set_content_type("image/jpeg")
                ->set_output($imgBytes);
        }
        else {
            echo "Imagen de obra no encontrada: $fileName";
        }
    }

    public function imagenGaleria(){
        $id = $this->uri->segment(3);
        $fileName = "./uploads/obras/" . $id . OBRA_IMAGE_GALLERY_MARKER . ".jpg";
        $imgBytes = read_file($fileName);
        if($imgBytes){
            $this->output
                ->set_content_type("image/jpeg")
                ->set_output($imgBytes);
        }
        else {
            echo "Imagen preview de obra no encontrada: $fileName";
        }
    }

    public function preview(){
        $id = $this->uri->segment(3);
        $fileName = "./uploads/obras/" . $id . OBRA_IMAGE_PREVIEW_MARKER . ".jpg";
        $imgBytes = read_file($fileName);
        if($imgBytes){
            $this->output
                ->set_content_type("image/jpeg")
                ->set_output($imgBytes);
        }
        else {
            echo "Imagen preview de obra no encontrada: $fileName";
        }
    }

    public function obrasPorCategoria(){
        $response = array();
        $error = "";
        $id_categoria_obra = (int) $this->input->post('id_categoria_obra');
        
        if($id_categoria_obra > 0){
            $response["obras"] = $this->obras_model->get_obras_categoria($id_categoria_obra, 'nombre_artista');
        } else {
            $error = "Se ha recibido un identificador de categoria invalido.";
        }

        if($error == ""){
            $response["success"] = true;
        }
        else {
            $response["success"] = false;
            $response["mensaje"] = $error;
        }
        
        $data['json_content'] = json_encode($response);
        $this->load->view('includes/jsoncontent', $data);
    }

}