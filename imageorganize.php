<?php
function find_all_files($dir) 
{ 
    $root = scandir($dir); 
    foreach($root as $value) 
    { 
        if($value === '.' || $value === '..') {continue;} 
        if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;} 
        foreach(find_all_files("$dir/$value") as $value) 
        { 
            $result[]=$value; 
        } 
    } 
    return $result; 
} 


#
$folder = "E:/BaiduYunDownload";
$output = "E:/img/";
//var_dump($argv);


# all files 
$files = find_all_files($folder);

foreach( $files as $key => $file )
{
	if(file_exists($file)) {
		
		if (exif_imagetype($file) === IMAGETYPE_JPEG) {

			echo "=======================" . PHP_EOL;
			echo "file : $file" . PHP_EOL;
			
			$exif = @exif_read_data($file);

			if(!isset($exif['DateTime'])) {
				echo "[NG] | this images has not createtime information. " . PHP_EOL;
				continue;
			}

			// folder format : YYYY.MM.DD
			$subfolder = substr($exif['DateTime'], 0, 10);
			$subfolder = str_replace(":", ".", $subfolder);

			// if folder not exsit ,create folder 
			if(!file_exists($output . $subfolder)) {
				mkdir($output . $subfolder);
			}

			// copy files
			$desc = $output . $subfolder . "/" . basename($file);
			if(copy($file, $desc)){
				echo "[OK] | $file => $desc" . PHP_EOL;
			} else {
				echo "[NG] | $file => $desc" . PHP_EOL;
			}

			echo PHP_EOL;
		}
	}

}
