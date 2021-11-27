<?php

namespace RocketsLab\Neewton\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class NeewtonInstallCommand extends Command
{
    protected $signature = "neewton:install 
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Neewton resources';

    public function handle()
    {
        $this->installInertiaVueStack();

        // Make inertia default Page directory
        if(!(new Filesystem)->exists(resource_path('js/Pages'))){
            (new Filesystem)->makeDirectory(resource_path('js/Pages'));
            copy(__DIR__.'/../../stubs/.gitkeep', resource_path('js/Pages/.gitkeep'));
        }

        // Copy stubs
        copy(__DIR__.'/../../stubs/js/app.js', resource_path('js/app.js'));
    }

    protected function installInertiaVueStack()
    {
        // Install inertia
        $this->requireComposerPackages('inertiajs/inertia-laravel:^0.4.3');

        // NPM Packages
        $this->updateNodePackages(function ($packages) {
            return [
                    '@inertiajs/inertia' => '^0.10.0',
                    '@inertiajs/inertia-vue3' => '^0.5.1',
                    '@vue/compiler-sfc' => '^3.0.5',
                    'vue' => '^3.0.5',
                    'vue-loader' => '^16.1.2',
                ] + $packages;
        });

        $this->info('Neewton scaffolding installed successfully.');
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
        $this->info('Perform "php artisan clear && php artisan view:clear" to clear appliacation cache.');
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }


    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

}
