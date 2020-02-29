@push('javascript')
<template id="change-image-template">
    <form id="change-image-form">
        <input type="hidden" name="_token" value="">
        <input type="hidden" name="model" value="">
        <input type="hidden" name="slug" value="">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="field" value="">
        <input type="hidden" name="image_id" value="">
        <div class="change-image-holder">
            <img src="" alt="">
        </div>
        <div class="file-change-input">
            <input id="change_image_input" type="file" name="adv_media_files_field" accept="image/*"/>
        </div>
    </form>
</template>

<div class="modal fade modal-danger" id="adv_confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
            </div>
            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} <span class="adv_confirm_delete_name"></span> @lang('voyager-extension::bread.adv_media_files.dialog_remove_images')</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="adv_confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>

@endpush


