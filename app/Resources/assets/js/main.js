$(document).ready(function() {
    console.log('test');
    // console.log('It\'s a Unix system, I know this');
    // $('.footer').prepend('<span>Life finds a way -> </span>');
    

    $('#search').keyup(function() {

        searchText = $(this).val();

        $.ajax({
            type: "POST",
            url: '/search',
            dataType: "json",
            data: {searchText : searchText},
            success : function(response)
            {
                var images = '';
                for(var i=0; i<response.length;++i){
                    // var obj = response[i];
                    // var attrName = key;
                    // var attrValue = obj[key];
                    // html = "<img src = '" + obj.webPath + "'>";

                    images += '<img class ="img-circle user-img" src="' + response[i].webPath + '" />';
                    images += '<a href="/user/'+ response[i].username.username + ' ">' + '<p class="label-danger">' + response[i].name + '</p>' + '<a/>';
                }
                // console.log($('#test').html(response));
                // console.log(response[0].country);
                console.log(images);
                $('#test').html(images);
                // console.log(data);
                // console.log(response);
            }
        });
    });
});
