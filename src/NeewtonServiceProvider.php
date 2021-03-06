<?php

namespace RocketsLab\Neewton;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use RocketsLab\Neewton\Console\NeewtonInstallCommand;
use RocketsLab\Neewton\Contracts\ModuleContract;

class NeewtonServiceProvider extends ServiceProvider
{
    protected $modulesConfiguration = [];

    public function register()
    {
        //
        $this->mergeConfigFrom(
            __DIR__.'/../config.php', 'neewton'
        );
    }

    public function boot()
    {
        $this->bootModules();

        $this->registerBladeDirective();

        if($this->app->runningInConsole()) {
            $this->commands([
                NeewtonInstallCommand::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../config.php' => config_path('neewton.php'),
        ]);
    }

    protected function bootModules()
    {
        foreach ($modules = config('neewton.active_modules') as $moduleClass) {

            $instance = app($moduleClass);

            if (!$instance instanceof ModuleContract) {
                throw new \Exception("A classe {$moduleClass} não é um módulo.");
            }

            if (!$instance->depends()) {
                $this->modulesConfiguration[] = $instance->configure();
                continue;
            }

            foreach ($instance->depends() as $dependencyClass) {
                if (!in_array($dependencyClass, $modules)) {
                    throw new \Exception("Modulo {$moduleClass} depends from {$dependencyClass}.");
                }
            }

            $this->modulesConfiguration[] = $instance->configure();
        }
    }

    protected function registerBladeDirective()
    {
        $neewtonModules = base64_encode(json_encode($this->modulesConfiguration, true));
        Blade::directive('neewtonModules', fn() => "<meta name='neewton-modules' content='$neewtonModules'>");
    }

}
