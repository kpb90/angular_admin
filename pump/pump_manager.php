<?php
require_once "../dbaccessor.php";

	
class PumpManager extends DBAccessor {
	
	function __construct() {	
		parent::__construct();				
	}
		
	function getPump() {				 
		 $dir = $this->getString('dir');
		 if(!$dir) {
		 	$_REQUEST['dir'] = 'DESC';
		 }
		 $condition = '';
		 if ($_REQUEST['form']) {
			$form = json_decode($_REQUEST['form']);
	 	 	if ($form->title){
	 	 		$condition = " where gtitle like '%{$form->title}%'";
	 	 	}
		 }

		 $query = "SELECT SQL_CALC_FOUND_ROWS id, title_pump, seria_pump, model_pump, work_run_dry, solids_concentration, size_of_solid, productivity, 
		 				  delivery_height, viscosity, density, engine, housing_material, fluid_temperature, 
		 				  ambient_temperature, shaft, o_ring, support_sleeve, self_priming  FROM pump {$condition}";
		 $this->executeListQuery($query);
	}

	function saveNewCharactPump () {
		$pump_id = $this->getInt ('form_model_id');
		$new_characteristic_opt = $this->getString ('new_characteristic_opt');
		$new_characteristic_val = $this->getString ('new_characteristic_val');
		$query = "INSERT INTO `add_characteristics`(`opt`, `val`, `pump_id`) VALUES ('$new_characteristic_opt','$new_characteristic_val','$pump_id')";
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			if ($id){
				echo '1';
			}
			else {
				echo mysql_insert_id();
			}
		} else {
			echo '0';
		}
	}

	function savePump() {
		$id = $this->getInt('id');
		$title_pump = $this->getString ('title_pump');
		$seria_pump = $this->getString ('seria_pump');
		$model_pump = $this->getString ('model_pump');
		$work_run_dry = $this->getString ('work_run_dry');
		$solids_concentration = $this->getString ('solids_concentration');
		$size_of_solid = $this->getString ('size_of_solid');
		$productivity = $this->getString ('productivity');
		$delivery_height = $this->getString ('delivery_height');
		$viscosity = $this->getString ('viscosity');
		$density = $this->getString ('density');
		$engine = $this->getString ('engine');
		$housing_material = $this->getString ('housing_material');
		$fluid_temperature = $this->getString ('fluid_temperature');
		$ambient_temperature = $this->getString ('ambient_temperature');
		$shaft = $this->getString ('shaft');
		$o_ring = $this->getString ('o_ring');
		$support_sleeve = $this->getString ('support_sleeve');
		$self_priming = $this->getString ('self_priming');
		$dynamic_fields = $this->getArr ('dynamic_fields');

		$query = '';
		if($id) {
			$query = "UPDATE pump SET title_pump = '$title_pump', seria_pump = '$seria_pump', model_pump = '$model_pump', work_run_dry = '$work_run_dry', 
									  solids_concentration = '$solids_concentration', 
									  size_of_solid = '$size_of_solid', productivity = '$productivity', delivery_height = '$delivery_height', 
									  viscosity = '$viscosity', density = '$density', engine = '$engine', 
									  housing_material = '$housing_material', fluid_temperature = '$fluid_temperature', ambient_temperature = '$ambient_temperature', 
									  shaft = '$shaft', o_ring = '$o_ring', support_sleeve = '$support_sleeve', self_priming = '$self_priming' WHERE id=$id";
		} else {
			$query = "INSERT INTO `pump`(`title_pump`, `seria_pump`, `model_pump`, `self_priming`, `work_run_dry`, `solids_concentration`, `size_of_solid`, `productivity`, `delivery_height`, `viscosity`, `density`, `engine`, `housing_material`, `fluid_temperature`, `ambient_temperature`, `shaft`, `o_ring`, `support_sleeve`) 
					       VALUES 
					       				('$title_pump','$seria_pump', '$model_pump', '$self_priming', '$work_run_dry', '$solids_concentration', '$size_of_solid', '$productivity', '$delivery_height', '$viscosity', '$density', '$engine', '$housing_material', '$fluid_temperature', '$ambient_temperature', '$shaft', '$o_ring', '$support_sleeve')";
		}

		$result = $this->execute($query);
		if($result['result'] == 'success') {
			$new_id_pump = $id ? $id : mysql_insert_id();
			if ($dynamic_fields&&count($dynamic_fields)>0) {
				$query = '';
				foreach ($dynamic_fields as $dynamic_fields_v) {
					$query .= "({$dynamic_fields_v[id]}, '$dynamic_fields_v[opt]','$dynamic_fields_v[val]', {$new_id_pump}),";
				}
				$query = substr($query, 0, strlen ($query)- 1). " ON DUPLICATE KEY UPDATE opt=VALUES(opt),val=VALUES(val),pump_id=VALUES(pump_id);";
				$query = "INSERT INTO `add_characteristics`
							(`id`,`opt`,`val`,`pump_id`)
							VALUES
							{$query}";
				$result = $this->execute($query);
				if($result['result'] != 'success') {
					echo '0';
					exit;
				}
			}
			echo $id ? 1 : $new_id_pump;
		} else {
			echo '0';
		}
	}
	
	function editPump() {
		$id = $this->getInt('id');
		$query = "SELECT * FROM pump WHERE id = $id";
		$result = $this->getAssoc($query);
		$query = "SELECT * FROM add_characteristics WHERE pump_id = $id";
		$dynamic_fields = $this->getAssocList($query);
		echo '{"dynamic_fields":' . json_encode($dynamic_fields) . ',"results":'. json_encode($result).'}';
	}
	
	function deletePump() {
		if ($dynamic_id = $this->getString('dynamic_id')) {
			$query = "DELETE FROM add_characteristics WHERE id = $dynamic_id";
		}
		else {
			$ids = $this->getString('ids');
			$query = "DELETE FROM pump WHERE id IN ($ids)";
		}
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}
	}
}
?>
