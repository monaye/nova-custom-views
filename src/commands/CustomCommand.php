<?php


namespace devmtm\NovaCustomViews;


use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;

abstract class CustomCommand extends Command
{
    abstract protected function viewsPath();

    /**
     * Install the views's NPM dependencies.
     *
     * @return void
     */
    protected function installNpmDependencies()
    {
        $this->runCommand('npm set progress=false && npm install', [$this->viewsPath()], $this->output);
    }

    /**
     * Compile the views's assets.
     *
     * @return void
     */
    protected function compile()
    {
        $this->runCommand('npm run dev', [$this->viewsPath()], $this->output);
    }

    /**
     * Update the project's composer dependencies.
     *
     * @return void
     */
    protected function composerUpdate()
    {
        $this->runCommand('composer update', [getcwd()], $this->output);
    }

    /**
     * Run the given command as a process.
     *
     * @param  string  $command
     * @param  string  $path
     * @return void
     */
    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        $process = (new Process($command, $arguments[0]))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }
}
