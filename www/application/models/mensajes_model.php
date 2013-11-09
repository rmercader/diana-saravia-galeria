<?php
class Mensajes_model extends CI_Model {
 
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
    * @param int $id_mensaje
    * @return array
    */
    public function get_mensaje_by_id($id)
    {
		$this->db->select('*');
		$this->db->from('mensaje');
		$this->db->where('id_mensaje', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }    

    /**
    * Fetch mensaje data from the database
    * possibility to mix search, filter and order
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_mensajes($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
		$this->db->select('*');
		$this->db->from('mensaje');

		if($search_string){
			$this->db->like('nombre', $search_string);
		}
		$this->db->group_by('id_mensaje');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('id_mensaje', $order_type);
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

    /**
    * Count the number of rows
    * @param int $search_string
    * @param int $order
    * @return int
    */
    function count_mensajes($search_string=null, $order=null)
    {
		$this->db->select('*');
		$this->db->from('mensaje');
		if($search_string){
			$this->db->like('nombre', $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('id_mensaje', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function store_mensaje($data)
    {
		$insert = $this->db->insert('mensaje', $data);
	    return $insert;
	}

    /**
    * Update mensaje
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_mensaje($id, $data)
    {
		$this->db->where('id_mensaje', $id);
		$this->db->update('mensaje', $data);
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
    * Delete mensajer
    * @param int $id - mensaje id
    * @return boolean
    */
	function delete_mensaje($id){
		$this->db->where('id_mensaje', $id);
		$this->db->delete('mensaje'); 
	}
    
}
?>