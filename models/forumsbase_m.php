<?php defined('BASEPATH') or exit('No direct script access allowed');

class ForumsBase_m extends MY_Model {

	protected $_stream = ''; // yeah well, this needs to be set by our children
	protected $_stream_prefix = 'forums_';
	protected $_stream_namespace = 'forums';

	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->_stream_prefix . $this->_table;
	}

	public function stream_slug()
	{
		return $this->_stream;
	}

	public function namespace_slug()
	{
		return $this->_stream_namespace;
	}

 	public function get_entries($stream_params = array())
 	{
		// unset accidentially set stream and namespace params
		unset($stream_params['stream']);
		unset($stream_params['namespace']);

		$params = array(
		   'stream' => $this->_stream,
		   'namespace' => $this->_stream_namespace
		);

		$params = array_merge($params, $stream_params);

		return $this->streams->entries->get_entries($params);
 	}

 	public function get_entry($id, $format = true)
 	{
 		if( $this->_stream == '')
 		{
			throw new Exception("model ' . get_class($this) . ' doesn't know its stream name :(");
 		}

		return $this->streams->entries->get_entry($id, $this->_stream, $this->_stream_namespace, $format);
 	}

	protected function build_id_array($data)
	{	
		$indexed_data = array();

		if( ! empty($data) )
		{
			foreach($data as $entry)
			{
				if( is_array($entry) )
				{
					$indexed_data[$entry['id']] = $entry;	
				}
				else if(is_object($entry))
				{
					$indexed_data[$entry->id] = $entry;	
				}
			}
		}
		else
		{
			return $data;
		}

		return $indexed_data;
	}

	public function sort_entries(&$data, $sort_by = false)
	{
		if( ! $sort_by )
		{
			$sort_by = 'sort';
		}

		usort($data['entries'], function($a, $b) use($sort_by) {
		    return strnatcasecmp($a[$sort_by], $b[$sort_by]);
		});
	}

	/*
	public function multisort_entries(&$data, $sorting)
	{
		$fields = array();

		foreach($sorting as $field => $order)
		{
			$fields[$field] = array();
		}

		foreach($data['entries'] as $idx => $row)
		{
			foreach($fields as $field => $field_data)
			{
				$fields[$field][$idx] = $row[$field];
			}
		}

		$sort_array = array();

		foreach($fields as $field => $field_data)
		{
			$sort_array = array_merge($sort_array, )
		}

		array_multisort($start_times, SORT_ASC, $sort, SORT_ASC, $titles, SORT_ASC, $sessions['entries']);

		unset($start_times);
		unset($sort);
		unset($titles);
	}
	*/
}