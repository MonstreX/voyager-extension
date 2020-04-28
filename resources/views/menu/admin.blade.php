<ol class="dd-list">

@foreach ($items as $item)

    <li class="dd-item" data-id="{{ $item->id }} @if(!$item->status) unpublished-record @endif">
        <div class="pull-right item_actions">
            <div class="btn btn-sm btn-danger pull-right delete" data-id="{{ $item->id }}">
                <i class="voyager-trash"></i> {{ __('voyager::generic.delete') }}
            </div>
            <div class="btn btn-sm btn-primary pull-right edit"
                data-id="{{ $item->id }}"
                data-title="{{ $item->title }}"
                data-url="{{ $item->url }}"
                data-target="{{ $item->target }}"
                data-icon_class="{{ $item->icon_class }}"
                data-color="{{ $item->color }}"
                data-route="{{ $item->route }}"
                data-parameters="{{ json_encode($item->parameters) }}"
            >
                <i class="voyager-edit"></i> {{ __('voyager::generic.edit') }}
            </div>
        </div>
        <div class="dd-handle admin-menu-title">
            @if($options->isModelTranslatable)
                @include('voyager::multilingual.input-hidden', [
                    'isModelTranslatable' => true,
                    '_field_name'         => 'title'.$item->id,
                    '_field_trans'        => json_encode($item->getTranslationsOf('title'))
                ])
            @endif

            <span>{{ $item->title }}</span> <small class="url">{{ $item->link() }}</small>
        </div>
        <div class="dd-admin-checkbox">
            <span class="tree-admin-status">
                <input type="checkbox" data-slug="menu-items" data-id="{{ $item->id }}" name="status" @if($item->status) checked @endif class="tiny-toggle" data-tt-type="dot" data-tt-size="tiny">
            </span>
        </div>

    @if(!$item->children->isEmpty())
            @include('voyager-extension::menu.admin', ['items' => $item->children])
        @endif
    </li>

@endforeach

</ol>
