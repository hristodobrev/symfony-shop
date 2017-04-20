$(function () {
    console.log('test');

    $('.alert').each(function (ind, el) {
        $(el).find('.close').click(function () {
            $(el).fadeOut();
        });

        setTimeout(function () {
            $(el).fadeOut();
        }, 3000);
    });
});