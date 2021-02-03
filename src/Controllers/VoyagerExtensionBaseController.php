<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use TCG\Voyager\Facades\Voyager;

use MonstreX\VoyagerExtension\ContentTypes\AdvImageContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvMediaFilesContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvFieldsGroupContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvPageLayoutContentType;

use Str;

class VoyagerExtensionBaseController extends VoyagerBaseController
{

    public function index(Request $request)
    {

        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            //-------- Extended #1
            $filters = null;
            if ($request->has('field') && $request->has('value')) {
                $filters  = ['field' => $request->get('field'), 'value' => $request->get('value')];
                $request->session()->put('filters', $filters);
            } elseif ($request->session()->has('filters') && !$request->has('reset_filters')) {
                $filters = $request->session()->get('filters');
            } else {
                $request->session()->forget('filters');
            }

            if ($filters) {
                foreach ($filters['field'] as $key => $filter) {
                    $query->where($filters['field'][$key], '=', $filters['value'][$key]);
                }
            }
            //-------- Extended #1 END

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'filters', // Extended params
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
        ));
    }

    public function edit(Request $request, $id)
    {

        $slug = $this->getSlug($request);
        View::share(['page_slug' => $slug, 'page_id' => $id]);
        return parent::edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        View::share(['page_slug' => $slug, 'page_id' => $id]);
        return parent::update($request, $id);
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);
        View::share('page_slug', $slug);
        return parent::create($request);
    }

    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {

        switch ($row->type) {
            case 'adv_image':
                return (new AdvImageContentType($request, $slug, $row, $options))->handle();
            case 'adv_media_files':
                return (new AdvMediaFilesContentType($request, $slug, $row, $options))->handle();
            case 'adv_fields_group':
                return (new AdvFieldsGroupContentType($request, $slug, $row, $options))->handle();
            case 'adv_page_layout':
                return (new AdvPageLayoutContentType($request, $slug, $row, $options))->handle();
            default:
                return Controller::getContentBasedOnType($request, $slug, $row, $options);
        }
    }

    /*
     *  $request - current request object
     *  $slug - slug (table name)
     *  $rows - data types rows of Voyager model
     *  $data - current model record
     */
    public function insertUpdateData($request, $slug, $rows, $data)
    {

        // we need to create a record (in an usual way) before associate image to the actual model record
        $result = VoyagerBaseController::insertUpdateData($request, $slug, $rows, $data);

        // Check through all our new field types
        foreach ($rows as $row) {

            // Bind Single Image to $data record
            if ($row->type == 'adv_image' && $request->hasFile($row->field)) {

                $data->addMediaFromRequest($row->field)
                    ->withCustomProperties(['title' => null, 'alt' => null])
                    ->setFileName($this->getFileName($request->file($row->field)))
                    ->toMediaCollection($row->field);

            } elseif ($row->type == 'adv_image') {

                // Save new Title and ALt props
                $mediaItem = $data->getFirstMedia($row->field);
                if ($mediaItem) {
                    $mediaItem->setCustomProperty('title', $request->input($row->field . "_title"));
                    $mediaItem->setCustomProperty('alt', $request->input($row->field . "_alt"));
                    $mediaItem->save();
                }

            } elseif ($row->type == 'adv_media_files' && $request->hasFile($row->field)) {

                // Bind Multiple Images to $data record
                $files = $request->file($row->field);
                foreach ($files as $file) {
                    if (!$file->isValid()) {
                        continue;
                    }
                    // Add default fields Title and Alt and Extra Fields if present
                    $fields = ['title' => null, 'alt' => null];
                    if ($row->details && isset($row->details->extra_fields)) {
                        foreach ($row->details->extra_fields as $key => $extra_field) {
                            $fields[$key] = null;
                        }
                    }

                    // Add Image
                    $data->addMedia($file)
                        ->withCustomProperties($fields)
                        ->setFileName($this->getFileName($file))
                        ->toMediaCollection($row->field);
                }
            }

        }

        return $result;
    }

    /*
    *
    * Clone an item BREA(D)
    *
    */
    public function clone(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $rows = $dataType->rows()->get();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $source = $data = app($dataType->model_name);
        $source = $source->where('id', $id)->first();
        $cloned = $source->replicate();

        $reset_types = config('voyager-extension.clone_record.reset_types');
        $suffix_fields = config('voyager-extension.clone_record.suffix_fields');

        foreach ($cloned->getAttributes() as $key => $value) {
            if (in_array($this->getRowByField($rows, $key)->type, $reset_types)) {
                $cloned->{$key} = null;
            }
            if (in_array($key, $suffix_fields)) {
                $cloned->{$key} = $cloned->{$key} . ' (clone)';
            }
        }

        $res = $cloned->save();

        $data = $res
            ? [
                'message' => __('voyager::generic.successfully_cloned') . " {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]
            : [
                'message' => __('voyager::generic.error_cloning') . " {$dataType->display_name_singular}",
                'alert-type' => 'error',
            ];

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /*
     * Used by Clone Method
     */
    private function getRowByField($rows, string $field)
    {
        return $rows->filter(function ($value, $key) use ($field) {
            return $value->field === $field;
        })->first();
    }

    /*
     *  Get Record Field
     */
    public function recordGet(Request $request)
    {
        try {

            $slug = $request->get('slug');
            $id = $request->get('id');
            $field = $request->get('field');

            // GET THE DataType based on the slug
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            // Load model and find record
            $model = app($dataType->model_name);
            $data = $model::find([$id])->first();

            // Check permission
            $this->authorize('edit', $data);

            // Check if field exists
            if (!isset($data->{$field}) && $data->{$field} !== null) {
                throw new Exception(__('voyager::generic.field_does_not_exist'), 400);
            }

            $fieldValue = $data->{$field};

            // Get field meta data and options
            $dataField = $this->getRowByField($dataType->browseRows, $field);

            // Special conversions for JSON group fields
            if ($dataField->type === 'adv_fields_group') {
                $group = json_decode($data->{$field});
                if (!isset($group->fields)) {
                    $fieldValue = $dataField->details->fields;
                } else {
                    $fieldValue = $group->fields;
                }
            }

            return json_response_with_success(200, '', $fieldValue);

        } catch (Exception $e) {
            return json_response_with_error(500, $e);
        }
    }

    /*
     *  Update Record Field
     */
    public function recordUpdate(Request $request)
    {
        try {

            $slug = $request->get('slug');
            $id = $request->get('id');
            $field = $request->get('field');
            $value = $request->get('value');
            $json = $request->get('json');
            $menu = $request->get('menu');

            // GET THE DataType based on the slug
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            if ($menu) {
                $model = app(Voyager::modelClass('MenuItem'));
                $data = $model::find([$id])->first();
            } else {
                // Load model and find record
                $model = app($dataType->model_name);
                $data = $model::find([$id])->first();
            }

            // Check permission
            $this->authorize('edit', $data);

            // Check if field exists
            if (!isset($data->{$field}) && $data->{$field} !== null) {
                throw new Exception(__('voyager::generic.field_does_not_exist'), 400);
            }

            if(isset($json) && $json) {
                $data->{$field} = json_encode(array_values($value));
            } else {
                $data->{$field} = $value;
            }

            $data->save();

            return json_response_with_success(200, __('voyager-extension::bread.record_updated'));

        } catch (Exception $e) {
            return json_response_with_error(500, $e);
        }
    }

    /*
     * Reorder Records according to the given TREE
     */
    public function recordsOrder(Request $request)
    {

        try {

            $slug = $request->get('slug');
            $itemsOrder = json_decode($request->get('order'));

            // GET THE DataType based on the slug
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            // Load model and load All Records to be ordered
            $model = app($dataType->model_name);

            // Check permission
            $this->authorize('edit', $model);

            $this->orderTree($itemsOrder, null, $model);

            return json_response_with_success(200, __('voyager-extension::bread.record_updated'));

        } catch (Exception $e) {
            return json_response_with_error(500, $e);
        }

    }

    private function orderTree(array $children, $parentId, $model)
    {
        foreach ($children as $index => $child) {
            $item = $model->findOrFail($child->id);
            $item->order = $index + 1;
            $item->parent_id = $parentId;
            $item->save();

            if (isset($child->children)) {
                $this->orderTree($child->children, $item->id, $model);
            }
        }
    }

    private function getFileName($file)
    {
        $fullName = $file->getClientOriginalName();
        $filename = pathinfo($fullName, PATHINFO_FILENAME);
        $extension = pathinfo($fullName, PATHINFO_EXTENSION);

        return config('voyager-extension.slug_filenames')? Str::slug($filename) . '.' . $extension : $fullName;
    }

}
