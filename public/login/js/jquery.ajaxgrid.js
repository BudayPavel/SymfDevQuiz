(function($){
    $.fn.ajaxgrid = function(options){

        options =$.extend({
            url:"",
            filterableFields:null,
            sortableFields:null
        },options);


        //----------------------------строка поиска
        let _tools = () => {
            let search = document.createElement("input");
            search.type = "text";
            search.setAttribute("class", "search");
            search.id = "search";
            this.append(search);

            let rowscount = document.createElement("select");
            rowscount.id = "rows_per_page";
            let option1 = document.createElement("option");
            option1.text = 10;
            let option2 = document.createElement("option");
            option2.text = 25;
            let option3 = document.createElement("option");
            option3.text = 50;

            rowscount.append(option1);
            rowscount.append(option2);
            rowscount.append(option3);

            this.append(rowscount);

            $('#search').keyup(function () {
                getTBody(1);
            });

            $('#rows_per_page').change(function () {
                getTBody(1);
            })
        };

        let getTable = () => {
            let table = document.createElement("TABLE");
            table.border = "1";
            table.id = "tbl";
            this.append(table);
        };


        //--------------------------Получение таблицы
        let getHeader = () => {
            $.ajax({
                url: options.url,
                type: "POST",
                data: "",
                dataType: "json",
                cache: false,
                success: function (response) {
                    let theader = [];

                    for (let key in response['rows'][0]) {
                        theader.push(key);
                        for (let obj of response['rows']) {
                            if (obj[key] === null) {
                                theader.pop();
                                break;
                            }
                        }
                    }

                    let columnCount = theader.length;

                    let row = document.createElement('thead');
                    for (let i = 0; i < columnCount; i++) {
                        let headerCell = document.createElement("TH");
                        headerCell.id = "header";
                        headerCell.classList.add('notselected');
                        headerCell.innerHTML = theader[i];
                        row.appendChild(headerCell);
                    }

                    $('#tbl').append(row);

                    $('th').click(function () {

                        if (this.classList.contains('selected')) {
                            if (this.classList.contains('ASC')) {
                                this.classList.remove('ASC');
                                this.classList.add('DESC');
                            } else {
                                this.classList.remove('DESC');
                                this.classList.add('ASC');
                            }
                        } else {
                            $('th').removeClass('selected ASC DESC');
                            $('th').addClass('notselected');
                            this.classList.remove('notselected');
                            this.classList.add('selected');
                            this.classList.add('ASC');
                        }

                        getTBody(1);
                    });
                },
                error: function (response) {
                    console.log(response);
                    alert('error');
                }
            });
        };

        let getTBody = (page) => {

            let data = {};
            data['searchPhrase'] = $('#search').val();
            data['current'] = page;
            data['rowCount'] = $('#rows_per_page').val();
            data['searchableFields'] = options.filterableFields;
            data['orderField'] = $('th.selected').html();
            if ($('th.selected').hasClass('ASC')){
                data['order'] = 'ASC';
            } else {
                data['order'] = 'DESC';
            }

            $.ajax({
                url: options.url,
                type: "POST",
                data: data,
                dataType: "json",
                cache: false,
                success: function (response) {
                    let tbody = [];

                    for (let key in response['rows'][0]) {
                        let i = 0;
                        for (let obj of response['rows']) {
                            if (obj[key] === null) {
                                break;
                            }
                            if (tbody[i] === undefined) {
                                tbody.push([]);
                            }
                            tbody[i].push(obj[key]);
                            i++;
                        }
                    }

                    let tblbody = document.createElement('tbody');
                    tblbody.id = 'table-data';

                    columnCount = tbody[0].length;

                    for (let i = 0; i < tbody.length; i++) {
                        row = document.createElement('tr');
                        for (let j = 0; j < columnCount; j++) {
                            let cell = row.insertCell(-1);
                            cell.innerHTML = tbody[i][j];
                            row.appendChild(cell);
                        }
                        tblbody.appendChild(row);
                    }

                    $('#table-data').remove();
                    $('#pages').remove();
                    $('#tbl').append(tblbody);

                    _pagination(data['rowCount'], page, response['total']);

                },
                error: function (response) {
                    console.log(response);
                    alert('error');
                }
            });
        };

        let _pagination = (row_per_page, current, count) => {
            let pages = document.createElement('div');
            pages.id = "pages";
            pages.setAttribute("class", "pages");

            if (current != 1) {
                let prev = document.createElement('a');
                prev.id = "prev";
                prev.href = "";
                prev.text = current-1;
                prev.setAttribute("class","page");
                pages.appendChild(prev);
            }
            let cur = document.createElement('a');
            cur.id = "cur";
            cur.text = current;
            pages.appendChild(cur);
            cur.setAttribute('class','cur_page');
            if (row_per_page*current<count) {
                let next = document.createElement('a');
                next.id = "next";
                next.text = Number(current) + 1;
                next.href = "";
                next.setAttribute('class', 'page');
                pages.appendChild(next);
            }
            let all = document.createElement('a');
            all.id = "all";
            all.text = "Showing "+(current*row_per_page-row_per_page+1)+" to "+((current*row_per_page > count) ? (count) : (current*row_per_page)) + " of " + count +" entries";


            pages.appendChild(all);
            this.append(pages);

            $('#prev').click(function (e) {
                e.preventDefault();
                getTBody(this.text);
            });

            $('#next').click(function (e) {
                e.preventDefault();
                getTBody(this.text);
            });

        };

        _tools();
        getTable();
        getHeader();
        getTBody(1);
    };
})(jQuery);


$(document).ready(function () {
 $("#entities-grid").ajaxgrid({
     'url':'http://quiz.dev/ajax/users',
     'filterableFields':['email','firstName','lastName'],
     'sorableFields':['id','email','firstName','lastName']
 });
});
