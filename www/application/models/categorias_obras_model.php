<?php
class Categorias_obras_model extends CI_Model {
 
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
    public function get_categoria_obra_by_id($id)
    {
		$this->db->select('*');
		$this->db->from('categoria_obra');
		$this->db->where('id_categoria_obra', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }    

    /**
    * Fetch categoria_obra data from the database
    * possibility to mix search, filter and order
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_categorias_obras($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
		$this->db->select('*');
		$this->db->from('categoria_obra');

		if($search_string){
			$this->db->like('nombre_categoria_obra', $search_string);
		}
		$this->db->group_by('id_categoria_obra');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('id_categoria_obra', $order_type);
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

    public function get_categorias_con_obras($search_string=null, $order=null, $order_type='Asc'){
        $this->db->select('c.*');
        $this->db->from('categoria_obra c');
        $this->db->where("EXISTS(SELECT o.id_obra FROM obra o WHERE o.id_categoria_obra = c.id_categoria_obra)");

        if($search_string){
            $this->db->like('nombre_categoria_obra', $search_string);
        }
        $this->db->group_by('id_categoria_obra');

        if($order){
            $this->db->order_by($order, $order_type);
        }else{
            $this->db->order_by('id_categoria_obra', $order_type);
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
    function count_categorias_obras($search_string=null, $order=null)
    {
		$this->db->select('*');
		$this->db->from('categoria_obra');
		if($search_string){
			$this->db->like('nombre_categoria_obra', $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('id_categoria_obra', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function store_categoria_obra($data)
    {
		$insert = $this->db->insert('categoria_obra', $data);
	    return $insert;
	}

    /**
    * Update categoria_obra
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_categoria_obra($id, $data)
    {
		$this->db->where('id_categoria_obra', $id);
		$this->db->update('categoria_obra', $data);
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
    * Delete categoria_obrar
    * @param int $id - categoria_obra id
    * @return boolean
    */
	function delete_categoria_obra($id){
		$this->db->where('id_categoria_obra', $id);
		$this->db->delete('categoria_obra'); 
	}

}
?>