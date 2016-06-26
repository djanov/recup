// $(document).ready(function() {
    console.log('test');
    // console.log('It\'s a Unix system, I know this');
    // $('.footer').prepend('<span>Life finds a way -> </span>');
var timeoutId = 0;

    $('#search').keyup(function() {
        clearTimeout(timeoutId);
        searchText = $(this).val();
        timeoutId = setTimeout(getFilterResult, 500);
    });

    function getFilterResult() {
        // clear search if empty
        if (!searchText.trim()) {
            $('#js-search').html('');
            return;
        }

        $.ajax({
            type: "POST",
            url: '/search',
            dataType: "json",
            data: {searchText : searchText},
            success : function(response)
            {
                var images = '';
                images += '<div style="overflow-y: scroll; height: 100px;">';
                for(var i=0; i<response.length;++i){
                    // var obj = response[i];
                    // var attrName = key;
                    // var attrValue = obj[key];
                    // html = "<img src = '" + obj.webPath + "'>";

                    images += '<img class ="img-circle user-img" src="' + response[i].webPath + '" />';
                    images += '<a href="/user/'+ response[i].username.username + ' ">' + '<p class="label-danger">' + response[i].name + '</p>' + '<a/>';
                }
                images += '</div>';

                // console.log($('#js-search').html(response));
                // console.log(response[0].country);
                console.log(images);
                $('#js-search').html(images);
                
            }
        });
    }

    // $( window ).load(function() {
    //
    // $.ajax({
    //     type: 'GET',
    //     url: '/songs/all',
    //     dataType: 'json',
    //     success: function(data) {
    //         var songs = '';
    //         for(var i=0; i<data.length;++i) {
    //             songs += '<p>' + data[i].about + '</p>';
    //             console.log(songs);
    //         }
    //         $('#js-item').html(songs);
    //         console.log(songs);
    //     }
    //  })
    // });
    $('.js-like-toggle').on('click', function (r) {
        // preventing the browser form "following" the link
        r.preventDefault();

        var $anchor = $(this);
        var url = $(this).attr('href')+'.json';

        $.post(url, null, function(data){
            if(data.like){
                // var message = 'thanks for the like!';
                // $anchor.after('<span class="label label-success">'+message+'</span>');
                // $anchor.hide();
                $anchor.after('<span class="label label-success css-label"> Thanks </span>');
                $anchor.hide();
            } else {
                // var message = 'sorry to see tha';
                $anchor.after('<span class="label label-danger css-label"> :( </span>');
                $anchor.hide();
            }
            // $anchor.after('<span class="label label-default">&#1004; '+message+'</span>');
            $anchor.hide();
        });
    });

// });
