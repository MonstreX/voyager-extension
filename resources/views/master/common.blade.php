@push('javascript')
<script>
    if (!window.rootAdminRoute) {
        window.rootAdminRoute = '{{ config('voyager-extension.assets_path_prefix') }}';
    }
</script>
@endpush
