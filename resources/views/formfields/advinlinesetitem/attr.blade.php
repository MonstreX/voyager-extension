@if(isset($field->attrs))
    @foreach($field->attrs as $attr => $data)
    {{$attr}}="{{$data}}"
    @endforeach
@endif
