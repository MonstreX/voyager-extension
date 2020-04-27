@if(!isset($edit))
@php
$urls = explode(PHP_EOL, $data->{$row->field});
foreach ($urls as $key => $url) {
    if (empty($url)) {
        unset($urls[$key]);
    }
}
@endphp
<span class="urls-block-list @if((int)$data->rules === 0) rules-a  @else rules-b @endif" >
    <ul>
    @foreach($urls as $url)
        @if($loop->index < 5)
        <li>{{ $url }}</li>
        @endif
    @endforeach
    </ul>
</span>
@endif