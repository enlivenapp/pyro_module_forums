<?php defined('BASEPATH') or exit('No direct script access allowed');

class Forums_m extends ForumsBase_m {

	protected $_table = 'forums';
	protected $_stream = 'forums';

	public function get_by_category($category_id)
	{
		return $this->get_entries(
			array(
				'where' => $this->db->protect_identifiers('category_id') . ' = ' . $this->db->escape($category_id)
			)
		);
	}

	public function delete_forums_by_category($id)
	{
		
	}

}