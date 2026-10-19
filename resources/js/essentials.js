/**
 * Install every Nyumbani script at once: import this single file from the app's js. It sets up toasts,
 * the shell, the double-submit guard, the file input, the modal and the formatting helpers. The packages
 * they import (toastr, @alpinejs/ui and pluralize) are listed in the package.json beside this folder and
 * installed by php artisan nyumbani:install.
 */
import './toastr';
import './shell';
import './submit';
import './file-input';
import './modal';
import './helpers';
