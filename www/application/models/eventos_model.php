<?php
class Eventos_model extends CI_Model {
 
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
    public function get_evento_by_id($id)
    {
		$this->db->select('*');
		$this->db->from('evento');
		$this->db->where('id_evento', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }    

    /**
    * Fetch evento data from the database
    * possibility to mix search, filter and order
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_eventos($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
		$this->db->select('*');
		$this->db->from('evento');

		if($search_string){
			$this->db->like('nombre_evento', $search_string);
		}
		$this->db->group_by('id_evento');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('id_evento', $order_type);
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
    function count_eventos($search_string=null, $order=null)
    {
		$this->db->select('*');
		$this->db->from('evento');
		if($search_string){
			$this->db->like('nombre_evento', $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('id_evento', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function store_evento($data)
    {
		$insert = $this->db->insert('evento', $data);
	    return $this->db->insert_id();
	}

    /**
    * Update evento
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_evento($id, $data)
    {
		$this->db->where('id_evento', $id);
		$this->db->update('evento', $data);
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
    * Delete eventor
    * @param int $id - evento id
    * @return boolean
    */
	function delete_evento($id){
		$this->db->where('id_evento', $id);
		$this->db->delete('evento'); 
	}

    function get_campo($id, $campo){
        $this->db->select($campo);
        $this->db->from('evento');
        $this->db->where('id_evento', $id);
        $query = $this->db->get();
        $res = $query->result_array();
        return $res[0][$campo];
    }

}
?>