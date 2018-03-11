var $ = require('jquery');

require('../css/app.scss');
require('bootstrap-sass');

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it

// or you can include specific pieces
// require('bootstrap-sass/javascripts/bootstrap/tooltip');
// require('bootstrap-sass/javascripts/bootstrap/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});