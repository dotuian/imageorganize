<?php

class ImageOrganize {

    public $input;
    public $output;
    public $summary = array();

    public function __construct($input, $output) {
        $this->input = $input;
        $this->output = $output;
    }

    public function find_all_files($dir) {
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir/$value")) {
                $result[] = "$dir/$value";
                continue;
            }
            foreach ($this->find_all_files("$dir/$value") as $value) {
                $result[] = $value;
            }
        }
        return $result;
    }

    private function organize_file($files, $output, &$summary) {

        foreach ($files as $key => $file) {
            if (file_exists($file)) {

                if (exif_imagetype($file) === IMAGETYPE_JPEG || true) { // 任意文件

                    echo "=======================" . PHP_EOL;
                    echo "file : $file" . PHP_EOL;

                    $exif = @exif_read_data($file);

                    if (!isset($exif['DateTime'])) {
                        echo "[NG] | this images has not createtime information. " . PHP_EOL;

                        $stat = stat($file);
                        //echo date('Y.m.d', $stat['atime']) . PHP_EOL; // 访问日�?
                        //echo date('Y.m.d', $stat['mtime']) . PHP_EOL;  // 更新日�?
                        //echo date('Y.m.d', $stat['ctime']) . PHP_EOL;  // 创建日�?

                        $subfolder = date('Y.m.d', $stat['mtime']);
                        
                    } else {
	                    // folder format : YYYY.MM.DD
	                    $subfolder = substr($exif['DateTime'], 0, 10);
	                    $subfolder = str_replace(":", ".", $subfolder);
                    }

                    // if folder not exsit ,create folder 
                    if (!file_exists($output . $subfolder)) {
                        mkdir($output . $subfolder);
                    }

                    // copy files
                    $desc = $output . $subfolder . "/" . basename($file);
                    if (copy($file, $desc)) {
                        echo "[OK] | $file => $desc" . PHP_EOL;
                    } else {
                        echo "[NG] | $file => $desc" . PHP_EOL;
                    }

                    // summary
                    $summary[$subfolder][] = $desc;

                    echo PHP_EOL;
                }
            }
        }
    }

    private function show_summary($summary) {
        foreach ($summary as $key => $value) {
            echo "$key has " . count($value) . " pictures" . PHP_EOL;
        }
    }

    public function run() {
        $files = $this->find_all_files($this->input);
        $this->organize_file($files, $this->output, $this->summary);
        $this->show_summary($this->summary);
    }

}

$input = "D:/001/";
$output = "D:/002/";

$obj = new ImageOrganize($input, $output);
$obj->run();

