<?php

namespace Aligilani\AdminGenerator\Tests\Feature\Classes;

use Aligilani\AdminGenerator\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\File;

class UpdateRequestNameTest extends TestCase {
    use DatabaseMigrations;

    /**
     * @test
     */
    public function update_request_generation_should_generate_an_update_request_name(): void {
        $filePath = base_path('app/Http/Requests/Admin/Category/UpdateCategory.php');

        $this->assertFileDoesNotExist($filePath);

        $this->artisan('admin:generate:request:update', [
            'table_name' => 'categories',
        ]);

        $this->assertFileExists($filePath);
        $this->assertStringStartsWith('<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCategory extends FormRequest', File::get($filePath));
    }

    /**
     * @test
     */
    public function is_generated_correct_name_for_custom_model_name(): void {
        $filePath = base_path('app/Http/Requests/Admin/Billing/Cat/UpdateCat.php');

        $this->assertFileDoesNotExist($filePath);

        $this->artisan('admin:generate:request:update', [
            'table_name'   => 'categories',
            '--model-name' => 'Billing\\Cat',
        ]);

        $this->assertFileExists($filePath);
        $this->assertStringStartsWith('<?php

namespace App\Http\Requests\Admin\Billing\Cat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateCat extends FormRequest', File::get($filePath));
    }
}
