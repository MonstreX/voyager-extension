<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\Flysystem\Util;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Facades\Voyager;
use Spatie\MediaLibrary\Models\Media;


class VoyagerExtensionController extends VoyagerBaseController
{

    /*
     * Load AJAX Content (HTML rendered) using Request params
     */
    public function load_image_form(Request $request)
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

            return json_response_with_error(500, $error);
        }

        return json_response_with_success(200, __('voyager-extension::bread.images_updated'));
    }


    /*
     * Sort Media files
     */
    public function sort_media(Request $request)
    {
        \Debugbar::info($request);

        $images_ids_order = $request->get('images_ids_order');
        try {
            Media::setNewOrder($images_ids_order);
            return json_response_with_success(200, __('voyager-extension::bread.images_sorted'));
        } catch (Exception $error) {
            return json_response_with_error(500, $error);
        }
    }


    public function assets(Request $request)
    {

        try {
            $path = dirname(__DIR__, 3).'/voyager-extension/publishable/assets/'.Util::normalizeRelativePath(urldecode($request->path));
        } catch (\LogicException $e) {
            abort(404);
        }

        if (File::exists($path)) {
            $mime = '';
            if (Str::endsWith($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (Str::endsWith($path, '.css')) {
                $mime = 'text/css';
            } else {
                $mime = File::mimeType($path);
            }
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('', 404);
    }

}
