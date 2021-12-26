<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/*
 * Get Translation from the given string using locale code
 * @param: {{en}}text in english{{ru}}text in russian
 * @return: text in current or given locale
 */
if (!function_exists('str_trans')) {
    function str_trans(?string $string, $lang = null)
    {
        if (!$lang) {
            $lang = \App::getLocale();
        }

        $open_bracket = '{{';
        $close_bracket = '}}';

        if (strpos($string, '[[') !== false) {
            $open_bracket = '[[';
            $close_bracket = ']]';
        }

        $loc_strings = explode($open_bracket, $string);

        foreach (explode($open_bracket, $string) as $line) {
            if (substr($line, 0, 4) === $lang . $close_bracket) {
                return substr($line, 4, strlen($line) - 4);
            }
        }

        // Lang stings present, but No app locale key has been found
        if (!empty($loc_strings)) {
            return '';
        }

        return !empty($string)? $string : '';
    }
}

if (!function_exists('json_response_with_success')) {
    function json_response_with_success($status, $message, $data = null)
    {
        return response()->json([
            'data' => [
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ],
        ]);
    }
}

/*
 *  Return JSON response with Error Code
 */
if (!function_exists('json_response_with_error')) {
    function json_response_with_error($status, Exception $error)
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

/*
 *  Returns required ASSET file (css, js)
 */
if (!function_exists('voyager_extension_asset')) {
    function voyager_extension_asset($path, $secure = null)
    {
        return route('voyager.voyager_extension_assets').'?path='.urlencode($path);
    }
}


// Makes Multi Level TREE (array) from FLAT array (adds Children elements)
if (!function_exists('flat_to_tree'))
{
    function flat_to_tree($flat_array)
    {
        $result = $flat_array;

        if (count($flat_array) > 0 && array_key_exists('parent_id', $flat_array[0])) {
            $result = build_tree($result);
        }

        return $result;
    }
}

// For flatToTree($flat_array)
if (!function_exists('build_tree'))
{
    function build_tree(array $elements, $parentId = null, $sort = true)
    {
        $branch = array();

        foreach ($elements as $element)
        {
            if ($element['parent_id'] == $parentId) {
                $children = build_tree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        if($sort) {
            $branch = Arr::sort($branch, function ($value) {
                return $value['order'];
            });
        }

        return $branch;
    }
}

// Makes FLAT Sorted Array from Multi level Sorted array TREE (excludes children elements)
if (!function_exists('build_flat_from_tree')) {
    function build_flat_from_tree($tree)
    {
        $result = [];
        $level = 0;

        build_flat_children($tree, $result, $level);

        return $result;
    }
}

if (!function_exists('build_flat_children')) {
    function build_flat_children($children, &$result, &$level)
    {
        foreach ($children as $child) {
            $elements = [];
            foreach ($child as $key => $field) {
                if($key !== 'children') {
                    $elements[$key] = $field;
                    $elements['level'] = $level;
                }
            }
            $result[] = $elements;
            if (isset($child['children'])) {
                $level++;
                build_flat_children($child['children'], $result, $level);
                $level--;
            }
        }
    }
}

if (!function_exists('find_package')) {
    function find_package($package_name)
    {
        $filesystem = app(Filesystem::class);
        $version = null;
        if ($filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode($filesystem->get(base_path('composer.lock')));
            // Loop through all the packages and get the version of voyager
            foreach ($file->packages as $package) {
                if ($package->name == $package_name) {
                    $version = $package->version;
                    break;
                }
            }
        }
        return $version;
    }
}

if (!function_exists('get_row_by_name')) {
    function get_row_by_name($browse_rows, $field_name)
    {
        return $browse_rows->first(function ($value) use ($field_name){
            return $value->field == $field_name;
        });
    }
}

if (!function_exists('get_index_by_name')) {
    function get_index_by_name($browse_fields, $field_name)
    {
        return collect($browse_fields)->search(function($value) use ($field_name) {
            return $value === $field_name;
        });
    }
}

if (!function_exists('set_session_redirect')) {
    function set_session_redirect(Request $request)
    {
        if ($request->has('redirect_to') && !empty($request->redirect_to)) {
            $request->session()->put('redirect_to', $request->redirect_to);
        }
    }
}
