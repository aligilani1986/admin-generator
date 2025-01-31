@php echo "<?php"
@endphp


namespace App\Http\Requests\Admin\{{ $modelWithNamespaceFromDefault }};
@php
    if($translatable->count() > 0) {
        $translatableColumns = $columns->filter(function($column) use ($translatable) {
            return in_array($column['name'], $translatable->toArray());
        });
        $standardColumn = $columns->reject(function($column) use ($translatable) {
            return in_array($column['name'], $translatable->toArray());
        });
    }
@endphp

@if($translatable->count() > 0)use Aligilani\Translatable\TranslatableFormRequest;
@else
use Illuminate\Foundation\Http\FormRequest;
@endif
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

@if($translatable->count() > 0)class Store{{ $modelBaseName }} extends TranslatableFormRequest
@else
class Store{{ $modelBaseName }} extends FormRequest
@endif
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * {{'@'}}return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.{{ $modelDotNotation }}.create');
    }

@if($translatable->count() > 0)/**
     * Get the validation rules that apply to the requests untranslatable fields.
     *
     * {{'@'}}return array
     */
    public function untranslatableRules(): array {
        return [
            @foreach($standardColumn as $column)'{{ $column['name'] }}' => [{!! implode(', ', (array) $column['serverStoreRules']) !!}],
            @endforeach
@if (count($relations))
    @if (count($relations['belongsToMany']))

            @foreach($relations['belongsToMany'] as $belongsToMany)'{{ $belongsToMany['related_table'] }}' => [{!! implode(', ', ['\'array\'']) !!}],
            @endforeach
    @endif
@endif

        ];
    }

    /**
     * Get the validation rules that apply to the requests translatable fields.
     *
     * {{'@'}}return array
     */
    public function translatableRules($locale): array {
        return [
            @foreach($translatableColumns as $column)'{{ $column['name'] }}' => [{!! implode(', ', (array) $column['serverStoreRules']) !!}],
            @endforeach

        ];
    }
@else
    /**
     * Get the validation rules that apply to the request.
     *
     * {{'@'}}return array
     */
    public function rules(): array
    {
        return [
            @foreach($columns as $column)
@if(!($column['name'] == "updated_by_admin_user_id" || $column['name'] == "created_by_admin_user_id" ))'{{ $column['name'] }}' => [{!! implode(', ', (array) $column['serverStoreRules']) !!}],
@endif
            @endforeach
@if (count($relations))
    @if (count($relations['belongsToMany']))

            @foreach($relations['belongsToMany'] as $belongsToMany)'{{ $belongsToMany['related_table'] }}' => [{!! implode(', ', ['\'array\'']) !!}],
            @endforeach
    @endif
@endif

        ];
    }
@endif

    /**
    * Modify input data
    *
    * {{'@'}}return array
    */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        //Add your code for manipulation with request data here

        return $sanitized;
    }
}
