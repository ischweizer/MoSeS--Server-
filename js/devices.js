/**
* API Versions
*/

var API_VERSION = {8: 'API 8: "Froyo" 2.2.x',
                   9: 'API 9: "Gingerbread" 2.3.0 - 2.3.2',
                   10: 'API 10: "Gingerbread" 2.3.3 - 2.3.7',
                   11: 'API 11: "Honeycomb" 3.0',
                   12: 'API 12: "Honeycomb" 3.1',
                   13: 'API 13: "Honeycomb" 3.2.x',
                   14: 'API 14: "Ice Cream Sandwich" 4.0.0 - 4.0.2',
                   15: 'API 15: "Ice Cream Sandwich" 4.0.3 - 4.0.4',
                   16: 'API 16: "Jelly Bean" 4.1.x',
                   17: 'API 17: "Jelly Bean" 4.2.x',
                   18: 'API 18: "Jelly Bean" 4.3'};

/**
* Pagination setup and query
*/
var paging = {
    'pages': <?php echo ((int)(count($USER_DEVICES) / 5)) + 1; ?>,
    'pageMax': 5,
    'curPage': 1
};
$('#page-selection').bootpag({
    total: paging['pages'],
    page: 1,
    maxVisible: paging['pageMax']
    }).on('page', function(event, num){    
        
       // setting current selected page
       paging['curPage'] = num;
       // request json data
       $.ajax({
        dataType: "json",
        url: 'content_provider.php',
        data: paging,
        success: function(result){
            // processing returned data
            var deviceNumber = (((paging['curPage']-1)*paging['pageMax'])+1);
            var replaceRows = '';
            for(var i=0; i<result.length; ++i){
                replaceRows += '<tr>';
                replaceRows += '<td>' + (deviceNumber+i) + '</td>';
                replaceRows += '<td>' + result[i].deviceid + '</td>';
                replaceRows += '<td>' + result[i].modelname + '</td>';
                replaceRows += '<td>' + API_VERSION[result[i].androidversion] + '</td>';
                replaceRows += '<td><a href="devices.php?remove='+ result[i].hwid +'" title="Remove device" class="btn btn-danger">Remove</a></td>';
                replaceRows += '</tr>';
            }
            
            $('#content').html(replaceRows);
        }
       }); 
});