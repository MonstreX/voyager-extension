@push('javascript')
<template id="change-file-template">
    <form id="change-file-form">
        <input type="hidden" name="_token" value="">
        <input type="hidden" name="model" value="">
        <input type="hidden" name="slug" value="">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="field" value="">
        <input type="hidden" name="media_file_id" value="">
        <div class="change-file-holder">
            <img src="" alt="">
        </div>
        <div class="file-change-input">
            <input id="change_file_input" type="file" name="adv_media_files_field" accept="image/*" />
        </div>
    </form>
</template>
@endpush


@section('css')
    111111111111111111
@endsection