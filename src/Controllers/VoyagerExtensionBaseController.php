<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use TCG\Voyager\Facades\Voyager;

use MonstreX\VoyagerExtension\ContentTypes\KeyValueJsonContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvImageContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvMediaFilesContentType;

class VoyagerExtensionBaseController extends VoyagerBaseController
{

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
                        ->toMediaCollection($row->field);
                }
            }

        }

        return $result;
    }

    //***************************************
    //
    //         Clone an item BREA(D)
    //
    //****************************************

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
                $model = app('\TCG\Voyager\Models\MenuItem');
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


}
