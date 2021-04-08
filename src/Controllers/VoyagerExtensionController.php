<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\Flysystem\Util;
use TCG\Voyager\Facades\Voyager;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Illuminate\Support\Facades\Cache;


class VoyagerExtensionController extends BaseController
{

    /*
     * Load Translations
     */
    public function load_translations(Request $request)
    {
        return response()->json(Cache::get('translations'));
    }


    /*
     * Get Data Type Row Instance and Field Data
     */
    private function getDataType($slug, $id, $field)
    {
        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        // Get field meta data and options
        $dataRow = $dataType->editRows->filter(function ($item) use ($field) {
            return $item->field == $field;
        })->first();

        // Load model and find record
        $model = app($dataType->model_name);
        $data = $model->findOrFail($id);

        // Check if field exists
        if (!isset($data->{$field}) && $data->{$field} !== null) {
            throw new Exception(__('voyager::generic.field_does_not_exist'), 400);
        }

        return [
            'dataType' => $dataType,
            'dataRow' => $dataRow,
            'data' => $data,
        ];
    }

    /*
     * Load AJAX Content (HTML rendered) using Request params
     */
    public function load_image_form(Request $request)
    {
        $slug = $request->get('slug');
        $field = $request->get('field');
        $id = $request->get('id');
        $media_file_id = $request->get('media_file_id');

        $row = $this->getDataType($slug, $id, $field);

        $file = $row['data']->getMedia($field)->where('id', $media_file_id)->first();

        return view('voyager-extension::forms.form-ajax', [
            'dataRow' => $row['dataRow'],
            'data' => $row['data'],
            'file' => $file,
            'model' => [
                'model' => $row['dataType']->model_name,
                'id' => $id,
                'field' => $field,
                'media_file_id' => $media_file_id,
            ]
        ]);
    }

    /*
     *  Get Group Form
     */
    public function load_group_form(Request $request)
    {
        try {

            $slug = $request->get('slug');
            $id = $request->get('id');
            $field = $request->get('field');

            $row = $this->getDataType($slug, $id, $field);

            $group = json_decode($row['data']->{$field});
            if (!isset($group->fields)) {
                $fieldValue = $row['dataRow']->details->fields;
            } else {
                $fieldValue = $group->fields;
            }

            return view('voyager-extension::forms.form-group-ajax', [
                'slug' => $slug,
                'id' => $id,
                'field' => $field,
                'fields' => $fieldValue,
            ]);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     *  Update media file
     */
    public function update_media(Request $request)
    {
        $model_class = $request->get('model');
        $id = $request->get('id');
        $field = $request->get('field');
        $media_file_id = $request->get('media_file_id');

        try {
            // Load the related Record associated with a medialibrary file
            $model = app($model_class);
            $data = $model->find($id);
            $file = $data->getMedia($field)->where('id', $media_file_id)->first();

            $customFields = $request->except(['model', 'id', 'field', 'media_file_id']);
            foreach ($customFields as $key => $field) {
                $file->setCustomProperty($key, $field);
            }
            $file->save();

        } catch (Exception $error) {

            return json_response_with_error(500, $error);
        }

        return json_response_with_success(200, __('voyager-extension::bread.media_updated'));
    }

    /*
     *  Change media file
     */
    public function change_media(Request $request)
    {
        $model_class = $request->get('model');
        $slug = $request->get('slug');
        $id = $request->get('id');
        $field = $request->get('field');
        $media_file_id = $request->get('media_file_id');

        try {

            // Load the related Record associated with a medialibrary file
            $model = app($model_class);
            $data = $model->find($id);

            // Load related BREAD Data
            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
            $dataRow = $dataType->editRows->filter(function ($item) use ($field) {
                return $item->field == $field;
            })->first();

            // Save OLD Properties
            $old_file = $data->getMedia($field)->where('id', $media_file_id)->first();
            $old_properties = [];
            $old_properties['title'] = $old_file->getCustomProperty('title');
            $old_properties['alt'] = $old_file->getCustomProperty('alt');

            if (isset($dataRow->details->extra_fields)) {
                foreach ($dataRow->details->extra_fields as $key => $item) {
                    $old_properties[$key] = $old_file->getCustomProperty($key);
                }
            }

            // Add New Image from Request
            $new_file = $data->addMediaFromRequest($field)
                ->withCustomProperties($old_properties)
                ->setFileName($this->getFileName($request->file($field)))
                ->toMediaCollection($field);

            $all_files = $data->getMedia($field);
            $new_order = [];
            foreach ($all_files as $key => $item) {
                if ($item->id === (int)$media_file_id) {
                    $new_order[] = $new_file->id;
                } else {
                    $new_order[] = $item->id;
                }
            }

            Media::setNewOrder($new_order);

            $old_file->delete();

            $file_name_size = Str::limit($new_file->file_name, 20, ' (...)');
            $file_name_size .= ' <i class="' . ($new_file->size > 100000 ? 'large' : '') . '">' . $new_file->human_readable_size . '</i>';

        } catch (Exception $error) {

            return json_response_with_error(500, $error);
        }

        return json_response_with_success(
            200,
            __('voyager-extension::bread.media_updated'), [
            'file_url' => $new_file->getFullUrl(),
            'file_name' => $new_file->file_name,
            'file_name_size' => $file_name_size,
            'file_id' => $new_file->id,
        ]);
    }


    /*
     *  Remove media file
     */
    public function remove_media(Request $request)
    {

        $media_ids = $request->get('media_ids');

        if (!$media_ids) {
            $media_ids = [$request->get('media_file_id')];
        }

        try {
            Media::destroy($media_ids);
        } catch (Exception $error) {
            return json_response_with_error(500, $error);
        }

        return json_response_with_success(200, __('voyager-extension::bread.media_removed'));
    }


    /*
     * Sort Media files
     */
    public function sort_media(Request $request)
    {

        $files_ids_order = $request->get('files_ids_order');

        try {
            Media::setNewOrder($files_ids_order);
            return json_response_with_success(200, __('voyager-extension::bread.media_sorted'));
        } catch (Exception $error) {
            return json_response_with_error(500, $error);
        }
    }

    /*
     * Return content of the requested asset file (js, css and etc)
     *
     * This function used as is from the original Voyager 1.3.1
     * input: 'http://site.com/admin/voyager-assets?path=js%2Fapp.js'
     */
    public function assets(Request $request)
    {

        try {
            $path = dirname(__DIR__, 3) . '/voyager-extension/publishable/assets/' . Util::normalizeRelativePath(urldecode($request->path));
        } catch (\LogicException $e) {
            abort(404);
        }

        return $this->assets_file($path);
    }

    /*
     * Return content of the requested asset file (js, css and etc)
     *
     * input: 'http://site.com/admin/voyager-extension/path/to/assets/file.*'
     */
    public function assets_regular(Request $request)
    {

        try {
            $path = dirname(__DIR__, 3) . '/voyager-extension/publishable/assets/' . str_replace('/admin/voyager-extension/','',urldecode($request->getPathInfo()));
        } catch (\LogicException $e) {
            abort(404);
        }

        return $this->assets_file($path);
    }

    private function assets_file($path)
    {
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
        abort(404);
    }

    private function getFileName($file)
    {
        $fullName = $file->getClientOriginalName();
        $filename = pathinfo($fullName, PATHINFO_FILENAME);
        $extension = pathinfo($fullName, PATHINFO_EXTENSION);

        return config('voyager-extension.slug_filenames')? Str::slug($filename) . '.' . $extension : $fullName;
    }

}
