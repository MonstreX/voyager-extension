<textarea id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          class="adv-form-control form-control inlineSetRichTextBox"
          name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          data-field-type="{{$field->type}}"
          data-min-height="{{isset($field->min_height)? $field->min_height : 100}}"
          @include('voyager-extension::formfields.advinlinesetitem.attr')
          >
    {{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}
</textarea>
