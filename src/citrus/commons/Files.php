<?php
namespace citrus\commons;

/**
 * @author Tobias Seipke <tobias.seipke@gmail.com>
 */
class Files {

    /**
     * Parses the given $dir recursively for php files (determined by the .php ending) and returns a list of found files
     *
     * @param $dir
     * @param array $files
     * @param array $ignoreFolders
     * @param array $ignoreFiles
     * @return array
     */
    public static function extractPhpFiles($dir, &$files=array("files" => array(), "ignored" => array()), $ignoreFolders=array(), $ignoreFiles=array()) {
        if(!is_dir($dir))return $files;
        $folders = array('.', '..');
        foreach($folders as $f) {
            if (!in_array($f, $ignoreFolders)) {
                array_push($ignoreFolders, $f);
            }
        }
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if(is_dir($dir."/".$file)) {
                if(!in_array($file, $ignoreFolders))
                    self::extractPhpFiles($dir."/".$file, $files, $ignoreFolders, $ignoreFiles);
            } else {
                $extension = substr(strstr($file, '.'), 1);
                if($extension==="php"&&!in_array($file, $ignoreFiles))
                    $files["files"][] = $dir."/".$file;
                else
                    $files["ignored"][] = $dir."/".$file;
            }
        }
        closedir($handle);
        return $files;
    }

    /**
     * Returns the classname of the class within the $file. Expected filename format: [ClassName].php
     * @param $file
     * @return string
     */
    public static function extractClassName($file) {
        $start = strrpos($file, '/');
        $class = substr($file, $start+1, strpos($file, '.php', $start) - $start - 1);
        return $class;
    }
    
    public static function cleanPath($path) {
        $search = array(
            '\\'
        );
        $replace = array(
            '/'
        );
        return str_replace($search, $replace, $path);
    }

    /**
     * Returns the file ending
     *
     * @param $path
     * @return string
     */
    public static function fileEnding($path) {
        return substr($path, strrpos($path, ".")+1);
    }

    /**
     * Creates a directory recursively starting at base
     *
     * @param $dir
     * @param $base
     * @return bool
     */
	public function mkdirrec($base, $dir) {
		$explode = explode((!(strpos($dir, '/')<0) ? '/' : '\\'), $dir);
		$dir = $base;
		foreach($explode as $append) {
			$dir.="/".$append;
			if(!is_dir($dir)) {
				if(!mkdir($dir)) {
					return false;
                }
            }
		}
		return true;
	}

    /**
     * Deletes $dir and all files directories within
     * @param $dir
     */
    public function deleteDir($dir) {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}
