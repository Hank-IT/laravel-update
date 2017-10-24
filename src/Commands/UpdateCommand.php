<?php

namespace MrCrankHank\Update\Commands;

use Exception;
use ParentIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application';

    public function handle()
    {
        if (! file_exists(base_path('files.json'))) {
            throw new Exception;
        }

        $this->call('down');

        $this->info('');

        $releaseFiles = json_decode(file_get_contents(base_path('files.json')), true);

        $this->call('update:generate-json-file', ['file' => 'current.json']);

        $currentFiles = json_decode(file_get_contents(base_path('current.json')), true);

        $files = array_diff($currentFiles, array_merge($releaseFiles, ['files.json', 'current.json']));

        if (! empty($files)) {
            $this->deleteFiles($files);
        } else {
            $this->info('No files to delete.');
        }

        $dirs = $this->getEmptySubFolders(base_path());

        if (! empty($dirs)) {
            $this->deleteDirectories($dirs);
        } else {
            $this->info('No directories to delete.');
        }

        $this->info('');
        $this->info('');

        $this->finish();
    }

    protected function getEmptySubFolders($path)
    {
        $iterator = new RecursiveDirectoryIterator(realpath($path));

        // Get directories only.
        $directories = new ParentIterator($iterator);

        // Loop over directories and remove empty ones.
        $dirsToDelete = [];
        $dirs = new RecursiveIteratorIterator($directories, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($dirs as $key => $dir) {
            // Count the number of "children" from the main directory iterator.
            // Compare with 2 because "." and ".." are always there.
            if (iterator_count($iterator->getChildren()) == 2) {
                $dirsToDelete[] = $dir->getPathname();
            }
        }

        return $dirsToDelete;
    }

    protected function deleteFiles($files)
    {
        $this->info('The following files can be safely deleted:');

        foreach ($files as $file) {
            $this->comment(base_path($file));
        }

        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('No files deleted!.');
            return;
        }

        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            unlink(base_path($file));

            $bar->advance();
        }
    }

    protected function deleteDirectories($dirs)
    {
        $this->info('The following directories can be safely deleted:');

        foreach ($dirs as $dir) {
            $this->comment($dir);
        }

        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('No folders deleted!.');
            return;
        }

        $bar = $this->output->createProgressBar(count($dirs));

        foreach ($dirs as $dir) {
            rmdir($dir);

            $bar->advance();
        }
    }

    protected function finish()
    {
        $this->call('migrate', ['--force']);

        unlink(base_path('files.json'));
        unlink(base_path('current.json'));

        $this->call('clear-compiled');
        $this->call('view:clear');
        $this->call('cache:clear');

        $this->call('config:cache');
        $this->call('optimize');
        $this->call('up');

        $this->info('Update is done.');
    }
}
