<?php
class Obras_model extends CI_Model {
 
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
    public function get_obra_by_id($id)
    {
		$this->db->select("obra.id_obra, obra.nombre_obra, artista.id_artista, artista.nombre_artista, categoria_obra.id_categoria_obra, categoria_obra.nombre_categoria_obra, obra.destacada, obra.orden");
        $this->db->from('obra');
        $this->db->join('artista', 'artista.id_artista = obra.id_artista', 'inner');
        $this->db->join('categoria_obra', 'categoria_obra.id_categoria_obra = obra.id_categoria_obra', 'inner');
		$this->db->where('id_obra', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }    

    /**
    * Fetch obra data from the database
    * possibility to mix search, filter and order
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_obras($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
		$this->db->select('obra.id_obra, obra.nombre_obra, artista.nombre_artista, categoria_obra.nombre_categoria_obra, destacada');
		$this->db->from('obra');
        $this->db->join('artista', 'artista.id_artista = obra.id_artista', 'inner');
        $this->db->join('categoria_obra', 'categoria_obra.id_categoria_obra = obra.id_categoria_obra', 'inner');

		if($search_string){
			$this->db->like('nombre_obra', $search_string);
            $this->db->or_like('nombre_artista', $search_string);
            $this->db->or_like('nombre_categoria_obra', $search_string);
		}
		$this->db->group_by('id_obra');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('id_obra', $order_type);
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

    public function get_obras_categoria($idCategoria, $order=null, $order_type='Asc'){
        $this->db->select('obra.id_obra, obra.nombre_obra, artista.nombre_artista, categoria_obra.nombre_categoria_obra');
        $this->db->from('obra');
        $this->db->join('artista', 'artista.id_artista = obra.id_artista', 'inner');
        $this->db->join('categoria_obra', 'categoria_obra.id_categoria_obra = obra.id_categoria_obra', 'inner');
        $this->db->where('obra.id_categoria_obra', $idCategoria);
        $this->db->group_by('id_obra');

        if($order){
            $this->db->order_by($order, $order_type);
        }else{
            $this->db->order_by('id_obra', $order_type);
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
    function count_obras($search_string=null, $order=null)
    {
		$this->db->select('obra.id_obra, obra.nombre_obra, artista.nombre_artista, categoria_obra.nombre_categoria_obra');
        $this->db->from('obra');
        $this->db->join('artista', 'artista.id_artista = obra.id_artista', 'inner');
        $this->db->join('categoria_obra', 'categoria_obra.id_categoria_obra = obra.id_categoria_obra', 'inner');
		
		if($search_string){
			$this->db->like('nombre_obra', $search_string);
            $this->db->or_like('nombre_artista', $search_string);
            $this->db->or_like('nombre_categoria_obra', $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('id_obra', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function store_obra($data)
    {
		$insert = $this->db->insert('obra', $data);
	    return $this->db->insert_id();
	}

    /**
    * Update obra
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_obra($id, $data)
    {
		$this->db->where('id_obra', $id);
		$this->db->update('obra', $data);
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
    * Delete obrar
    * @param int $id - obra id
    * @return boolean
    */
	function delete_obra($id){
		$this->db->where('id_obra', $id);
		$this->db->delete('obra'); 
	}

    function obras_por_artista($idArtista){
        $this->db->select('id_obra, nombre_obra');
        $this->db->from('obra');
        $this->db->where('id_artista', $idArtista);
        $this->db->order_by('nombre_obra', 'ASC');
        $query = $this->db->get();
        
        return $query->result_array(); 
    }

    function obtener_destacadas_ordenadas(){
        $this->db->select('obra.id_obra, obra.nombre_obra, artista.nombre_artista');
        $this->db->from('obra');
        $this->db->join('artista', 'artista.id_artista = obra.id_artista', 'inner');
        $this->db->where('destacada', 1);
        $this->db->order_by('orden', 'ASC');
        $query = $this->db->get();
        
        return $query->result_array(); 
    }

}
?>