<div id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}_code"
     data-textarea-id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
     data-mode="{{ isset($field->mode)? $field->mode : 'html'}}"
     data-theme="{{ isset($field->theme)? $field->theme : 'github'}}"
     data-minlines="{{ isset($field->minlines)? $field->minlines : 4 }}"
     data-maxlines="{{ isset($field->maxlines)? $field->maxlines : 100 }}"
     data-field-type="{{$field->type}}"
     class="adv-form-control form-control inline-code-editor"
     @include('voyager-extension::formfields.advinlinesetitem.attr')
     >{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}</div>

<textarea id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          class="adv-form-control form-control hidden"
          name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          data-field-type="{{$field->type}}"
          data-ace="true"
          >{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}</textarea>
