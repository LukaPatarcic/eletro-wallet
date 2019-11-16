import '../css/app.css';
import $ from 'jquery';
import jQuery from 'jquery';
global.$ = $;
global.jQuery = jQuery;

import 'bootstrap'
import '../../node_modules/mdbootstrap/js/modules/chart'
import '../../node_modules/mdbootstrap/js/modules/forms-free'
import '../../node_modules/mdbootstrap/js/modules/enhanced-modals'
import '../../node_modules/mdbootstrap/js/modules/waves'
import '../../node_modules/mdbootstrap/js/modules/velocity.min'
import 'toastr'
import toastr from "toastr";


toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
