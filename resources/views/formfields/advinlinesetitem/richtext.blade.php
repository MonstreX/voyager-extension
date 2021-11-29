<textarea id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          class="form-control inlineSetRichTextBox"
          data-field-type="{{$field->type}}"
          name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
          @include('voyager-extension::formfields.advinlinesetitem.attr')
          >
    {{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}
</textarea>
