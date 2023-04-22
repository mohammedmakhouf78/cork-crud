<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class EditViewService extends AbstractParent
{

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/EditView.php.stub");
        return $this->stub;
    }

    public function prepareStub()
    {
        $editFields = "";
        foreach ($this->columns as $column) {
            if ($column->type == "string" && $column->lang) {
                $editFields .= "<div class='row mb-4'>\n";
                $editFields .= <<<END
                    <div class="col">
                        <label>{{ trans('main.{$column->name}_ar') }}</label>
                        <input type="text" name="{$column->name}_ar" class="form-control" value="{{\${$this->modelLower}->getTranslation('$column->name', 'ar')}}">
                        @error('{$column->name}_ar')
                            <p class="text-danger my-1">{{\$message}}</p>
                        @enderror
                    </div>\n
                END;
                $editFields .= <<<END
                    <div class="col">
                        <label>{{ trans('main.{$column->name}_en') }}</label>
                        <input type="text" name="{$column->name}_en" class="form-control" value="{{\${$this->modelLower}->getTranslation('$column->name', 'en')}}">
                        @error('{$column->name}_en')
                            <p class="text-danger my-1">{{\$message}}</p>
                        @enderror
                    </div>\n
                END;
                $editFields .= "</div>\n";
            } else if ($column->type == "text" && $column->lang) {
                $editFields .= "<div class='row mb-4'>\n";
                $editFields .= <<<END
                    <div class="col">
                        <label>{{ trans('main.{$column->name}_ar') }}</label>
                        <textarea name="{$column->name}_ar" class="form-control">{{\${$this->modelLower}->getTranslation('$column->name', 'ar')}}</textarea>
                        @error('{$column->name}_ar')
                            <p class="text-danger my-1">{{\$message}}</p>
                        @enderror
                    </div>\n
                END;
                $editFields .= <<<END
                    <div class="col">
                        <label>{{ trans('main.{$column->name}_en') }}</label>
                        <textarea name="{$column->name}_en" class="form-control">{{\${$this->modelLower}->getTranslation('$column->name', 'en')}}</textarea>
                        @error('{$column->name}_en')
                            <p class="text-danger my-1">{{\$message}}</p>
                        @enderror
                    </div>\n
                END;
                $editFields .= "</div>\n";
            } else if ($column->type == "image") {
                $editFields .= <<<END
                    <div class="row mb-4">
                        <div class="col">
                            <input type="file" class="form-control" name="$column->name">
                            @error('$column->name')
                                <p class="text-danger my-1">{{\$message}}</p>
                            @enderror
                        </div>
                    </div>\n
                END;
            } else if ($column->type == "select") {
                $relationVariable = $this->prepareRelationName($column->name);
                $selectVal = $column->selectVal ?? 'id';
                $selectDis = $column->selectDis ?? 'name';


                $editFields .= <<<END
                    <div class="row mb-4">
                        <div class="col">
                            <label>{{ trans('main.{$column->name}') }}</label>
                            <select class="form-control" name="$column->name">
                                <option value=""> </option>
                                @foreach(\${$relationVariable}Collection as \${$relationVariable})
                                <option value="{{ \${$relationVariable}->$selectVal }}" 
                                {{\${$this->modelLower}->$column->name ==  \${$relationVariable}->$selectVal ? "selected" : "" }}> 
                                    {{ \${$relationVariable}->$selectDis }}
                                </option>
                                @endforeach
                            </select>
                            @error('$column->name')
                                <p class="text-danger my-1">{{\$message}}</p>
                            @enderror
                        </div>
                    </div>\n
                END;
            } else if ($column->type == "integer") {
                $editFields .= <<<END
                    <div class="col">
                        <label>{{ trans('main.{$column->name}') }}</label>
                        <input type="number" name="{$column->name}" class="form-control" value="{{\${$this->modelLower}->$column->name}}">
                        @error('{$column->name}')
                            <p class="text-danger my-1">{{\$message}}</p>
                        @enderror
                    </div>\n
                END;
            }
        }

        $this->stub = str_replace("{model_lower}", $this->modelLower, $this->stub);
        $this->stub = str_replace("{inputs}", $editFields, $this->stub);
        $this->stub = str_replace("{enctype}", $this->hasImage ? 'enctype="multipart/form-data"' : "", $this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("resources/views/admin/pages/{$this->modelLower}"))) {
            mkdir(base_path("resources/views/admin/pages/{$this->modelLower}"), 0777, true);
        }

        File::put(base_path("resources/views/admin/pages/{$this->modelLower}/edit.blade.php"), $this->stub);
    }
}
