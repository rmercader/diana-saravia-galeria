<?php
class Admin_eventos extends CI_Controller {

    private $uploadConfig;
    private $imgLibConfig;

    /**
    * name of the folder responsible for the views 
    * which are manipulated by this controller
    * @constant string
    */
    const VIEW_FOLDER = 'admin/eventos';
 
    /**
    * Responsable for auto load the mode
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('eventos_model');

        // Helpers
        $this->load->helper('file');
        $this->load->helper('url');

        // Manejo de uploads
        $this->uploadConfig = array();
        $this->uploadConfig['upload_path'] = './uploads/eventos/';
        $this->uploadConfig['allowed_types'] = 'jpg|JPG';
        $this->uploadConfig['overwrite'] = true;
        $this->load->library('upload');

        // Manejo de imagenes
        $this->imgLibConfig = array();
        $this->imgLibConfig['image_library'] = 'gd2';
        $this->imgLibConfig['create_thumb'] = TRUE;
        $this->imgLibConfig['maintain_ratio'] = FALSE;
        $this->imgLibConfig['quality'] = EVENTO_IMAGE_QUALITY;
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

        $data['thumbnailWidth'] = EVENTO_THUMB_WIDTH;
        $data['thumbnailHeight'] = EVENTO_THUMB_HEIGHT;
        $data['previewWidth'] = EVENTO_PREVIEW_WIDTH;
        $data['previewHeight'] = EVENTO_PREVIEW_HEIGHT;

        $config['base_url'] = base_url().'admin/eventos';
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
            $data['count_eventos']= $this->eventos_model->count_eventos($search_string, $order);
            $config['total_rows'] = $data['count_eventos'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['eventos'] = $this->eventos_model->get_eventos($search_string, $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['eventos'] = $this->eventos_model->get_eventos($search_string, '', $order_type, $config['per_page'],$limit_end);           
                }
            }else{
                if($order){
                    $data['eventos'] = $this->eventos_model->get_eventos('', $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['eventos'] = $this->eventos_model->get_eventos('', '', $order_type, $config['per_page'],$limit_end);        
                }
            }

        }else{

            //clean filter data inside section
            $filter_session_data['evento_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_eventos']= $this->eventos_model->count_eventos();
            $data['eventos'] = $this->eventos_model->get_eventos('', '', $order_type, $config['per_page'],$limit_end);        
            $config['total_rows'] = $data['count_eventos'];

        }//!isset($search_string) && !isset($order)
         
        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/eventos/list';
        $this->load->view('includes/template', $data);  

    }//index

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('nombre_evento', 'Nombre', 'required');
            $this->form_validation->set_rules('fecha', 'Fecha', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');  

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $nombre_evento = $this->input->post('nombre_evento');
                $data_to_store = array(
                    'nombre_evento' => $nombre_evento,
                    'fecha' => $this->input->post('fecha'),
                    'detalles' => $this->input->post('detalles'),
                );

                $idEvento = $this->eventos_model->store_evento($data_to_store);
                if(is_numeric($idEvento)){
                    $data_to_store = array('ficha' => url_title("$idEvento $nombre_evento", '-', true));                    
                    $data['flash_message'] = $this->eventos_model->update_evento($idEvento, $data_to_store); 
                    $this->uploadConfig['file_name'] = $idEvento;
                    $this->upload->initialize($this->uploadConfig);
                    if($this->upload->do_upload('imagen_evento')){
                        $upload_data = $this->upload->data();

                        // Salvo imagen preview
                        $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['thumb_marker'] = EVENTO_IMAGE_PREVIEW_MARKER;
                        $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['width'] = EVENTO_PREVIEW_WIDTH;
                        $this->imgLibConfig['height'] = EVENTO_PREVIEW_HEIGHT;
                        $this->image_lib->initialize($this->imgLibConfig); 
                        if(!$this->image_lib->resize())
                        {
                            $data['error'] = $this->image_lib->display_errors() . "<br>";
                        } else {
                            // Salvo imagen thumbnail
                            $this->imgLibConfig['thumb_marker'] = EVENTO_IMAGE_THUMB_MARKER;
                            $this->imgLibConfig['width'] = EVENTO_THUMB_WIDTH;
                            $this->imgLibConfig['height'] = EVENTO_THUMB_HEIGHT;
                            $this->image_lib->clear();
                            $this->image_lib->initialize($this->imgLibConfig); 
                            if(! $this->image_lib->resize())
                            {
                                $data['error'] .= $this->image_lib->display_errors() . "<br>";
                            }
                        }

                    } else {
                        $upload_data = $this->upload->data();
                        if(is_array($upload_data) && !empty($upload_data['file_name'])){
                            $data['error'] = $this->upload->display_errors() . "<br>";    
                        }
                    } 
                }else{
                    $data['flash_message'] = FALSE; 
                }

            }

        }
        //load the view
        $data['main_content'] = 'admin/eventos/add';
        $this->load->view('includes/template', $data);  
    }       

    /**
    * Update item by his id
    * @return void
    */
    public function update()
    {
        //evento id 
        $id = $this->uri->segment(4);

        $data['previewWidth'] = EVENTO_PREVIEW_WIDTH;
        $data['previewHeight'] = EVENTO_PREVIEW_HEIGHT;
  
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form validation
            $this->form_validation->set_rules('nombre_evento', 'Nombre', 'required');
            $this->form_validation->set_rules('fecha', 'Fecha', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $nombre_evento = $this->input->post('nombre_evento');
                $data_to_store = array(
                    'nombre_evento' => $nombre_evento,
                    'fecha' => $this->input->post('fecha'),
                    'detalles' => $this->input->post('detalles'),
                );
                //if the insert has returned true then we show the flash message
                if($this->eventos_model->update_evento($id, $data_to_store) == TRUE){
                    $data_to_store = array('ficha' => url_title("$id $nombre_evento", '-', true));                    
                    $data['flash_message'] = $this->eventos_model->update_evento($id, $data_to_store); 
                    $this->uploadConfig['file_name'] = $id;
                    $this->uploadConfig['overwrite'] = true;
                    $this->upload->initialize($this->uploadConfig);
                    if($this->upload->do_upload('imagen_evento')){
                        $upload_data = $this->upload->data();

                        // Salvo imagen preview
                        $this->imgLibConfig['source_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['thumb_marker'] = EVENTO_IMAGE_PREVIEW_MARKER;
                        $this->imgLibConfig['new_image'] = $upload_data['full_path'];
                        $this->imgLibConfig['width'] = EVENTO_PREVIEW_WIDTH;
                        $this->imgLibConfig['height'] = EVENTO_PREVIEW_HEIGHT;
                        $this->image_lib->initialize($this->imgLibConfig); 
                        if(!$this->image_lib->resize())
                        {
                            $data['error'] = $this->image_lib->display_errors() . "<br>";
                        } else {
                            // Salvo imagen thumbnail
                            $this->imgLibConfig['thumb_marker'] = EVENTO_IMAGE_THUMB_MARKER;
                            $this->imgLibConfig['width'] = EVENTO_THUMB_WIDTH;
                            $this->imgLibConfig['height'] = EVENTO_THUMB_HEIGHT;
                            $this->image_lib->clear();
                            $this->image_lib->initialize($this->imgLibConfig); 
                            if(! $this->image_lib->resize())
                            {
                                $data['error'] .= $this->image_lib->display_errors() . "<br>";
                            }
                        }

                    } else {
                        $upload_data = $this->upload->data();
                        if(is_array($upload_data) && !empty($upload_data['file_name'])){
                            $data['error'] = $this->upload->display_errors() . "<br>";    
                        }
                    } 
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/eventos/update/'.$id.'');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //evento data 
        $datosEvento = $this->eventos_model->get_evento_by_id($id);
        if(count($datosEvento) > 0){
            $data['evento'] = $datosEvento;
        }
        
        //load the view
        $data['main_content'] = 'admin/eventos/edit';
        $this->load->view('includes/template', $data);            

    }//update

    public function thumbnail(){
        $id = $this->uri->segment(4);
        $imgBytes = read_file($this->uploadConfig['upload_path'] . $id . ".thu.jpg");
        $this->output
            ->set_content_type("image/jpeg")
            ->set_output($imgBytes);
    }

     public function preview(){
        $id = $this->uri->segment(4);
        $imgBytes = read_file($this->uploadConfig['upload_path'] . $id . ".prv.jpg");
        $this->output
            ->set_content_type("image/jpeg")
            ->set_output($imgBytes);
    }

    public function delete()
    {
        $id = $this->uri->segment(4);
        $this->eventos_model->delete_evento($id);
        @unlink($this->uploadConfig['upload_path'] . $id . ".jpg");
        @unlink($this->uploadConfig['upload_path'] . $id . ".prv.jpg");
        @unlink($this->uploadConfig['upload_path'] . $id . ".thu.jpg");
        redirect('admin/eventos');
    }

}