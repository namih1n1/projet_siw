<?php
function mix_tb_keyed($tb) {
	$keys = array_keys($tb);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_tb[$key] = $tb[$key];
	}
	$tb = $rnd_tb;
	unset($rnd_tb);
}

?>