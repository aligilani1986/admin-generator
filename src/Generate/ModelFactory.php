<?php namespace Aligilani\AdminGenerator\Generate;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModelFactory extends FileAppender {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:generate:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Append a new factory';

    /**
     * Path for view
     *
     * @var string
     */
    protected $view = 'factory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        //TODO check if exists
        //TODO make global for all generator
        //TODO also with prefix
        if (!empty($template = $this->option('template'))) {
            $this->view = 'templates.' . $template . '.factory';
        }

        if ($this->appendIfNotAlreadyAppended(base_path('database/factories/ModelFactory.php'), $this->buildClass())) {
            $this->info('Appending ' . $this->modelBaseName . ' model to ModelFactory finished');
        }

        if ($this->option('seed')) {
            $this->info('Seeding testing data');
            factory($this->modelFullName, 50)->create();
        }
    }

    /**
     * @return mixed
     */
    protected function buildClass() {

        return view('brackets/admin-generator::' . $this->view, [
            'modelFullName' => $this->modelFullName,

            'columns'       => $this->readColumnsFromTable($this->tableName)
            // we skip primary key
                ->filter(function ($col) {
                    return 'id' != $col['name'];
                })
                ->map(function ($col) {
                    if ('deleted_at' == $col['name']) {
                        $type = 'null';
                    } else if ('remember_token' == $col['name']) {
                        $type = 'null';
                    } else {
                        if ('date' == $col['type']) {
                            $type = '$faker->date()';
                        } elseif ('time' == $col['type']) {
                            $type = '$faker->time()';
                        } elseif ('datetime' == $col['type']) {
                            $type = '$faker->dateTime';
                        } elseif ('text' == $col['type']) {
                            $type = '$faker->text()';
                        } elseif ('boolean' == $col['type']) {
                            $type = '$faker->boolean()';
                        } elseif ('integer' == $col['type'] || 'numeric' == $col['type'] || 'decimal' == $col['type']) {
                            $type = '$faker->randomNumber(5)';
                        } elseif ('float' == $col['type']) {
                            $type = '$faker->randomFloat';
                        } elseif ('title' == $col['name']) {
                            $type = '$faker->sentence';
                        } elseif ('email' == $col['name']) {
                            $type = '$faker->email';
                        } elseif ('name' == $col['name'] || 'first_name' == $col['name']) {
                            $type = '$faker->firstName';
                        } elseif ('surname' == $col['name'] || 'last_name' == $col['name']) {
                            $type = '$faker->lastName';
                        } elseif ('slug' == $col['name']) {
                            $type = '$faker->unique()->slug';
                        } elseif ('password' == $col['name']) {
                            $type = 'bcrypt($faker->password)';
                        } else {
                            $type = '$faker->sentence';
                        }
                    }
                    return [
                        'name'  => $col['name'],
                        'faker' => $type,
                    ];
                }),
            'translatable'  => $this->readColumnsFromTable($this->tableName)->filter(function ($column) {
                return "json" == $column['type'];
            })->pluck('name'),
        ])->render();
    }

    protected function getOptions() {
        return [
            ['model-name', 'm', InputOption::VALUE_OPTIONAL, 'Generates a code for the given model'],
            ['template', 't', InputOption::VALUE_OPTIONAL, 'Specify custom template'],
            ['seed', 's', InputOption::VALUE_OPTIONAL, 'Seeds the table with fake data'],
            ['model-with-full-namespace', 'fnm', InputOption::VALUE_OPTIONAL, 'Specify model with full namespace'],
        ];
    }

}