(function($){
    $.fn.ajaxgrid = function(options){

        options =$.extend({
            url:"",
            table: "",
            showFields:['id'],
            filterableFields:null,
            sortableFields:null,
            mode: 1
        },options);

        let _tools = () => {
                this.append($(
                    '<div align>' +
                    '<div align="right">' +
                    '<input type="text" id="search">' +
                    '<select id="rows_per_page">' +
                    '<option>10</option>' +
                    '<option>25</option>' +
                    '<option>50</option>' +
                    '</select>' +
                    '<button type="button" id="addbtn" data-toggle="modal" data-target="#userForm" class="btn btn-info">Add</button>' +
                    '</div>' +
                    '</div>'
                ));
            if (options.mode != 1) {
                $("#addbtn").remove();
            }

            $('#search').keyup(function () {
                _body(1);
            });

            $('#rows_per_page').change(function () {
                _body(1);
            })

            $('#addbtn').click(function () {
                switch (options.table)
                {
                    case 'user': addUserForm('add');
                        break;
                    case 'quiz': addQuizForm('add');
                        break;
                    case 'question': addQuestionForm('add');
                        break;
                }
                }
            )
        };


        let _table = () => {
            this.append($(
                '<div class="table-responsive">' +
                    '<table class="table table-sm table-hover" id="tbl"></table>' +
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
                '<div class="input-group mb-3">' +
                '<input type="text" name="question[ans]" id="question_ans" class="form-control" placeholder="Answer text" aria-label="Answer text" aria-describedby="basic-addon2">' +
                '<div class="input-group-append">' +
                '<button class="btn btn-outline-info" type="button" id="ansAdd">Add</button>' +
                '</div>' +
                '</div>' +
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
                    $('.modal-body').append(backhtml);
                    backhtml = null;
                    $('#newQuestion').click(function (e) {
                        e.preventDefault();
                        addQuestionForm('add', $('.modal-body').html());
                    });
                    $('button#deleteRow').click(function (e) {
                        e.preventDefault();
                        this.parentElement.parentElement.remove();
                    });

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
                    success: function(response) {
                        $('#userForm').modal('hide');
                    },
                    error: function(response) {
                        console.log(response);
                        $('#formError').text(response.responseJSON['errorMes']);
                    }
                });

            });


        };

        let addQuizForm = (action) => {
            $('.modal-body').empty().append($('<h4 align="center">Quiz</h4>' +
                '<label id="formError"></label>' +
                '<form id="quizdata">' +
                '<input name="id" id="quiz_id" type="text" hidden/>' +
                '<h6>Name:</h6><input type="text" name="quiz[name]" id="quiz_name" class="form-control"/>' +
                '<div id="quiz_a"><input type="checkbox" name="quiz[active]" id="quiz_active">Active</div>' +
                '<div class="row"><div class="col-md-10">Questions:</h6></div><div class="col-md-2"><button class="btn btn-outline-info" id="newQuestion">New</button></div></div>' +
                '<table  class="table table-bordered table-striped table-sm" name="quiz[questions]" id="quiz_questions">' +
                '<thead class="thead-dark">' +
                '<th>Id</th>' +
                '<th>Text</th>' +
                '<th></th>' +
                '</thead>' +
                '<tbody id="quesBody">' +
                '</tbody>' +
                '</table>' +
                '<h6>New question:</h6>' +
                '<div class="input-group">' +
                '<input id="inputSearch" type = "text" class="form-control" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                '<div class="dropdown-menu" id="variants">' +
                '</div>' +
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
                            $('#variants').append('<span class="dropdown-item" value="'+q['id']+'">'+q['text']+'</span>');
                        }

                        $('.dropdown-item').click(function () {
                            $('#quesBody').append($('<tr><td>'+this.getAttribute('value')+'</td><td>'+this.innerHTML+'</td>' +
                                '<td><button id="deleteRow">X</button></td></tr>'));

                            $('button#deleteRow').click(function (e) {
                                e.preventDefault();
                                this.parentElement.parentElement.remove();
                            });

                            $('#inputSearch').val("");
                        });

                        _body(1);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                })
            };

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

                console.log(send);

                $.ajax({
                    url: link,
                    type: "POST",
                    data: send,
                    dataType: "json",
                    cache: false,
                    success: function(response){
                        console.log(response);
                        $('#userForm').modal('toggle');
                         _body(1);
                    },
                    error: function (response) {
                        console.log(response);
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
            $.ajax({
                url: options.url,
                type: "POST",
                data: "",
                dataType: "json",
                cache: false,
                success: function (response) {
                    let theader = [];
                    let row = document.createElement('thead');
                    row.classList.add('thead-dark');

                    for (let key in response['rows'][0]) {
                        if (contains(options['showFields'],key)) {
                            let headerCell = document.createElement("TH");
                            headerCell.id = "header";
                            headerCell.classList.add('notselected');
                            headerCell.innerHTML = key;
                            row.appendChild(headerCell);
                        }
                    }

                    let headerCell = document.createElement("th");
                    headerCell.innerHTML = "Commands";
                    row.appendChild(headerCell);

                    $('#tbl').append(row);

                    $('th').click(function () {
                        if (options.sortableFields.findIndex( i => i === this.innerHTML) != -1) {

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
                error: function (response) {
                    console.log(response);
                    alert('Table header error');
                }
            });
        };

        let _body = (page) => {
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
                    console.log(response);
                    $('#table-data').remove();
                    $('#pages').remove();
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
                        if (options.mode === 1) {
                            $(cell).append($(
                                '<button id="edit" class="btn btn-warning edit">Edit</button>' +
                                '<button id="delete" class="btn btn-danger delete">Delete</button>'
                                )
                            );
                        } else {
                            let btn = document.createElement('a');
                            btn.classList.add('btn');
                            btn.classList.add('btn-success');
                            btn.innerText = 'Play';
                            btn.href = "/"+keys[i];
                            $(cell).append(btn);
                        }
                        tblbody.appendChild(row);
                    }

                    $('#tbl').append(tblbody);

                    $('button.delete').click(function (e) {
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
                                        success: function (response) {
                                            _body(page);
                                        },
                                        error: function (response) {
                                            alert('Delete error');
                                        }
                                    });
                            }
                    });

                    $('button.edit').click(function (e) {
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
                                        console.log(response);
                                        alert('Delete error');
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
                                            console.log(a);
                                            if (a['right']) {
                                                radio.checked = true;
                                            }
                                            let td3 = document.createElement('td');
                                            let btn = document.createElement('button');
                                            btn.id = "deleteRow";
                                            btn.innerHTML = 'X';

                                            tr.appendChild(td1);
                                            console.log(radio);
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
                                        console.log(response);
                                        alert('Delete error');
                                    }
                                });
                                break;
                        }
                    });

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
            if (count != 0) {
                if (current != 1) {
                    let prev = document.createElement('a');
                    prev.id = "prev";
                    prev.href = "";
                    prev.text = current - 1;
                    prev.setAttribute("class", "page");
                    pages.appendChild(prev);
                }
                let cur = document.createElement('a');
                cur.id = "cur";
                cur.text = current;
                pages.appendChild(cur);
                cur.setAttribute('class', 'cur_page');
                if (row_per_page * current < count) {
                    let next = document.createElement('a');
                    next.id = "next";
                    next.text = Number(current) + 1;
                    next.href = "";
                    next.setAttribute('class', 'page');
                    pages.appendChild(next);
                }
                let all = document.createElement('a');
                all.id = "all";
                all.text = "Showing " + (current * row_per_page - row_per_page + 1) + " to " + ((current * row_per_page > count) ? (count) : (current * row_per_page)) + " of " + count + " entries";

                pages.appendChild(all);
            }
            else
            {
                pages.innerHTML = "<label>No data found</label>";
            }
            this.append(pages);

            $('#prev').click(function (e) {
                e.preventDefault();
                _body(this.text);
            });

            $('#next').click(function (e) {
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

        _tools();
        _table();
        if (options.mode === 1) {
            _header();
        }
        _body(1);
    };
})(jQuery);
