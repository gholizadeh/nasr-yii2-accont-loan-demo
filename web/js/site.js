$(document).ready(function () {
    $('.sidebarCollapse').on('click', function () {
        $('#sidebar, #content').toggleClass('active');
    });

    $('.seg-part').click(function (e) {
        e.stopPropagation();
    });
});