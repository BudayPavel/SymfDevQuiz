(function($){
    jQuery.fn.ajaxgrid = function(options){

        options =$.extend({
            url:"",
        },options);

        console.log(options);

        let getTable = function(){
            $.ajax({
                url: options.url,
                type: "POST",
                data: "",
                dataType: "json",
                cache: false,
                obj: this,
                success: function (response) {
                    let users = [];
                    users.push(["№", "Email", "Name", "LastName", "Role", "Active"]);
                    response.forEach(function (item, i, response) {
                        users.push([i, response[i]['email'], response[i]['firstName'], response[i]['lastName'], response[i]['roles'], response[i]['active']]);
                    });

                    let table = document.createElement("TABLE");
                    table.border = "1";
                    table.id = "tbl";

                    let columnCount = users[0].length;

                    let row = table.insertRow(-1);
                    for (let i = 0; i < columnCount; i++) {
                        let headerCell = document.createElement("TH");
                        headerCell.innerHTML = users[0][i];
                        row.appendChild(headerCell);
                    }

                    for (let i = 1; i < users.length; i++) {
                        row = table.insertRow(-1);
                        for (let j = 0; j < columnCount; j++) {
                            let cell = row.insertCell(-1);
                            cell.innerHTML = users[i][j];
                        }
                    }

                    this['obj'].innerHTML = "";
                    this['obj'].appendChild(table);
                },
                error: function () {
                    alert('error');
                }
            });
        };

        return this.each(getTable);
    };
})(jQuery);


$(document).ready(function () {
 $("#entities-grid").ajaxgrid({'url':'http:/mysite.dev/ajax/users'});
});
