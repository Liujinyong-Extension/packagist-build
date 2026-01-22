<?php

namespace Liujinyong\PackagistBuild\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class Build extends Base
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }


    protected function configure()
    {
        $this->setName('create')
            ->setDescription('创建一个基础开发包');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln("<info>懒是程序员的终极生产力：为了永远偷懒，所以拼命勤快</info>");
        $output->writeln("<comment>  以下是此命令【create】创建扩展包的流程步骤</comment>");
        $table = new Table($output);
        $table->setHeaders(array('步骤', '事项'))->setRows(array(
            array('[1.创建包路径]', '创建扩展包的路径，为空的话默认当前路径'),
            array('[2.命名扩展包]', '命名当前扩展包'),
            array('[3.填写描述]', '描述当前扩展包'),
        ));
        $table->render();


        $question = new Question("<1>提供扩展包路径[ D:\packagist\packagist-name],不传默认当前路径:", ' ');
        $question->setValidator(function ($value) use ($output) {
            if (trim($value) != '') {
                return trim($value);
            } else {
                return null;
            }
        });
        $packagistDir = $this->getHelperHandle()->ask($input, $output, $question);
        $question->setMaxAttempts(3);

        $question = new Question("<2>请命名你的扩展包,[your-name/your-packagist-name]:");
        $question->setValidator(function ($value) use ($output) {
            if (trim($value) == '') {
                throw new \Exception('⚠️ 警告：包名不能为空！');

            }
            if (strpos($value, '/') === false) {
                throw new \Exception('⚠️ 警告：包名不能没有[/]！');

            }
            return trim($value);
        });
        $packagistName = $this->getHelperHandle()->ask($input, $output, $question);

        $question->setMaxAttempts(3);

        $question = new Question("<3>请填写描述:");
        $question->setValidator(function ($value) use ($output) {
            return trim($value);
        });
        $des = $this->getHelperHandle()->ask($input, $output, $question);
        $question->setMaxAttempts(3);
        //创建文件目录
        if (!is_dir($packagistDir)) {
            $packagistDir = getcwd() . '\\' . str_replace('/', '-', $packagistName);
        }
        mkdir($packagistDir . '\\src\\Exceptions', 0777, true);
        mkdir($packagistDir . '\\src\\Handler', 0777, true);

        //copy 文件到指定的目录

        list($a, $b) = explode('/', $packagistName);
        list($c, $d) = preg_split('/[-_]+/', $b);
        $namespace = ucfirst($a) . '\\\\' . ucfirst($c);
        $namespace .= $d != null ? ucfirst($d) . '\\\\' : '\\\\';

        $controllerNameSpace = 'namespace ' . str_replace('\\\\', '\\', $namespace) . 'Exceptions;';

        $fs = new Filesystem();

        $files = array_diff(scandir(__DIR__ . '/../stubs/'), array('.', '..'));
        foreach ($files as $file) {
            if ($file == 'composer_json') {
                $content = str_replace("{{name}}", $packagistName, file_get_contents(__DIR__ . '/../stubs/' . $file));
                $content = str_replace("{{namespace}}", $namespace, $content);
                if ($fs->exists($packagistDir . '\\composer.json')) {
                    $fs->remove($packagistDir . '\\composer.json');
                }
                $fs->dumpFile($packagistDir . '\\composer.json', $content);
            } else if ($file == '.gitignore') {

                if ($fs->exists($packagistDir . '\\.gitignore')) {
                    $fs->remove($packagistDir . '\\.gitignore');
                }
                $fs->copy(__DIR__ . '/../stubs/' . $file,$packagistDir . '\\.gitignore');
            } else {
                $content = str_replace("{{namespace}}", $controllerNameSpace, file_get_contents(__DIR__ . '/../stubs/' . $file));
                if ($fs->exists($packagistDir . '\\src\\Exceptions\\' . str_replace('_', '.', $file))) {
                    $fs->remove($packagistDir . '\\src\\Exceptions\\' . str_replace('_', '.', $file));
                }
                $fs->dumpFile($packagistDir . '\\src\\Exceptions\\' . str_replace('_', '.', $file), $content);
            }
        }


        return 0;
    }
}