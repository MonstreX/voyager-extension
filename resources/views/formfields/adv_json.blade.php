<div class="adv-json-wrapper">
    @php
    if(!isset($options->json_fields)) {
        $options->json_fields->key = 'Key';
        $options->json_fields->value = 'Value';
    }
    @endphp

    <input id="{{$row->field}}" name="{{$row->field}}" type="hidden" value="{{ $dataTypeContent->{$row->field} }}">
    <div id="adv-json-list-{{$row->field}}" class="adv-json-list {{$row->field}}" data-field="{{$row->field}}">

    @if($fields = json_decode($dataTypeContent->{$row->field}))
        @foreach($fields as $key => $field)
        <div class="adv-json-item">
            @foreach($field as $key2 => $input)
            <div class="form-group-line">
                <input type="text" data-master-field="{{$row->field}}" data-field="{{ $input->key }}" data-title="{{ $input->title }}" class="form-control" value="{{ $input->value }}">
            </div>
            @endforeach
            <div class="form-group-line">
                <button data-field="{{ $row->field }}" data-remove="Remove" type="button" class="btn btn-danger remove-json"><i class='voyager-x'></i></button>
            </div>
        </div>
        @endforeach
    @endif

    </div>
    <div class="adv-json-add-holder">
        <div class="adv-json-add-form">
            @foreach($options->json_fields as $key => $value)
            <div class="form-group-line">
                <label for="{{$row->field.'-'.$key}}">{{$value}} ({{$key}})</label>
                <input id="{{$row->field.'-'.$key}}" type="text" data-master-field="{{$row->field}}"  data-field="{{$key}}" data-title="{{$value}}" class="form-control">
            </div>
            @endforeach
            <div class="form-group-line">
                <button data-field="{{$row->field}}" data-remove="<i class='voyager-x'></i>" type="button" class="btn btn-success add-json"><i class='voyager-list-add'></i></button>
            </div>
        </div>
    </div>
</div>