define(['jquery'], function ($) {
    console.log('jquery.mobile-config');
    $(document).on('mobileinit', function () {
        console.log('mobileinit');
        $.mobile.autoInitializePage = false;
    });
});