<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Http\Request;
use MonstreX\VoyagerExtension\FormFields\AdvImagesGalleryFormField;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use TCG\Voyager\Facades\Voyager;
use Spatie\MediaLibrary\Models\Media;

use MonstreX\VoyagerExtension\ContentTypes\KeyValueJsonContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvImageContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvImagesGalleryContentType;

class VoyagerExtensionBaseController extends VoyagerBaseController
{

    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {
        switch ($row->type) {
            case 'adv_image':
                return (new AdvImageContentType($request, $slug, $row, $options))->handle();
            case 'adv_images_gallery':
                return (new AdvImagesGalleryContentType($request, $slug, $row, $options))->handle();
            case 'key_value_json':
                return (new KeyValueJsonContentType($request, $slug, $row, $options))->handle();
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
            if ($row->type == 'adv_image' && $request->hasFile($row->field)){

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

            } elseif ($row->type == 'adv_images_gallery' && $request->hasFile($row->field)) {

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
            } elseif ($row->type == 'adv_images_gallery') {
                //


            }


        }

        return $result;
    }





    /*
     * Load AJAX Content using Request params
     */
    public function load_content(Request $request)
    {
        $slug = $request->get('slug');
        $field = $request->get('field');
        $id = $request->get('id');
        $image_id = $request->get('image_id');

        // Load related BREAD Data
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $dataRow = $dataType->editRows->filter(function($item) use ($field) {
            return $item->field == $field;
        })->first();

        // Load Related Media
        $model = app($dataType->model_name);
        $data = $model->findOrFail($id);
        $image = $data->getMedia($field)->where('id', $image_id)->first();

        return view('voyager-extension::forms.form-ajax', [
            'dataRow'      => $dataRow,
            'data'         => $data,
            'image'        => $image,
            'model'        => [
                'model'     => $dataType->model_name,
                'id'       => $id,
                'field'    => $field,
                'image_id' => $image_id,
            ]
        ]);
    }


    /*
     *  Remove media files
     */
    public function update_media(Request $request)
    {
        $model_class = $request->get('model');
        $id = $request->get('id');
        $field = $request->get('field');
        $image_id = $request->get('image_id');

        try {
            // Load the related Record associated with a medialibrary image
            $model = app($model_class);
            $data = $model->find($id);
            $image = $data->getMedia($field)->where('id', $image_id)->first();

            $customFields = $request->except(['model', 'id', 'field', 'image_id']);
            foreach ($customFields as $key => $field) {
                $image->setCustomProperty($key, $field);
            }
            $image->save();

        } catch (Exception $error) {

            return $this->jsonResponseWithError(500, $error);
        }

        return $this->jsonResponseWithSuccess(200, __('voyager-extension::bread.images_updated'));
    }



    /*
     *  Remove media files
     */
    public function remove_media(Request $request)
    {

        $type = $request->get('type');
        $model = $request->get('model');
        $id = $request->get('id');
        $image_id = $request->get('image_id');

        try {
            // Imagelibrary types
            if ($type === 'adv_image' || $type === 'adv_images_gallery') {

                // Load the related Record associated with a medialibrary image
                $model = app($model);
                $data = $model::find([$id])->first();
                $data->deleteMedia($image_id);

            } else {
                return VoyagerBaseController::remove_media($request);
            }

        } catch (Exception $error) {
            return $this->jsonResponseWithError(500, $error);
        }

        return $this->jsonResponseWithSuccess(200, __('voyager::media.file_removed'));
    }

    /*
     * Sort Media files
     */
    public function sort_media(Request $request)
    {
        $images_ids_order = $request->get('images_ids_order');
        try {
            Media::setNewOrder($images_ids_order);
            return $this->jsonResponseWithSuccess(200, __('voyager-extension::bread.images_sorted'));
        } catch (Exception $error) {
            return $this->jsonResponseWithError(500, $error);
        }
    }


    /*
     *  Return JSON response with Success Code
     */
    private function jsonResponseWithSuccess($status, $message)
    {
        return response()->json([
            'data' => [
                'status' => $status,
                'message' => $message,
            ],
        ]);
    }


    /*
     *  Return JSON response with Error Code
     */
    private function jsonResponseWithError($status, Exception $error)
    {
        $code = $status;

        $message = __('voyager::generic.internal_error');

        if ($error->getCode()) {
            $code = $error->getCode();
        }

        if ($error->getMessage()) {
            $message = $error->getMessage();
        }

        return response()->json([
            'data' => [
                'status' => $code,
                'message' => $message,
            ],
        ], $code);
    }

}
