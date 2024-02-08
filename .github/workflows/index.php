<?php
	ini_set("display_errors", "1");
	error_reporting(E_ALL);
	
	// $filename = 'vjgimages.s3.amazonaws.com/10-2131Y-1.jpg';
	// $filename = 's7d3.scene7.com/is/image/AshleyFurniture/EB3392-131-CLSD-ANGLE-SW-P1-KO';
	// $status = checkFileExists($filename);
	// var_dump ($status); die;
	
	function checkFileExists($filename)
	{
		$filename= "https://".$filename;
		$headers = get_headers($filename);
		// print_r($headers[0]); echo "<br />";
		if($headers && strpos( $headers[0], '200')) {
			return "success";
		}
		else {
			return "fail";
		}
	}
	
	// echo "fifth";die;
	$dataPrice= csv_to_array('fifth.csv', ',');
	// $dataPrice= csv_to_array('single.csv', ',');
	
	$data = imageNotFoundData($dataPrice['content']);
	
	create_csv_path($data);
	
	function imageNotFoundData($content)
	{
		$file_not_found = array();
		foreach ($content as $item)
		{
			if(!empty($item['swatch']))	{
				$status = checkFileExists($item['swatch']);
			
				if($status=='fail' && !array_key_exists($item['swatch'],$file_not_found))	{
					$file_not_found[] = array($item['swatch']);
				}
			}
			
			
			$gallery_images = explode(",",$item['gallery_images']);
			
			foreach($gallery_images as $img) 
			{
				if(!empty($img))	{
					$status = checkFileExists($img);
				
					if($status=='fail' && !array_key_exists($img,$file_not_found))	{
						$file_not_found[] =array($img);
					}
				}
			}
		}
		return $file_not_found;
	}
	
	function create_csv_path($content_rows)
	{
		$fichier = 'Ashley_path_only.csv';
		header( "Content-Type: text/csv;charset=utf-8" );
		header( "Content-Disposition: attachment;filename=\"$fichier\"" );
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$fp= fopen('php://output', 'w');
		fputcsv($fp, array('path'));
		
		foreach ($content_rows as $fields)
		{
			$fields = @$fields;
			@fputcsv($fp, $fields);
		}
		
		fclose($fp);
		exit();
	}


	function csv_to_array($filename='', $delimiter=',')
	{
		if(!file_exists($filename) || !is_readable($filename))
        return FALSE;
		
		$header = NULL;
		$data = array();
		$data_with_header = array();
		
		if (($handle = fopen($filename, 'r')) !== FALSE)
		{
			while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE)
			{
				if(!$header){
					$header = $row;
				}
				else {		
					if(count($header) == count($row)){
						$data[] = array_combine($header,$row);
					}
				}
			}
			fclose($handle);
		}
		$data_with_header = array("header"=>$header, "content"=>$data);
		return $data_with_header;
	}
	
?>