<?php
/**
 * Created by liuguansheng.
 * Email: w193241125@163.com
 * Time: 2018/9/27 15:22
 */
namespace App\Console\Commands\Larwas;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class BaseModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larwas:model';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class extends BaseModel';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'BaseModel';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';    //对应你的模板文件目录
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {

        $stub = str_replace(
            ['ModelNamespace', 'ModelName'],
            [$this->getNamespace($name), $this->setModel()],
            $stub
        );

        return $this;
    }


    /**
     * set Model
     *
     */
    private function setModel()
    {
        if (!empty($this->option('model'))) {
            return $this->option('model');
        } else {
            $name = explode('/', $this->getNameInput('name'));
            return str_replace('Model','',$name[count($name)-1]);
        }
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Injection  model.']
        ];
    }
}
