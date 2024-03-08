<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class SiteModel extends Model {
	
	protected $db;
	public function __construct(ConnectionInterface &$db) {
		$this->db =& $db;
		$this->site_data = $this->db->table("sites");
		$this->setting   = $this->db->table("setting");
	}


	function get_app_key($keyType){
		// $types = ['public_key', 'secret_key'];
		return $this->setting
		            ->select("value")
		            ->where("key",$keyType)
					->get()
					->getRow()->value;
	}



	

	function add($data = NULL) {
		return $this->site_data 
                        ->insert($data);
	}

	function update_site($id = NULL,$data = NULL){
		return $this->site_data 
						->where("id",$id)
						->set($data)
						->update();
	}
	function get($key,$value = NULL){
		return $this->site_data 
					   ->where($key,$value)
					   ->get()
						->getFirstRow();
	}

	function update_or_add($key,$values)
	{
        $data =$this->get($key,$values[$key]);
		if($data)
		{
			return $this->update_site($data->id ,$values);
		}
		return $this->add($values);

	}
	
}