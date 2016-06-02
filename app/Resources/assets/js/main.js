$(document).ready(function() {
    console.log('It\'s a Unix system, I know this');
    $('.footer').prepend('<span>Life finds a way -> </span>');

    $('#search').keyup(function() {

        searchText = $(this).val();

        $.ajax({
            type: "POST",
            url: '/search',
            dataType: "json",
            data: {searchText : searchText},
            success : function(response)
            {
                // $('#test').html(data);
                console.log('test')
                // console.log(data);
                console.log(response);
            }
        });
    });
});
