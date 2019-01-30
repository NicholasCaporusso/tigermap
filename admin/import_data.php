<?php
$data=file_get_contents('json_fhsuDataTypes_shapesOnly.json', FILE_USE_INCLUDE_PATH);

$data=json_decode($data,true);
echo '<pre>';
/*print_r($data);
die();*/
$shape;
$coords_data=['LLcenter','radius','LLoutline','LLentrances'];

require_once('_helpers.php');
require_once('_helpers_app.php');
require_once('settings.php');
require_once('lib/mysqlclass.php');

$types=['marker'=>Shapes::MARKER,'poi'=>Shapes::POI,'circle'=>Shapes::CIRCLE,'polyline'=>Shapes::POLYLINE,'polygon'=>Shapes::POLYGON,'parking'=>Shapes::PARKING,'building'=>Shapes::BUILDING];
foreach($data['shapes'] as $k=>$record){
	$diff_array=['type'=>'','name'=>'','description'=>'','style'=>''];
	$shape['type']=$types[$record['type']];
	$shape['name']=isset($record['name']) ? $record['name'] : '';
	$shape['description']=isset($record['description']) ? $record['description'] : '';
	$shape['details']=[];
	if(isset($record['style'])) $shape['details']['style']=$record['style'];
	$shape['details']['coords']=[];
	foreach($coords_data as $k){
		if(isset($record[$k])) $shape['details']['coords'][$k]=$record[$k];
		$diff_array[$k]='';
	}
	$record=array_diff_key($record,$diff_array);
	$shape['details']=json_encode(array_merge($shape['details'],$record));
	
	print_r($shape);
	$shape_ID=DB::get()->insert('INSERT INTO shapes('.implode(array_keys($shape),',').') VALUES('.implode(array_fill(0,count($shape),'?'),',').')',array_values($shape));
	DB::get()->insert('INSERT INTO apps_shapes(app_ID,shape_ID) VALUES(1,?)',[$shape_ID]);
	echo '<hr />';
}


