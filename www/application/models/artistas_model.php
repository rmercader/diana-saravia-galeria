<?php
class Artistas_model extends CI_Model {
 
    /**
    * Responsable for auto load the database
    * @return void
    */
    public function __construct()
    {
        $this->load->database();
    }

    /**
    * Get product by his is
    * @param int $product_id 
    * @return array
    */
    public function get_artista_by_id($id)
    {
		$this->db->select('*');
		$this->db->from('artista');
		$this->db->where('id_artista', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }    

    /**
    * Fetch artista data from the database
    * possibility to mix search, filter and order
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_artistas($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
		$this->db->select('*');
		$this->db->from('artista');

		if($search_string){
			$this->db->like('nombre_artista', $search_string);
		}
		$this->db->group_by('id_artista');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('id_artista', $order_type);
		}

        if($limit_start && $limit_end){
          $this->db->limit($limit_start, $limit_end);	
        }

        if($limit_start != null){
          $this->db->limit($limit_start, $limit_end);    
        }
        
		$query = $this->db->get();
		
		return $query->result_array(); 	
    }

    // Solo devuelve el nombre y el id de los artistas
    public function get_lista_artistas($order=null, $order_type='Asc'){
        
        $this->db->select('id_artista, nombre_artista');
        $this->db->from('artista');
        $this->db->group_by('id_artista');

        if($order){
            $this->db->order_by($order, $order_type);
        }else{
            $this->db->order_by('id_artista', $order_type);
        }
        
        $query = $this->db->get();
        
        return $query->result_array();  
    }

    /**
    * Count the number of rows
    * @param int $search_string
    * @param int $order
    * @return int
    */
    function count_artistas($search_string=null, $order=null)
    {
		$this->db->select('*');
		$this->db->from('artista');
		if($search_string){
			$this->db->like('nombre_artista', $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('id_artista', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function store_artista($data)
    {
		$insert = $this->db->insert('artista', $data);
	    return $insert;
	}

    /**
    * Update artista
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_artista($id, $data)
    {
		$this->db->where('id_artista', $id);
		$this->db->update('artista', $data);
		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if($report !== 0){
			return true;
		}else{
			return false;
		}
	}

    /**
    * Delete artistar
    * @param int $id - artista id
    * @return boolean
    */
	function delete_artista($id){
		$this->db->where('id_artista', $id);
		$this->db->delete('artista'); 
	}

    function get_nombre($id){
        $this->db->select('nombre_artista');
        $this->db->from('artista');
        $this->db->where('id_artista', $id);
        $query = $this->db->get();
        $res = $query->result_array();
        return $res[0]['nombre_artista'];
    }

}
?>