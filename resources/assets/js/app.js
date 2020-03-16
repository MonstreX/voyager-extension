// Vendor JS code
require('zebra_dialog');

import CodeMirror from 'codemirror';
window.CodeMirror = CodeMirror;

require('codemirror/mode/xml/xml');
require('codemirror/mode/javascript/javascript');
require('codemirror/mode/css/css');
require('codemirror/mode/htmlmixed/htmlmixed');

import Sortable from 'sortablejs';
window.Sortable = Sortable;

// Local JS code
window.vext_helpers = require('./helpers.js');

window.vext = require('./common.js');

// Fields Specific
require('./adv_image.js');
require('./adv_media_files.js');
require('./voyager_legacy.js');

$('document').ready(function () {


});