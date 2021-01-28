<?php

namespace NovaCustomViews\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Laravel\Nova\Console\Concerns\AcceptsNameAndVendor;
use Laravel\Nova\Nova;
use Symfony\Component\Process\Process;

class DashboardViewCommand extends CustomCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dashboard view';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if(!is_dir($this->viewsBasePath())) {
            mkdir($this->viewsBasePath());
        }

        (new Filesystem)->copyDirectory(
            __DIR__ . '/../stubs/dashboard',
            $this->viewsPath()
        );

        (new Filesystem)->move(
            $this->viewsPath().'/src/DashboardViewServiceProvider.stub',
            $this->viewsPath().'/src/DashboardViewServiceProvider.php'
        );

        // Register the views...
        $this->addViewsRepositoryToRootComposer();
        $this->addViewsPackageToRootComposer();
        $this->addScriptsToNpmPackage();

        if ($this->confirm("Would you like to install the views's NPM dependencies?", true)) {
            $this->installNpmDependencies();

            $this->output->newLine();
        }

        if ($this->confirm("Would you like to compile the views's assets?", true)) {
            $this->compile();

            $this->output->newLine();
        }

        if ($this->confirm('Would you like to update your Composer packages?', true)) {
            $this->composerUpdate();
        }
    }

    /**
     * Add a path repository for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addViewsRepositoryToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['repositories'][] = [
            'type' => 'path',
            'url' => './'.$this->relativeViewsPath(),
        ];

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a package entry for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addViewsPackageToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['require']['nova-custom-views/dashboard-view'] = '*';

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a path repository for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addScriptsToNpmPackage()
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true);

        $package['scripts']['build-dashboard-view'] = 'cd '.$this->relativeViewsPath().' && npm run dev';
        $package['scripts']['build-dashboard-view'.'-prod'] = 'cd '.$this->relativeViewsPath().' && npm run prod';

        file_put_contents(
            base_path('package.json'),
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Get the path to the tool.
     *
     * @return string
     */
    protected function viewsBasePath()
    {
        return base_path('nova-components/views/');
    }

    /**
     * Get the path to the tool.
     *
     * @return string
     */
    protected function viewsPath()
    {
        return base_path('nova-components/views/dashboard');
    }

    /**
     * Get the relative path to the views.
     *
     * @return string
     */
    protected function relativeViewsPath()
    {
        return 'nova-components/views/dashboard';
    }

}
