    <script type="text/javascript">
         $(document).ready(function () {

             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var arrCountries = $.map(countries, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var arrMarkets = $.map(markets, function(el) { return el; });
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var permissions = jQuery.parseJSON('<?php echo $loginRoles; ?>');
             
             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }

             var source =
             {
                dataType: "json",
                id: 'userid',
                url: "/users/get_users/",
                datafields: [
                    { name: 'displayname', type: 'string' },
                    { name: 'title', type: 'string' },
                    { name: 'location', type: 'string' },
                    { name: 'email', type: 'string' },
                    { name: 'permission', type: 'string' },
                    { name: 'nameofentity', type: 'string' },
                    { name: 'active', type: 'string' }
                ],
                addRow: function (rowID, rowData, position, commit) {
                    // synchronize with the server - send insert command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    // you can pass additional argument to the commit callback which represents the new ID if it is generated from a DB.
                    commit(true);
                },
                updateRow: function (rowID, rowData, commit) {
                    // synchronize with the server - send update command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    commit(true);
                },
                deleteRow: function (rowID, commit) {
                    // synchronize with the server - send delete command
                    // call commit with parameter true if the synchronization with the server is successful 
                    // and with parameter false if the synchronization failed.
                    commit(true);
                }
             };

             var dataAdapter = new $.jqx.dataAdapter(source, {
                loadComplete: function () {
                    // data is loaded.
                }
             });
             this.editrow = -1;
             
            var entityEditor;
            $("#dataTable").jqxDataTable(
            {
                width: (parseInt(screen.availWidth) - 30),
                source: dataAdapter,
                pageable: false,
                sortable: false,
                altRows: true,
                editable: true,
                autoRowHeight: true,
                enableHover: false,
                editSettings: { saveOnPageChange: true, saveOnBlur: false, saveOnSelectionChange: false, cancelOnEsc: true, saveOnEnter: true, editOnDoubleClick: false, editOnF2: false },
                // called when jqxDataTable is going to be rendered.
                rendering: function()
                {
                    // destroys all buttons.
                    if ($(".editButtons").length > 0) {
                        $(".editButtons").jqxButton('destroy');
                    }
                    if ($(".cancelButtons").length > 0) {
                        $(".cancelButtons").jqxButton('destroy');
                    }
                },
                // called when jqxDataTable is rendered.
                rendered: function () {
                    if ($(".editButtons").length > 0) {
                        $(".cancelButtons").jqxButton();
                        $(".editButtons").jqxButton();
                        
                        var editClick = function (event) {
                            var target = $(event.target);
                            // get button's value.
                            var value = target.val();
                            // get clicked row.
                            var rowIndex = parseInt(event.target.getAttribute('data-row'));
                            if (isNaN(rowIndex)) {
                                return;
                            }
                            if (value == "EDIT") {
                                if(target.parent().parent().parent().find('.cancelButtons:visible').length > 0) {
                                        return false;
                                }
                                // begin edit.
                                $("#dataTable").jqxDataTable('beginRowEdit', rowIndex);
                                target.parent().find('.cancelButtons').show();
                                target.val("SAVE");
                            } else {
                                rowEdit = $("#dataTable").jqxDataTable('endRowEdit', rowIndex);
                                if(target.parent().parent().find('.jqx-grid-validation-label').length > 0) {
                                        var rowIndex = parseInt(event.target.getAttribute('data-row'));
                                        $("#dataTable").jqxDataTable('beginRowEdit', rowIndex);
                                        return false;
                                }
                                
                                var displayname = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'displayname');
                                var title = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'title');
                                var location = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'location');
                                var email = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'email');
                                var permission = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'permission');
                                var nameofentity = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'nameofentity');
                                var activeflag = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'active');
                                
                                var row = { displayname: displayname, title: title,
                                        location: location, email: email, permission: permission, 
                                        nameofentity: nameofentity, activeflag: activeflag
                                };
                                $.ajax({
                                        type: "POST",
                                        url: "/users/update_user/",
                                        data: JSON.stringify(row),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        success : function(result) {
                                            if(result.success == true) {
                                                // end edit and save changes.
                                                target.parent().find('.cancelButtons').hide();
                                                target.val("Edit");
                                                alert("Data saved successfully...");
                                            } else {
                                                alert(result.errors);
                                                return false;
                                            }
                                        }
                                });
                            }
                        }
                        $(".editButtons").on('click', function (event) {
                            editClick(event);
                        });
                 
                        $(".cancelButtons").click(function (event) {
                            // end edit and cancel changes.
                            var rowIndex = parseInt(event.target.getAttribute('data-row'));
                            if (isNaN(rowIndex)) {
                                return;
                            }
                            $("#dataTable").jqxDataTable('endRowEdit', rowIndex, true);
                        });
                    }
                },
                columns: [
                  { text: 'Name', dataField: 'displayname', width: 200, align: 'center',
                      validation: function (cell, value) {
                          if (value == '' || value == null) {
                                return { message: "Name is required!", result: false };
                          }
                          return true;
                      }
                  },
                  { text: 'Title', dataField: 'title', width: 200, align: 'center' },
                  { text: 'Location', dataField: 'location', width: 120, align: 'center' },
                  { text: 'Email', dataField: 'email', editable: false, width: 230, align: 'center' },
                  {
                      text: 'Permission', dataField: 'permission', width: 110, align: 'center',
                      columnType: 'template',
                      createEditor: function (row, cellvalue, editor, cellText, width, height) {
                          editor.jqxDropDownList({ autoDropDownHeight: true, source: permissions, width: width });
                      },
                      initEditor: function (row, cellvalue, editor, celltext, width, height) {
                          // set the editor's current value. The callback is called each time the editor is displayed.
                          editor.jqxDropDownList({ width: width, height: height });
                          editor.val(cellvalue);
                          editor.bind('change', function (event) {
                              var args = event.args;
                              var item = editor.jqxDropDownList('getItem', args.index);
                              if(item.value == "Global") {
                                        entityEditor.jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                              } else if(item.value == "Regional") {
                                        entityEditor.jqxDropDownList({ source: regions, checkboxes: false, selectedIndex: -1 });
                              } else if(item.value == "Country" || item.value == "Country - Viewer") {
                                        entityEditor.jqxDropDownList({ source: markets, checkboxes: true, selectedIndex: -1 });
                              } else {
                                        entityEditor.jqxDropDownList({ source: ['/'], checkboxes: false, selectedIndex: 0 });
                              }
                              return item.value;
                          });
                      },
                      validation: function (cell, value) {
                          if (value == '' || value == null) {
                                return { message: "Permission is required!", result: false };
                          }
                          return true;
                      }
                  },
                  {
                      text: 'Entity', dataField: 'nameofentity', width: 180, align: 'center',
                      columnType: 'template',
                      initEditor: function (row, cellvalue, editor, cellText, width, height) {
                                var permission = $('#dataTable').jqxDataTable('getCellValue', row, "permission");
                                if(permission == "Global") {
                                        editor.jqxDropDownList({ source: ['Global'], checkboxes: false, width: width, height: height, selectedIndex: 0 });
                                } else if(permission == "Regional") {
                                        editor.jqxDropDownList({ source: regions, checkboxes: false, width: width, height: height });
                                        if (cellvalue != "Please Choose:") {
                                                var index = arrRegions.indexOf(cellvalue);
                                                editor.jqxDropDownList('selectIndex', index);
                                        }
                                } else if(permission == "Country" || permission == "Country - Viewer") {
                                        editor.jqxDropDownList({ source: markets, checkboxes: true, width: width, height: height });
                                        if (cellvalue != "Please Choose:") {
                                                entities = cellvalue.split(',');
                                                for(key in entities) {
                                                        if(arrMarkets.indexOf(entities[key]) != -1) {
                                                               index = arrMarkets.indexOf(entities[key]);
                                                               editor.jqxDropDownList('checkIndex', index);
                                                        }
                                                }
                                        }
                                } else {
                                        editor.jqxDropDownList({ source: ['/'], checkboxes: false, width: width, height: height, selectedIndex: 0 });
                                }
                                entityEditor = editor;
                                editor.bind('change', function (event) {
                                        if($(event.target).val() != '') {
                                                $(event.target).removeClass('jqx-grid-validation-label');
                                        }
                                });
                      },
                      validation: function (cell, value) {
                          if (value == '' || value == null) {
                                return { message: "Entity is required!", result: false };
                          }
                          return true;
                      }
                  },
                  { text: 'Active', datafield: 'active', columntype: 'template', width: 50, align: 'center', cellsalign: 'center',
                        cellsrenderer: function (row, columnfield, cellvalue) {
                                if(cellvalue == 0) {
                                        return "No";
                                } else {
                                        return "Yes";
                                }
                        },
                        createEditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxCheckBox({ checked: ((cellvalue == 1) ? true : false), width: width, height: height });
                        }
                  },
                  {
                      text: 'Edit', cellsAlign: 'center', align: "center", columnType: 'none', width: 160, editable: false, sortable: false, dataField: null, cellsRenderer: function (row, column, value) {
                          // render custom column.
                          return "<button data-row='" + row + "' class='editButtons'>EDIT</button><button style='display: none; margin-left: 5px;' data-row='" + row + "' class='cancelButtons'>CANCEL</button>";
                      }
                  }
                ]
            });
            $('#dataTable').on('rowEndEdit', function (event) {
                /*var target = $(event.target);
                console.log(target.parent().parent().find('.jqx-grid-validation-label').length);
                if(target.parent().parent().find('.jqx-grid-validation-label')) {
                        var rowIndex = parseInt(event.target.getAttribute('data-row'));
                        $("#dataTable").jqxDataTable('beginRowEdit', rowIndex);
                        return false;
                }*/
            });

            $("#popupWindow").jqxWindow({
                width: 1200, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNew"), maxWidth: 1200, showCloseButton: false 
            });
            $("#createNew").jqxButton({ theme: theme });
            $("#createNew").click(function () {
                var offset = $("#dataTable").offset();
                $("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 20, y: parseInt(offset.top) + 20 }, height: "200px", maxWidth: 1200, isModal: true });
                $("#name").jqxInput({ placeHolder: "Enter First Name", height: 25, width: 225, minLength: 7, 
                        source: function (query, response) {
                                var dataAdapter = new $.jqx.dataAdapter
                                (
                                        {
                                                datatype: "json",
                                                type: "POST",
                                                datafields:
                                                [
                                                        { name: 'Name' },
                                                        { name: 'Title' },
                                                        { name: 'Location' },
                                                        { name: 'Email' }
                                                ],
                                                url: "/users/search_user/",
                                                data:
                                                {
                                                        maxRows: 20
                                                }
                                        },
                                        {
                                                autoBind: true,
                                                formatData: function (data) {
                                                        data.name_startsWith = query;
                                                        return data;
                                                },
                                                loadComplete: function (data) {
                                                        if (data.length > 0) {
                                                                response($.map(data, function (item) {
                                                                        return {
                                                                                label: item.Name + (item.Title ? ", " + item.Title : "") + (item.Location ? ", " + item.Location : "") + (item.Email ? ", " + item.Email : ""),
                                                                                value: item.Name + "# " + item.Title + "# " + item.Location + "# " + item.Email + "# " + item.UserName
                                                                        }
                                                                }));
                                                        } else {
                                                                response(jQuery.parseJSON('{"label":"No matching records found!", "value":"NA"}'));
                                                        }
                                                }
                                        }
                                );
                        }
                }).val('');
                $('#name').jqxInput('focus'); 
                $("#name").on('select', function (event) {
                    if (event.args) {
                        var item = event.args.item;
                        if (item) {
                            var terms = item.value.split(/#\s*/);
                            $("#name").jqxInput('val', terms[0]);
                            $("#title").jqxInput('val', terms[1]);
                            $("#location").jqxInput('val', terms[2]);
                            $("#email").jqxInput('val', terms[3]);
                            $("#username").val(terms[4]);
                        }
                    }
                });
                $("#title").jqxInput({ height: 25, width: 150 }).val('');
                $("#location").jqxInput({ height: 25, width: 125 }).val('');
                $("#email").jqxInput({ height: 25, width: 200 }).val('');
                $("#permission").jqxDropDownList({ source: permissions, selectedIndex: -1 });
                $("#nameofentity").jqxDropDownList({ selectedIndex: -1 });
                $("#active").jqxCheckBox({ checked: true });
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
                $("#permission").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#permission').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        if(item.label == "Global") {
                                $("#nameofentity").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(item.label == "Regional") {
                                $("#nameofentity").jqxDropDownList({ source: regions, checkboxes: false, selectedIndex: -1 });
                                $('#testForm').jqxValidator('validateInput', '#nameofentity');
                        } else if(item.label == "Country" || item.label == "Country - Viewer") {
                                $("#nameofentity").jqxDropDownList({ source: markets, checkboxes: true, selectedIndex: -1 });
                                $('#testForm').jqxValidator('validateInput', '#nameofentity');
                        } else {
                                $("#nameofentity").jqxDropDownList({ source: ['/'], checkboxes: false, selectedIndex: 0 });
                        }
                    }
                });
            });
            
            $('#testForm').jqxValidator({ position: 'bottom', rules: [
                { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required' },
                { input: '#email', message: 'E-mail is required!', action: 'blur', rule: 'required' },
                { input: '#email', message: 'Invalid e-mail!', action: 'blur', rule: 'email' },
                { input: '#permission', message: 'Permission is required!', action: 'change, blur', rule: function (input) {
                        if (input.val() != '') {
                                return true;
                        }
                        return false;
                    } 
                },
                { input: '#nameofentity', message: 'Entity is required!', action: 'change, blur', rule: function (input) {
                        if (input.val() != '') {
                                return true;
                        }
                        return false;
                    } 
                }
                ]
            });
            
            $("#CancelNew").jqxButton({ theme: theme });
            $("#SaveNewUser").jqxButton({ theme: theme });
            // update the edited row when the user clicks the 'Save' button.
            $("#SaveNewUser").click(function () {
                if(!$('#testForm').jqxValidator('validate')) {
                        return false;
                }
                
                var row = { displayname: $("#name").val(), username: $("#username").val(), title: $("#title").val(),
                    location: $("#location").val(), email: $("#email").val(), permission: $("#permission").val(), 
                    nameofentity: $("#nameofentity").val(), activeflag: $("#active").val()
                };
                $.ajax({
                    type: "POST",
                    url: "/users/save_user/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            //alert("Data saved successfully...");
                            $("#dataTable").jqxDataTable('updateBoundData');
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });
        });
    </script>
    <div id='jqxWidget'>
        <div id="dataTable"></div>
            <div style='margin-top: 20px;'>
            <div style='float: right; padding-right: 15px; padding-bottom: 30px;'>
                    <button value="Create New User" id='createNew'>CREATE NEW USER</button>
            </div>
        </div>
    </div>
    
    <div id="popupWindow">
        <div>Create New User</div>
        <div style="overflow: hidden;">
        <div style="padding-bottom: 10px;" align="right"><button style="margin-right: 15px;" id="CancelNew" value="Cancel">CANCEL</button></div>
        <form id="testForm" action="./">
            <table>
                <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                    <td align="center">Name</td>
                    <td align="center">Title</td>
                    <td align="center">Location</td>
                    <td align="center">Email</td>
                    <td align="center">Permission</td>
                    <td align="center">Name of entity</td>
                    <td align="center">Active</td>
                </tr>
                <tr>
                    <td align="left"><input type="text" id="name" autocomplete="off"/></td>
                    <td align="left"><input type="text" id="title"/></div></td>
                    <td align="left"><input type="text" id="location"/></td>
                    <td align="left"><input type="text" class="readonly" id="email" readonly/>
                            <input type="hidden" id="username"/>
                    </td>
                    <td align="left"><div id="permission"></div></td>
                    <td align="left"><div id="nameofentity"></div></td>
                    <td align="center"><div id="active"></div></td>
                </tr>
            </table>
        </form>
        <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNewUser" value="Add user">ADD USER</button></div>
        </div>
   </div>
