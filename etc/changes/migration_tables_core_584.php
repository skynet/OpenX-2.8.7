<?php

require_once(MAX_PATH.'/lib/OA/Upgrade/Migration.php');

class Migration_584 extends Migration
{

    function Migration_584()
    {
        //$this->__construct();

		$this->aTaskList_constructive[] = 'beforeAddField__zones__oac_category_id';
		$this->aTaskList_constructive[] = 'afterAddField__zones__oac_category_id';


		$this->aObjectMap['zones']['oac_category_id'] = array('fromTable'=>'zones', 'fromField'=>'oac_category_id');
    }



	function beforeAddField__zones__oac_category_id()
	{
		return $this->beforeAddField('zones', 'oac_category_id');
	}

	function afterAddField__zones__oac_category_id()
	{
		return $this->afterAddField('zones', 'oac_category_id');
	}

}

?>