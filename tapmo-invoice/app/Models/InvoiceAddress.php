<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class InvoiceAddress extends Model {
	
	protected $db;
	public function __construct(ConnectionInterface &$db) {
		$this->db =& $db;
		$this->invoice_address = $this->db->table("invoice_address");
	}


	function get_by_order_id($order_id){
		// $types = ['public_key', 'secret_key'];
		return $this->invoice_address
		            ->select("*")
		            ->where("order_id",$order_id)
					->get()
					->getRow();
	}



	

	function add($data = NULL) {
		return $this->invoice_address 
                        ->insert($data);
	}

	function update_data($id = NULL,$data = NULL){
		return $this->invoice_address 
						->where("id",$id)
						->set($data)
						->update();
	}
	// function get($key,$value = NULL){
	// 	return $this->site_data 
	// 				   ->where($key,$value)
	// 				   ->get()
	// 					->getFirstRow();
	// }

	// function update_or_add($key,$values)
	// {
  //       $data =$this->get($key,$values[$key]);
	// 	if($data)
	// 	{
	// 		return $this->update_site($data->id ,$values);
	// 	}
	// 	return $this->add($values);

	// }
	
}