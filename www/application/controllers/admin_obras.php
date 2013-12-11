<?php
class Admin_obras extends CI_Controller {

    private $uploadConfig;
    private $imgLibConfig;

    /**
    * name of the folder responsible for the views 
    * which are manipulated by this controller
    * @constant string
    */
    const VIEW_FOLDER = 'admin/obras';
 
    /**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('obras_model');
        $this->load->model('categorias_obras_model');
        $this->load->model('artistas_model');

        // Helpers
        $this->load->helper('file');
        $this->load->helper('url');

        // Manejo de uploads
        $this->uploadConfig = array();
        $this->uploadConfig['upload_path'] = './uploads/obras/';
        $this->uploadConfig['allowed_types'] = 'jpg';
        $this->uploadConfig['overwrite'] = true;
        $this->load->library('upload');

        // Manejo de imagenes
        $this->imgLibConfig = array();
        $this->imgLibConfig['image_library'] = 'gd2';
        $this->imgLibConfig['create_thumb'] = TRUE;
        $this->imgLibConfig['maintain_ratio'] = FALSE;
        $this->imgLibConfig['quality'] = OBRA_IMAGE_QUALITY;
        $this->load->library('image_lib'); 

        if(!$this->session->userdata('is_logged_in')){
            redirect('admin/login');
        }
    }
 
    /**
    * Load the main view with all the current model model's data.
    * @return void
    */
    public function index()
    {

        //all the posts sent by the view
        $search_string = $this->input->post('search_string');        
        $order = $this->input->post('order'); 
        $order_type = $this->input->post('order_type'); 

        //pagination settings
        $config['per_page'] = 5;

        $config['base_url'] = base_url().'admin/obras';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data['order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        

        $data['thumbnailWidth'] = OBRA_THUMB_WIDTH;
        $data['thumbnailHeight'] = OBRA_THUMB_HEIGHT;
        $data['previewWidth'] = OBRA_PREVIEW_WIDTH;
        $data['previewHeight'] = OBRA_PREVIEW_HEIGHT;

        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if($search_string !== false && $order !== false || $this->uri->segment(3) == true){ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */
            if($search_string){
                $filter_session_data['search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data['order'] = $order;
            }
            else{
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            if(isset($filter_session_data)){
              $this->session->set_userdata($filter_session_data);    
            }
            
            //fetch sql data into arrays
            $data['count_obras']= $this->obras_model->count_obras($search_string, $order);
            $config['total_rows'] = $data['count_obras'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['obras'] = $this->obras_model->get_obras($search_string, $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['obras'] = $this->obras_model->get_obras($search_string, '', $order_type, $config['per_page'],$limit_end);           
                }
            }else{
                if($order){
                    $data['obras'] = $this->obras_model->get_obras('', $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['obras'] = $this->obras_model->get_obras('', '', $order_type, $config['per_page'],$limit_end);        
                }
            }

        }else{

            //clean filter data inside section
            $filter_session_data['obra_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_obras']= $this->obras_model->count_obras();
            $data['obras'] = $this->obras_model->get_obras('', '', $order_type, $config['per_page'],$limit_end);        
            $config['total_rows'] = $data['count_obras'];

        }//!isset($search_string) && !isset($order)
         
        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/obras/list';
        $this->load->view('includes/template', $data);  

    }//index

    public function add()
    {
        ini_set("display_errors", "on");
        ini_set("memory_limit", "64M");
        error_reporting(E_ALL);
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('nombre_obra', 'Nombre', 'required');
            $this->form_validation->set_rules('id_artista', 'Artista', 'required');
            $this->form_validation->set_rules('id_categoria_obra', 'Categoría', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $nombre_obra = $this->input->post('nombre_obra');
                $id_artista = $this->input->post('id_artista');
                if($this->input->post('destacada')){
                    $destacada = 1;
                }
                else {
                    $destacada = 0;
                }
                $data_to_store = array(
                    'nombre_obra' => $nombre_obra,
                    'id_artista' => $id_artista,
                    'id_categoria_obra' => $this->input->post('id_categoria_obra'),
                    'destacada' => $destacada
                );

                //if the insert has returned id then we show the flash message
                $idObra = $this->obras_model->store_obra($data_to_store); 
                $nombreArtista = $this->artistas_model->get_nombre($id_artista);
                if(is_numeric($idObra)){
                    $data_to_store = array('ficha' => url_title("$idObra $nombreArtista $nombre_obra", '-', true));                    
                    $data['flash_message'] = $this->obras_model->update_obra($idObra, $data_to_store); 
                    
                    $this->uploadConfig['file_name'] = $idObra;
                    $this->upload->initialize($this->uploadConfig);
                    if($this->upload->do_upload('imagen_obra')){
                        $upload_data = $this->upload->data();

                        // Salvo imagen galeria
                        $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_GALLERY_MARKER;
                        $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['width'] = OBRA_GALLERY_WIDTH;
                        $this->imgLibConfig['height'] = OBRA_GALLERY_HEIGHT;
                        $this->image_lib->initialize($this->imgLibConfig); 
                        if(! $this->image_lib->resize())
                        {
                            $data['error'] = $this->image_lib->display_errors() . "<br>";
                            log_message("error", $this->image_lib->display_errors());
                        }
                        else {
                            // Salvo imagen preview
                            $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                            $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_PREVIEW_MARKER;
                            $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                            $this->imgLibConfig['width'] = OBRA_PREVIEW_WIDTH;
                            $this->imgLibConfig['height'] = OBRA_PREVIEW_HEIGHT;
                            $this->image_lib->initialize($this->imgLibConfig); 
                            if(! $this->image_lib->resize())
                            {
                                $data['error'] = $this->image_lib->display_errors() . "<br>";
                                log_message("error", $this->image_lib->display_errors());
                            } else {
                                // Salvo imagen thumbnail
                                $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_THUMB_MARKER;
                                $this->imgLibConfig['width'] = OBRA_THUMB_WIDTH;
                                $this->imgLibConfig['height'] = OBRA_THUMB_HEIGHT;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($this->imgLibConfig); 
                                if(! $this->image_lib->resize())
                                {
                                    $data['error'] .= $this->image_lib->display_errors() . "<br>";
                                    log_message("error", $this->image_lib->display_errors());
                                }
                                else {
                                    // Finalmente grabo la imagen al tamano maximo
                                    $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                                    $this->imgLibConfig['thumb_marker'] = '';
                                    $this->imgLibConfig['width'] = OBRA_IMAGE_WIDTH;
                                    $this->imgLibConfig['height'] = OBRA_IMAGE_HEIGHT;
                                    $this->imgLibConfig['create_thumb'] = FALSE;
                                    $this->imgLibConfig['maintain_ratio'] = FALSE;
                                    $this->image_lib->initialize($this->imgLibConfig); 
                                    if(! $this->image_lib->resize())
                                    {
                                        $data['error'] = $this->image_lib->display_errors() . "<br>";
                                        log_message("error", $this->image_lib->display_errors());
                                    }
                                }
                            }
                        }
                    } else {
                        $upload_data = $this->upload->data();
                        if(is_array($upload_data) && !empty($upload_data['file_name'])){
                            $data['error'] = $this->upload->display_errors() . "<br>";    
                        }
                    } 
                } else {
                    $data['flash_message'] = FALSE; 
                } 
            }
        }
        //load the view
        $data['main_content'] = 'admin/obras/add';
        $this->load->view('includes/template', $data);  
    }       

    /**
    * Update item by his id
    * @return void
    */
    public function update()
    {
        ini_set("display_errors", "on");
        ini_set("memory_limit", "64M");
        error_reporting(E_ALL);
        //obra id 
        $id = $this->uri->segment(4);

        $data['previewWidth'] = OBRA_PREVIEW_WIDTH;
        $data['previewHeight'] = OBRA_PREVIEW_HEIGHT;
  
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form validation
            $this->form_validation->set_rules('nombre_obra', 'Nombre', 'required');
            $this->form_validation->set_rules('id_artista', 'Artista', 'required');
            $this->form_validation->set_rules('id_categoria_obra', 'Categoría', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $nombre_obra = $this->input->post('nombre_obra');
                $id_artista = $this->input->post('id_artista');
                $nombreArtista = $this->artistas_model->get_nombre($id_artista);
                if($this->input->post('destacada')){
                    $destacada = 1;
                }
                else {
                    $destacada = 0;
                }

                $data_to_store = array(
                    'nombre_obra' => $nombre_obra,
                    'id_artista' => $id_artista,
                    'id_categoria_obra' => $this->input->post('id_categoria_obra'),
                    'destacada' => $destacada
                );

                //if the insert has returned true then we show the flash message
                if($this->obras_model->update_obra($id, $data_to_store) == TRUE){
                    $data_to_store = array('ficha' => url_title("$id $nombreArtista $nombre_obra", '-', true));                    
                    $data['flash_message'] = $this->obras_model->update_obra($id, $data_to_store); 
                    $this->uploadConfig['file_name'] = $id;
                    $this->uploadConfig['overwrite'] = true;
                    $this->upload->initialize($this->uploadConfig);

                    if($this->upload->do_upload('imagen_obra')){
                        $upload_data = $this->upload->data();

                        // Salvo la imagen galeria
                        $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_GALLERY_MARKER;
                        $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['width'] = OBRA_GALLERY_WIDTH;
                        $this->imgLibConfig['height'] = OBRA_GALLERY_HEIGHT;
                        $this->image_lib->initialize($this->imgLibConfig);     
                        if(!$this->image_lib->resize())
                        {
                            $data['error'] = $this->image_lib->display_errors() . "<br>";
                        } else {
                            // Salvo imagen preview
                            $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                            $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_PREVIEW_MARKER;
                            $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                            $this->imgLibConfig['width'] = OBRA_PREVIEW_WIDTH;
                            $this->imgLibConfig['height'] = OBRA_PREVIEW_HEIGHT;
                            $this->image_lib->initialize($this->imgLibConfig); 
                            if(!$this->image_lib->resize())
                            {
                                $data['error'] = $this->image_lib->display_errors() . "<br>";
                            } else {
                                // Salvo imagen thumbnail
                                $this->imgLibConfig['thumb_marker'] = OBRA_IMAGE_THUMB_MARKER;
                                $this->imgLibConfig['width'] = OBRA_THUMB_WIDTH;
                                $this->imgLibConfig['height'] = OBRA_THUMB_HEIGHT;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($this->imgLibConfig); 
                                if(! $this->image_lib->resize())
                                {
                                    $data['error'] .= $this->image_lib->display_errors() . "<br>";
                                }
                                else {
                                    // Finalmente grabo la imagen al tamano maximo
                                    $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                                    $this->imgLibConfig['thumb_marker'] = '';
                                    $this->imgLibConfig['width'] = OBRA_IMAGE_WIDTH;
                                    $this->imgLibConfig['height'] = OBRA_IMAGE_HEIGHT;
                                    $this->imgLibConfig['create_thumb'] = FALSE;
                                    $this->imgLibConfig['maintain_ratio'] = FALSE;
                                    $this->image_lib->initialize($this->imgLibConfig); 
                                    if(! $this->image_lib->resize())
                                    {
                                        $data['error'] = $this->image_lib->display_errors() . "<br>";
                                    }
                                }
                            }
                        }

                    } else {
                        $upload_data = $this->upload->data();
                        if(is_array($upload_data) && !empty($upload_data['file_name'])){
                            $data['error'] = $this->upload->display_errors() . "<br>";    
                        }
                    } 

                    $this->session->set_flashdata('flash_message', 'updated');
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/obras/update/'.$id.'');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //obra data 
        $data['obra'] = $this->obras_model->get_obra_by_id($id);
        //load the view
        $data['main_content'] = 'admin/obras/edit';
        $this->load->view('includes/template', $data);            

    }//update

    public function thumbnail(){
        $id = $this->uri->segment(4);
        $imgBytes = read_file($this->uploadConfig['upload_path'] . $id . ".thu." . $this->uploadConfig['allowed_types']);
        $this->output
            ->set_content_type("image/jpeg")
            ->set_output($imgBytes);
    }

     public function preview(){
        $id = $this->uri->segment(4);
        $imgBytes = read_file($this->uploadConfig['upload_path'] . $id . ".prv." . $this->uploadConfig['allowed_types']);
        $this->output
            ->set_content_type("image/jpeg")
            ->set_output($imgBytes);
    }

    public function delete()
    {
        $id = $this->uri->segment(4);
        $this->obras_model->delete_obra($id);
        @unlink($this->uploadConfig['upload_path'] . $id . "." . $this->uploadConfig['allowed_types']);
        @unlink($this->uploadConfig['upload_path'] . $id . OBRA_IMAGE_GALLERY_MARKER . "." . $this->uploadConfig['allowed_types']);
        @unlink($this->uploadConfig['upload_path'] . $id . OBRA_IMAGE_PREVIEW_MARKER . "." . $this->uploadConfig['allowed_types']);
        @unlink($this->uploadConfig['upload_path'] . $id . OBRA_IMAGE_THUMB_MARKER . "." . $this->uploadConfig['allowed_types']);
        redirect('admin/obras');
    }

    // Ordenacion de destacadas
    public function destacadas()
    {
        // See more on: http://jqueryui.com/sortable/
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form validation
            $this->form_validation->set_rules('ids_ordenados', 'Ordenacion', 'required');
            $this->form_validation->set_message('required', 'No se ha recibido una ordenación correcta.');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $jsonIds = $this->input->post('ids_ordenados');
                $ids = json_decode($jsonIds);
                $booleanResult = true;
                $indice = 1;
                foreach ($ids as $id) {
                    $data_to_store = array(
                        'orden' => $indice
                    );
                    $booleanResult = $booleanResult && $this->obras_model->update_obra($id, $data_to_store);
                    $indice++;
                }
                
                if($booleanResult){
                    $data['flash_message'] = TRUE; 
                }else{
                    $data['flash_message'] = FALSE; 
                }
            }

        }
        
        //load the view
        $data['previewWidth'] = OBRA_PREVIEW_WIDTH;
        $data['previewHeight'] = OBRA_PREVIEW_HEIGHT;
        $data['destacadas'] = $this->obras_model->obtener_destacadas_ordenadas();
        $data['main_content'] = 'admin/obras/ordenacion_destacadas';
        $this->load->view('includes/template', $data);  
    }

}