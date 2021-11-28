@if(!$source)<template id="template_{{$row_field}}">@endif
<div class="adv-inline-set-item columns-{{$columns}} {{ !$source? 'adv-inline-set-template' : '' }}"
     data-new="{{ !$source? true : false }}"
     data-id="{{ $id }}"
     data-row-id="{{ $row_id }}">
    <div class="adv-inline-set-handle">
        <span></span><span></span><span></span>
    </div>
    <div class="adv-inline-set-holder">
        <div class="row">
            @foreach($inline_fields as $key_field => $field)
            <div class="form-group {{ isset($field->class)? $field->class : 'col-md-12' }}">
                <label for="{{$row_field}}_{{$key_field}}_{{$row_id}}">{{$field->label}}</label>
                @include('voyager-extension::formfields.advinlinesetitem.'.$field->type)
            </div>
            @endforeach
        </div>
    </div>
    <button type="button" class="adv-inline-set-delete">
        <i class="voyager-x"></i>
    </button>
</div>
@if(!$source)</template>@endif





