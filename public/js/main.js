$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /**
     * Handle Filter button click
     * */
    $('#filter').on('click', function (e) {
        console.log('clicked');
        $.ajax({
            type: 'get',
            url: '/filter',
            data: $('#filterForm').serializeArray(),
            success: function (res) {
                $('#totalResult').text(res.total);
                $('#filteredResult').text(res.filtered);
            }
        })
    });

    /**
    * Handle Send Messages button click
    * */
    $('#sendToQueue').on('click', function (e) {
        $.ajax({
            type: 'post',
            url: '/send-message',
            data: $('#filterForm').serializeArray(),
            success: function (res) {
                if (res.status === 'success') {
                    $('#queueStatus').text(res.message);
                } else {
                    $('#queueStatus').text(res.message);
                }
            }
        })
    });

    $('#filterForm').on('submit', function(e){
       e.preventDefault();
    });

    /**
     * Refresh queue list every 2 seconds
     * */
    setInterval(function () {
        $.ajax({
            type: 'get',
            url: '/get-message-queues',
            success: function (res) {
                if (res.status === 'success') {
                    var tbody = '';
                    $.each(res.data, function (id, item) {
                        if (item.messages && parseInt(item.messages) > 0){
                            tbody += '<tr>'
                                + '<td>' + item.name + '</td>'
                                + '<td>' + (item.messages ? item.messages : 0) + '</td>'
                                + '</tr>'
                        }
                    });

                    if (tbody === '') {
                        tbody = '<tr><td colspan="2"> No Active jobs</td></tr>';
                    }
                    $('#queuesTable tbody').html(tbody);
                }
            }
        })
    }, 2000)


});