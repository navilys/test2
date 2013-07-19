<?
G::LoadClass ( 'case' );
G::LoadClass ( 'pmFunctions' );

$form=$_POST['form'];

if(isset($form))
{	
	foreach($form['GRID_SELECTOR_NEW_OPTIONS'] as $grid){
		
		$update="UPDATE PMT_FIELDS_INBOX  SET 
		DESCRIPTION  = '".trim($grid['DESCRIPTION'])."',
		INCLUDE_OPTION  = '".$grid['INCLUDE_OPTION']."',
		POSITION  = '".$grid['POSITION']."'
    	WHERE ID = '".$grid['ID']."'
		";
    	$update2=executeQuery($update);	
    	$type = $grid['INCLUDE_OPTION'];
    
	}	
}

?>
<script type="text/javascript">
	parent.location.href = 'adminAutorisations.php';
</script>