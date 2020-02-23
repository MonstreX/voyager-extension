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
            return json_response_with_error(500, $error);
        }

        return json_response_with_success(200, __('voyager::media.file_removed'));
    }


}
