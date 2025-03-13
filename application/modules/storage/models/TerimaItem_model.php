<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class TerimaItem_model extends Conf{
	protected $table = 'terima_item';
	public $timestamps = false;

	public function item()
	{
		return $this->hasOne('\Model\Storage\Item_model', 'kode', 'item_kode')->with(['satuan']);
	}
}
