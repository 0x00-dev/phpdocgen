$(document).ready(function (){
    $('.spoiler-btn').on('click', function () {
        let target = $(this).data('target');
        let status = $('#' + target).css('display');
        if ('block' === status) {
            $(this).text('+');
            $('#' + target).hide();
        } else {
            $(this).text('-');
            $('#' + target).show();
        }
    });
});