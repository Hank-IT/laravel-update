<?php

namespace MrCrankHank\Update\Commands;

use Exception;
use Illuminate\Console\Command;

class GenerateJsonFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:generate-json-file {file : Name of the file.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a json file with all application file paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = $this->getDirContents(base_path());

        $config = config('update');

        // Don't include ignored directories
        foreach ($files as $key => $file) {
                foreach ($config['ignore_dirs'] as $dir) {
                    if (strpos($file, $dir) !== false) {
                        unset($files[$key]);
                    }
                }
        }

        // Add files which are not part of the release but should not be deleted on update
        // We just merge them in the files array, which makes them part of the installation
        // therefore they won't be deleted
        $files = array_merge($files, $config['ignore_files']);

        $files = json_encode($files, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $error = file_put_contents(base_path($this->argument('file')), $files);

        if ($error === false) {
            throw new Exception;
        }
    }

    protected function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = $dir.DIRECTORY_SEPARATOR.$value;
            if(!is_dir($path)) {
                $results[] = str_replace(base_path() . '/', '', $path);
            } else if($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
            }
        }

        return $results;
    }
}