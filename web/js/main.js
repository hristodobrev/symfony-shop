$(function () {
    console.log('test');

    $('.alert').each(function (ind, el) {
        $(el).find('.close').click(function () {
            $(el).fadeOut();
        });
    });

    $('.alert:not(.alert-danger)').each(function(ind, el){
        setTimeout(function () {
            $(el).fadeOut();
        }, 3000);
    });
});