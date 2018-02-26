(function($){
    $.fn.ajaxgrid = function(options){

        options =$.extend({
            url:"",
            table: "",
            showFields:['id'],
            filterableFields:null,
            sortableFields:null,
            mode: 0
        },options);

        let _tools = () => {
                this.append($(
                    '<div class="table-responsive">' +
                    '<div class="dataTables_wrapper container-fluid dt-bootstrap4">' +
                    '<div class="row">' +
                    ' <div class="col-sm-12 col-md-2">' +
                    ' <div class="dataTables_length">' +
                    ' <label style="color:#fff; font-weight: normal; margin-top: 2.4px; max-width: 200; width: 200px; margin-left: -2px;">Show <select id="rows_per_page" class="form-control form-control-sm" style="display: inline-block; width: 65px; margin: 0 5px;">' +
                    ' <option selected="selected">10</option>' +
                    ' <option>25</option>' +
                    ' <option>50</option>' +
                    ' </select> entries</label>' +
                    ' </div>' +
                    ' </div>' +
                    ' <div class="col-sm-12 col-md-1"><label style="color:#fff; margin-left:170px; margin-top:8; font-weight: normal;">Search:</label></div>' +
                    ' <div class="col-sm-12 col-md-7">' +
                    ' <input id="search" class="searchBlock__input text-box ingle-line" style="margin-left: 157px; width: 80%;" name="search" type="text">' +
                    ' </div>' +
                    ' <div class="col-sm-12 col-md-1">' +
                    ' <button type="button" id="addbtn" class="btn" style="color: #fff; background-color: #17a2b8; border-color: #17a2b8; padding: 7.3px 30px 7.3px 30px; margin-top: 1px; margin-left: 40px;">Add</button>' +
                    ' </div>' +
                    ' </div>' +
                    '</div>' +
                    '</div>' +
                    '<br>'


                ));

            if (options.mode != 0) {
                $("#addbtn").remove();
            }

            this.find('#rows_per_page').change(() => {
                _body(1);
            });

            this.find('#addbtn').click((e) => {
                e.preventDefault();
                switch (options.table) {
                    case 'user':
                        addUserForm('add');
                        break;
                    case 'quiz':
                        addQuizForm('add');
                        break;
                    case 'question':
                        addQuestionForm('add');
                        break;
                }
                this.find('#userForm').modal();
            });

            this.find('#search').keyup(() => {
                _body(1);
            });
        };


        let _table = () => {
            this.append($(
                '<div class="dataTable_length">' +
                    '<table class="table table-inverse style_table" id="tbl" role="grid" aria-describedby="dataTable_info" width="100%" cellspacing="0"></table>' +
                '</div>'
            ))
        };

        function contains(arr, elem) {
            return arr.find((i) => i === elem) != undefined;
        }

        let addQuestionForm = (action, backhtml = null) => {
            $('.modal-body').empty().append($('<h4 align="center">Question</h4>' +
                '<label id="formError"></label>' +
                '<form id="questiondata">' +
                '<input name="id" id="question_id" type="text" hidden/>' +
                '<h6>Name:</h6><input type="text" name="question[name]" id="question_name" class="form-control"/>' +
                '<h6>Answers:</h6><table  class="table table-bordered table-striped table-sm" name="question[answers]" id="question_answers">' +
                '<thead class="thead-dark">' +
                '<th>Text</th>' +
                '<th>Correct</th>' +
                '<th></th>' +
                '</thead>' +
                '<tbody id="ansBody">' +
                '</tbody>' +
                '</table>' +
                '<h6>New answer:</h6>' +
                '<div class="input-group">'+
			    '<input type="text" name="question[ans]" id="question_ans" class="form-control" placeholder="Answer text">'+
			    '<span class="input-group-btn">'+
			    '<button class="btn btn-default" type="button" id="ansAdd">Add</button>'+
			    '</span>'+
			    '</div><br>'+	
                '<div align="right">' +
                '<button type="button" class="btn btn-secondary exit" data-dismiss="modal">Close</button>' +
                '<button type="submit" class="btn btn-primary save">Done</button>' +
                '</div>' +
                '</form>'
            ));

            $('button.save').prop('id',action);

            $('#ansAdd').click(function (e) {
                e.preventDefault();
                $('#ansBody').append($('<tr><td>'+$('#question_ans').val()+'</td>' +
                    '<td><input type="radio" name="correct"></td>' +
                    '<td><button id="deleteRow">X</button></td></tr>'));
                $('#question_ans').val("");

                $('button#deleteRow').click(function (e) {
                    e.preventDefault();
                    this.parentElement.parentElement.remove();
                });

            });

            $('#userForm').on('hidden.bs.modal',function () {
                if (backhtml === null) {
                _body(1);
                } else {
                    $('.modal-body').empty();
                    $('.modal-body').html(backhtml);
                    backhtml = null;
                    $('#newQuestion').click(function (e) {
                        e.preventDefault();
                        addQuestionForm('add', $('.modal-body').html());
                    });
                    $('button#deleteRow').click(function (e) {
                        e.preventDefault();
                        this.parentElement.parentElement.remove();
                    });
                    $('#inputSearch').keyup(function () {
                        getQuestions();
                    });

                    $('button.save').click(function (e) {
                        e.preventDefault();
                        let send = {};
                        send['text'] = $('#quiz_name').val();
                        send['questions']={};
                        let i = 0;
                        $('form#quizdata tbody tr').each(function () {
                            send['questions'][i] = this.cells[0].innerText;
                            i++
                        });

                        let link;
                        if (this.id === 'add') {
                            link = "http://quiz.dev/ajax/quiz/add";
                        } else {
                            send['id'] = $('#quiz_id').val();
                            send['active'] = $('#quiz_active').is(':checked');
                            link = "http://quiz.dev/ajax/quiz/update";
                        }

                        $.ajax({
                            url: link,
                            type: "POST",
                            data: send,
                            dataType: "json",
                            cache: false,
                            success: function(response){
                                $('#userForm').modal('toggle');
                                _body(1);
                            },
                            error: function (response) {
                                $('#formError').text(response.responseJSON['errorMes']);
                            }
                        });
                    });

                    getQuestions();

                    $('#userForm').modal('show');
                }

            });

            $('button.save').click(function (e) {
                e.preventDefault();
                let send = {};
                send['text'] = $('#question_name').val();
                send['answers']={};
                let i = 0;
                $('form#questiondata tbody tr').each(function () {
                    send['answers'][i] = {};
                    send['answers'][i]['text'] = this.cells[0].innerText;
                    send['answers'][i]['correct'] = $(this.cells[1].firstChild).prop('checked');
                    i++
                });

                let link;
                if (this.id === 'add') {
                    link = "http://quiz.dev/ajax/question/add";
                } else {
                    send['id'] = $('#question_id').val();
                    link = "http://quiz.dev/ajax/question/update";
                }
                $.ajax({
                    url: link,
                    type: "POST",
                    data: send,
                    dataType: "json",
                    cache: false,
                    success: function() {
                        $('#userForm').modal('hide');
                    },
                    error: function(response) {
                        $('#formError').text(response.responseJSON['errorMes']);
                    }
                });

            });


        };

        let getQuestions = () => {
            $.ajax({
                url: "http://quiz.dev/ajax/question/get",
                type: "POST",
                data: {'search':$('#inputSearch').val()},
                dataType: "json",
                cache: false,
                success: function(response){
                    $('#variants').empty();
                    for (let q of response) {
                        $('#variants').append('<li class="dropdown-item" value="'+q['id']+'">'+q['text']+'</li>');
                    }

                    $('.dropdown-item').click(function () {
                        $('#quesBody').append($('<tr><td>'+this.getAttribute('value')+'</td><td>'+this.innerHTML+'</td>' +
                            '<td><button id="deleteRow">X</button></td></tr>'));

                        $('button#deleteRow').click(function (e) {
                            e.preventDefault();
                            this.parentElement.parentElement.remove();
                        });

                        $('#inputSearch').val("");
                        getQuestions();
                    });

                },
                error: function (response) {
                }
            })
        };

        let addQuizForm = (action) => {
            $('.modal-body').empty().append($('<h4 align="center">Quiz</h4>' +
                '<label id="formError"></label>' +
                '<form id="quizdata">' +
                '<input name="id" id="quiz_id" type="text" hidden/>' +
                '<h6>Name:</h6><input type="text" name="quiz[name]" id="quiz_name" class="form-control"/>' +
                '<div id="quiz_a"><input type="checkbox" name="quiz[active]" id="quiz_active">Active</div>' +
                '<div class="row"><div class="col-md-10">Questions:</h6></div><div class="col-md-2"><button class="btn ButtonStyle" id="newQuestion">New</button></div></div>' +
                '<table  class="table table-bordered table-striped table-sm" name="quiz[questions]" id="quiz_questions">' +
                '<thead class="thead-dark">' +
                '<th>Id</th>' +
                '<th>Text</th>' +
                '<th></th>' +
                '</thead>' +
                '<tbody id="quesBody">' +
                '</tbody>' +
                '</table>' +
                '<style>'+
                '.ButtonStyle{'+
                'color: #fff; '+
                'background-color: #17a2b8; '+
                'border-color: #17a2b8; '+
                'padding: 7px 20px 7px 20px; '+
                'margin-top: 1px;'+
                '}'+
                '.ButtonStyle:hover{'+
                'color: #fff; '+
                'background-color: #1f8898; '+
                'border-color: #3c93a0; '+
                'padding: 7px 20px 7px 20px; '+
                'margin-top: 1px;'+
                '}'+
                '</style>'+
                '<h6>New question:</h6>' +
                '<div class="">' +
                '<input id="inputSearch" type = "text" class="form-control" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                '<div class="dropdown-menu" id="variants" style="top:auto; left:auto">' +
                '</div><br>' +
                '</div>' +
                '<div align="right">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                '<button type="submit" class="btn btn-primary save">Done</button>' +
                '</div>' +
                '</div>'
            ));

            $('#newQuestion').click(function (e) {
                e.preventDefault();
                addQuestionForm('add', $('.modal-body').html());
            });

            $('button.save').prop('id',action);
            if (action === 'add') $('#quiz_a').remove();

            $('#inputSearch').keyup(function () {
               getQuestions();
            });

            $('button.save').click(function (e) {
                e.preventDefault();
                let send = {};
                send['text'] = $('#quiz_name').val();
                send['questions']={};
                let i = 0;
                $('form#quizdata tbody tr').each(function () {
                    send['questions'][i] = this.cells[0].innerText;
                    i++
                });

                let link;
                if (this.id === 'add') {
                    link = "http://quiz.dev/ajax/quiz/add";
                } else {
                    send['id'] = $('#quiz_id').val();
                    send['active'] = $('#quiz_active').is(':checked');
                    link = "http://quiz.dev/ajax/quiz/update";
                }

                $.ajax({
                    url: link,
                    type: "POST",
                    data: send,
                    dataType: "json",
                    cache: false,
                    success: function(){
                        $('#userForm').modal('toggle');
                         _body(1);
                    },
                    error: function (response) {
                        $('#formError').text(response.responseJSON['errorMes']);
                    }
                });
            });

            getQuestions();
        };

        let addUserForm = (action) => {
            $('.modal-body').empty().append($('<h4 align="center">User</h4>' +
                '<label id="formError"></label>' +
                '<form id="userdata">' +
                '<input name="id" id="user_id" type="text" hidden/>' +
                '<h6>Email:</h6><input type="email" name="user[email]" id="user_email" class="form-control"/>' +
                '<h6>First name:</h6><input type="text" name="user[firstName]" id="user_firstName" class="form-control"/>' +
                '<h6>Last name:</h6><input type="text" name="user[lastName]" id="user_lastName" class="form-control"/>' +
                '<h6 id="tmp">Password:</h6><input type="text" name="user[plainPassword][first]" id="user_plainPassword_first" class="form-control"/>' +
                '<h6 id="tmp">Repeat password:</h6><input type="text" name="user[plainPassword][second]" id="user_plainPassword_second" class="form-control"/>' +
                '<br><select name="user[role]" id="user_role" class="form-control">' +
                '<option>ROLE_USER</option>' +
                '<option>ROLE_ADMIN</option>' +
                '</select><br>' +
                '<input type="checkbox" name="user[active]" id="user_active">Activated<br>' +
                '<div align="right">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                '<button type="submit" class="btn btn-primary save">Done</button>' +
                '</div>' +
                '</form>'
            ));
            $('button.save').prop('id',action);

            $('button.save').click(function (e) {
                e.preventDefault();
                let formData = $('#userdata').serialize();
                let link;
                if (this.id === 'add') {
                    link = "http://quiz.dev/ajax/users/add";
                } else {
                    link = "http://quiz.dev/ajax/users/update";
                }
                $.ajax({
                    url: link,
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    cache: false,
                    success: function(){
                        $('#userForm').modal('toggle');
                        _body(1);
                    },
                    error: function (response) {
                        $('#formError').text(response.responseJSON['errorMes']);
                    }
                });

            });
        };

        let _header = () => {
            let data = {};
            data['rowCount'] = 1;
            data['current'] = 1;
            $.ajax({
                url: options.url,
                type: "POST",
                data: data,
                dataType: "json",
                cache: false,
                obj:this,
                success: function (response) {
                    let row = document.createElement('thead');
                    row.classList.add('thead-dark');

                    for (let key in response['rows'][0]) {
                        if (contains(options['showFields'], key)) {
                            let headerCell = document.createElement("TH");
                            headerCell.id = "header";
                            headerCell.classList.add('notselected');
                            headerCell.innerHTML = key;
                            row.appendChild(headerCell);
                        }
                    }
                    if (response['rows'].length != 0 && options.mode != 4 ){
                    let headerCell = document.createElement("th");
                    headerCell.innerHTML = 'Commands';
                    headerCell.setAttribute('style', 'width: 150px')
                    row.appendChild(headerCell);
                    }
                    this.obj.find('#tbl').append(row);

                    this.obj.find('th').click(function () {
                        if (options.sortableFields != null && options.sortableFields.findIndex(i => i === this.innerHTML) != -1) {

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
                            _body(1);
                        }
                    });

                },
                error: function () {
                }
            });
        };

        let _body = (page) => {
            let data = {};
            data['searchPhrase'] = this.find('#search').val();
            data['current'] = page;
            switch (options.mode)
            {
                case 0: data['rowCount'] = this.find('#rows_per_page').val();
                    break;
                case 2: data['rowCount'] = 5;
                    break;
                default: data['rowCount'] = 10;
                    break;
            }

            data['searchableFields'] = options.filterableFields;
            data['orderField'] = this.find('th.selected').html();
            if (this.find('th.selected').hasClass('ASC')){
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
                obj: this,
                success: function (response) {
                     this.obj.find('#table-data').remove();
                     this.obj.find('#pages').remove();
                     if (options.mode != 4)
                    _pagination(data['rowCount'], page, response['total']);
                    if (response['rows'].length === 0) {
                        return;
                    }
                    let tbody = [];

                    let keys = [];
                    for (let obj of response['rows']) {
                        keys.push(obj['id']);
                    }

                    for (let key in response['rows'][0]) {
                        if (contains(options['showFields'],key)) {
                            let j = 0;
                            for (let obj of response['rows']) {
                                if (tbody[j] === undefined) {
                                    tbody.push([]);
                                }
                                tbody[j].push(obj[key]);
                                j++;
                            }
                        }
                    }

                    let tblbody = document.createElement('tbody');
                    tblbody.id = 'table-data';

                    columnCount = tbody[0].length;

                    for (let i = 0; i < tbody.length; i++) {
                        let row = document.createElement('tr');
                        for (let j = 0; j < columnCount; j++) {
                            let cell = row.insertCell(-1);
                            cell.innerHTML = tbody[i][j];
                        }

                        let cell = row.insertCell(-1);
                        switch (options.mode){
                            case 0:
                                $(cell).append($(
                                '<button id="edit" class="btn btn-warning edit" style="width: 60px">Edit</button>' +
                                '<button id="delete" class="btn btn-danger delete" style="margin-left: 10px; width: 60px">Delete</button>'
                                )
                                );
                                break;
                            case 1: $(cell).append('<a style="width:90px" class="btn btn-success" href="/play?quiz='+keys[i]+'">Play</a>');
                                break;
                            case 2: $(cell).append('<a style="width:90px" class="btn btn-warning" href="/play?quiz='+keys[i]+'">Continue</a>');
                                break;
                            case 3: $(cell).append('<a style="width:90px" class="btn btn-warning" href="/replay/'+keys[i]+'">Replay</a>');
                                break;
                        }
                        tblbody.appendChild(row);
                    }

                    this.obj.find('#tbl').append(tblbody);

                    this.obj.find('button.delete').click(function (e) {
                        if (confirm("Delete record?")) {
                            let param = {
                                        'id': this.parentElement.parentElement.children[0].innerHTML
                                    };
                                    $.ajax({
                                        url: options.url + '/delete',
                                        type: "POST",
                                        data: param,
                                        dataType: "json",
                                        cache: false,
                                        success: function () {
                                            _body(page);
                                        },
                                        error: function () {
                                            alert('Delete error');
                                        }
                                    });
                            }
                    });

                    this.obj.find('button.edit').click(function (e) {
                        e.preventDefault();
                        let row = this.parentElement.parentElement.childNodes;
                        switch (options.table) {
                            case 'user':
                            addUserForm('edit');
                                $('#user_plainPassword_first').remove();
                                $('#user_plainPassword_second').remove();
                                $('h6#tmp').remove();
                                $('#userForm').modal();
                                $('#user_id').val(row[0].innerHTML);
                                $('#user_email').val(row[1].innerHTML);
                                $('#user_firstName').val(row[2].innerHTML);
                                $('#user_lastName').val(row[3].innerHTML);
                                $('#user_role').val(row[4].innerHTML);
                                $('#user_active').prop('checked', JSON.parse(row[5].innerHTML));
                            break;
                            case 'quiz':
                                addQuizForm('edit');
                                $('#quiz_id').val(row[0].innerHTML);
                                $('#quiz_name').val(row[1].innerHTML);
                                $('#quiz_active').prop('checked',row[2].innerHTML === 'true'?true:false);
                                $.ajax({
                                    url: options.url + '/sub',
                                    type: "POST",
                                    data: {'id':row[0].innerHTML},
                                    dataType: "json",
                                    cache: false,
                                    success: function (response) {
                                        for (let a of response) {
                                            let tr = document.createElement('tr');
                                            let td1 = document.createElement('td');
                                            td1.innerHTML = a['id'];
                                            let td2 = document.createElement('td');
                                            td2.innerHTML = a['text'];
                                            let td3 = document.createElement('td');
                                            let btn = document.createElement('button');
                                            btn.id = "deleteRow";
                                            btn.innerHTML = 'X';

                                            tr.appendChild(td1);
                                            tr.appendChild(td2);
                                            td3.appendChild(btn);
                                            tr.appendChild(td3);
                                            $('#quesBody').append(tr);

                                            $('button#deleteRow').click(function (e) {
                                                e.preventDefault();
                                                this.parentElement.parentElement.remove();
                                            });
                                        }
                                        $('#userForm').modal();
                                    },
                                    error: function (response) {
                                    }
                                });
                                break;
                            case 'question':
                                addQuestionForm('edit');
                                $('#question_id').val(row[0].innerHTML);
                                $('#question_name').val(row[1].innerHTML);
                                $.ajax({
                                    url: options.url + '/sub',
                                    type: "POST",
                                    data: {'id':row[0].innerHTML},
                                    dataType: "json",
                                    cache: false,
                                    success: function (response) {
                                        for (let a of response) {
                                            let tr = document.createElement('tr');
                                            let td1 = document.createElement('td');
                                            td1.innerHTML = a['text'];
                                            let td2 = document.createElement('td');
                                            let radio = document.createElement('input');
                                            radio.type = 'radio';
                                            radio.name = 'correct';
                                            if (a['right']) {
                                                radio.checked = true;
                                            }
                                            let td3 = document.createElement('td');
                                            let btn = document.createElement('button');
                                            btn.id = "deleteRow";
                                            btn.innerHTML = 'X';

                                            tr.appendChild(td1);
                                            td2.appendChild(radio);
                                            tr.appendChild(td2);
                                            td3.appendChild(btn);
                                            tr.appendChild(td3);
                                            $('#ansBody').append(tr);

                                            $('button#deleteRow').click(function (e) {
                                                e.preventDefault();
                                                this.parentElement.parentElement.remove();
                                            });
                                        }
                                        $('#userForm').modal();
                                    },
                                    error: function (response) {
                                    }
                                });
                                break;
                        }
                    });

                },
                error: function (response) {
                }
            });
        };

        let _pagination = (row_per_page, current, count) => {
            this.append($(
                '<div id ="pages" class="dataTables_wrapper container-fluid dt-bootstrap4">\n' +
                '   <div class="row">\n' +
                '       <div class="col-sm-12 col-md-5">\n' +
                '           <div align="left"><p id="all" style="color: #fff; font-size: 12pt; text-decoration: none;">Showing ' + (current * row_per_page - row_per_page + 1) + ' to ' + ((current * row_per_page > count) ? (count) : (current * row_per_page)) + ' of ' + count + ' entries</p></div>\n' +
                '       </div>\n' +
                '       <div class="col-sm-12 col-md-7">\n' +
                '           <div class="dataTables_paginate paging_simple_numbers" align="right">\n' +
                '               <ul class="pagination">\n' +
                '               </ul>\n' +
                '           </div>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '</div>'
            ));

            if (count != 0) {
                if (Number(current) > Number(1)) {
                    let lprev = $('<li class="paginate_button page-item"></li>');
                    let link = $('<a aria-controls="dataTable" data-dt-idx="1" tabindex="0" class="page-link" id="prev">'+(Number(current)-Number(1))+'</a>');
                    lprev.append(link);
                    this.find('ul.pagination').append(lprev);
                }
                let lcur = $('<li class="paginate_button page-item active"></li>');
                let link = $('<a aria-controls="dataTable" data-dt-idx="1" tabindex="0" class="page-link">'+current+'</a>');
                link.innerHTML = current;
                lcur.append(link);
                this.find('ul.pagination').append(lcur);

                if (row_per_page * current < count) {
                    let lnext = $('<li class="paginate_button page-item"></li>');
                    let link = $('<a aria-controls="dataTable" data-dt-idx="1" tabindex="0" class="page-link" id="next">'+(Number(current)+Number(1))+'</a>');
                    link.innerText = current + 1;
                    lnext.append(link);
                    this.find('ul.pagination').append(lnext);
                }
            }
            else
            {
                this.find('#pages').html("<label>No data found</label>");
            }

            this.find('#prev').click(function (e) {
                e.preventDefault();
                _body(this.text);
            });

            this.find('#next').click(function (e) {
                e.preventDefault();
                _body(this.text);
            });

        };

        this.append('<div class="modal" id="userForm" tabindex="-1" role="dialog" aria-labelledby="user" aria-hidden="true">' +
            '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                    '<div class="modal-body">' +
                    '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');


        if (options.mode ===0) {
            _tools();
        }
        _table();
        _header();
        _body(1);
    };
})(jQuery);
