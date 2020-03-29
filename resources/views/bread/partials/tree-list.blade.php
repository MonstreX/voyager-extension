<ol class="dd-list">
    @foreach($items as $key => $item)

    @php
        $data = $dataTypeContent->filter(function ($record, $key) use($item) {
            return $record->id === $item['id'] ;
        })->first();
    @endphp

    <li data-record-id="{{$item['id']}}" data-slug="{{$dataType->slug}}" class="dd-item @if(isset($item['status']) && $item['status'] === 0) unpublished-record @endif" data-id="{{ $item['id'] }}">

        <div class="dd-tree-handle">
            <div class="dd-tree-move">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="dd-handle">
            <div class="dd-content-holder">
                <div class="dd-content-main">
                @foreach($dataType->browseRows as $row)
                    @if($row->browse)
                        @if(isset($row->details->browse_inline_checkbox))
                        <span class="tree-{{ $row->field }}">
                            <input type="checkbox" data-id="{{ $item['id'] }}" name="{{ $row->field }}" @if($item[$row->field]) checked @endif class="tiny-toggle" data-tt-type="dot" data-tt-size="tiny">
                        </span>
                        @else
                            @if($row->field !== 'parent_id')
                            @if(isset($row->details->url)) <a href="{{ route('voyager.'.$dataType->slug.'.'.$row->details->url, $item['id']) }}"> @endif
                            <span class="tree-{{$row->field}} tree-extra-fields @if(isset($row->details->browse_tree_push_right)) right-auto @endif">{{ mb_strlen( $item[$row->field] ) > 200 ? mb_substr($item[$row->field], 0, 200) . ' ...' : $item[$row->field] }}</span>
                            @if(isset($row->details->url)) </a> @endif
                            @endif
                        @endif
                    @endif
                @endforeach
                </div>
                <div class="dd-content-actions">
                    <div class="no-sort no-click" id="bread-actions">
                        @foreach($actions as $action)
                            @include('voyager::bread.partials.actions', ['action' => $action])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if(isset($item['children']))
            @include('voyager-extension::bread.partials.tree-list', ['items' => $item['children']])
        @endif
    </li>
    @endforeach
</ol>
