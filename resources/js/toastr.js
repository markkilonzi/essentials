import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

/**
 * Shared toastr setup: import this once from the app's js, after installing toastr
 * with npm. It makes toastr global, which the package layout calls for flash messages.
 */
window.toastr = toastr;

/**
 * Define toastr options
 */
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: false,
    progressBar: true,
    positionClass: 'toast-top-left',
    preventDuplicates: true,
    showDuration: 300,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};