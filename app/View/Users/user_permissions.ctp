    <script type="text/javascript">
         $(document).ready(function () {

             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var arrCountries = $.map(countries, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var arrMarkets = $.map(markets, function(el) { return el; });
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var permissions = jQuery.parseJSON('<?php echo $loginRoles; ?>');
             var userRole = '<?php echo $userRole;?>'; //used to hide options like delete, export if user role is not Global

             var theme = 'base';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }
             
             var clientListSource =
             {
                 datatype: "json",
                 datafields: [
                     { name: 'display_name' },
                     { name: 'client_name' }
                 ],
                 url: "/users/get_client_list/",
                 async: true
             };
             var clientListDataAdapter = new $.jqx.dataAdapter(clientListSource);

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
                    { name: 'active', type: 'boolean' },
                    { name: 'dailysyncmail', type: 'boolean' },
                    { name: 'weeklysummarymail', type: 'boolean' },
                    { name: 'clientpitchmail', type: 'boolean' },
                    { name: 'targetclients', type: 'string' },
                    { name: 'adminlinks', type: 'string' }
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
             var renderer = function (text, align, height) {
                var sort = "<div style='margin-top:8px;'><div style='text-align:center padding-bottom:5px; margin-left:90px;'>"+ text +"<div class='jqx-grid-column-sortascbutton' style='width: 16px; height: 16px; float:right'>"
                 "</div></div></div>"
                 return sort;
             }
             $("#dataTable").jqxDataTable(
             {
                width: (parseInt(screen.availWidth) - 30),
                source: dataAdapter,
                pageable: true,
                pageSize:25,
                pagerButtonsCount:5,
                filterable:true,
                filterMode:'simple',
                sortable: true,
                altRows: true,
                editable: false,
                autoRowHeight: true,
                enableHover: false,
                editSettings: { saveOnPageChange: true, saveOnBlur: false, saveOnSelectionChange: false, cancelOnEsc: true, saveOnEnter: true, editOnDoubleClick: false, editOnF2: false },
                columns: [
                  { text: '', hidden: true, dataField: 'dailysyncmail' },
                  { text: '', hidden: true, dataField: 'weeklysummarymail' },
                  { text: '', hidden: true, dataField: 'clientpitchmail' },
                  { text: '', hidden: true, dataField: 'targetclients' },
                  { text: '', hidden: true, dataField: 'adminlinks' },
                  { text: 'Name', dataField: 'displayname', width: 210, align: 'center', renderer: renderer },
                  { text: 'Title', dataField: 'title', width: 180, align: 'center',sortable:false },
                  { text: 'Location', dataField: 'location', width: 210, align: 'center',renderer: renderer },
                  { text: 'Email', dataField: 'email', editable: false, width: 250, align: 'center',sortable:false },
                  { text: 'Permission', dataField: 'permission', width: 210, align: 'center', columnType: 'template',renderer: renderer },
                  { text: 'Entity', dataField: 'nameofentity', width: 170, align: 'center', columnType: 'template',sortable:false },
                  { text: 'Active', datafield: 'active', columntype: 'template', width: 45, align: 'center', cellsalign: 'center',sortable:false,
                        cellsrenderer: function (row, columnfield, cellvalue) {
                                if(cellvalue) {
                                        return "Yes";
                                } else {
                                        return "No";
                                }
                        }
                  },
                  {
                      text: '', cellsAlign: 'center', align: "center", columnType: 'none', width: 155, editable: false, sortable: false, dataField: null, cellsRenderer: function (row, column, value) {
                          // render custom column.
                          if(userRole != 'Global') {
                                return "<div align='center'><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button><button style='margin-left: 5px; display: none;' data-row='" + row + "' class='deleteButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='deleteClick(event)'>DELETE</button></div>";
                          } else {
                                return "<div align='center'><button data-row='" + row + "' class='editButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='editClick(event)'>EDIT</button><button style='margin-left: 5px;' data-row='" + row + "' class='deleteButtons jqx-rc-all jqx-button jqx-widget jqx-fill-state-normal' onClick='deleteClick(event)'>DELETE</button></div>";
                          }
                      }
                  }
                ]
            });

            editClick = function (event) {
                var target = $(event.target);
                // get button's value.
                var value = target.val();
                // get clicked row.
                var rowIndex = parseInt(event.target.getAttribute('data-row'));
                if (isNaN(rowIndex)) {
                    return;
                }
                //$("#dataTable").jqxDataTable('selectRow', rowIndex);
                var selection = $("#dataTable").jqxDataTable('getSelection');
                for (var i = 0; i < selection.length; i++) {
                    // get a selected row.
                    var data = selection[i];
                }
                openPopup(data);
            }

            $("#popupWindow").jqxWindow({
                width: 1200, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#CancelNew"), maxWidth: 1200, showCloseButton: false
            });
            $(".createNew").jqxButton({ theme: theme });
            $(".createNew").click(function () {
                openPopup();
            });
            function openPopup(rowData) {
                var offset = $("#dataTable").offset();
                $("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 20, y: parseInt(offset.top) + 20 }, height: (userRole != 'Global') ? 250 : "490px", maxWidth: 1200, isModal: true });
                if(rowData) {
                        $('#popupWindow').jqxWindow('setTitle', 'Update Existing User');
                        $("#SaveNewUser").html('UPDATE USER');
                } else {
                        $("#SaveNewUser").html('ADD USER');
                }
                $("#recordid").val((rowData ? rowData.uid : ''));
                if(rowData) {
                        $("#name").jqxInput({ height: 25, width: 225, minLength: 6 }).val(rowData.displayname);
                } else {
                        $("#name").jqxInput({ placeHolder: "Enter First Name", height: 25, width: 225, minLength: 6,
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
                }
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
                $("#title").jqxInput({ height: 25, width: 150 }).val((rowData ? rowData.title : ''));
                $("#location").jqxInput({ height: 25, width: 125 }).val((rowData ? rowData.location : ''));
                $("#email").jqxInput({ height: 25, width: 200 }).val((rowData ? rowData.email : ''));
                $("#permission").jqxDropDownList({ source: permissions, selectedIndex: -1 });
                $("#nameofentity").jqxDropDownList({ selectedIndex: -1 });
                $("#active").jqxCheckBox({ checked: (rowData ? rowData.active : true) });

                $("#dailySyncMails").jqxCheckBox({ checked: (rowData ? rowData.dailysyncmail : false) });
                $("#weeklySummaryMails").jqxCheckBox({ checked: (rowData ? rowData.weeklysummarymail : false) });
                $("#clientPitchMails").jqxCheckBox({ checked: (rowData ? rowData.clientpitchmail : false) });
                $("#targetClients").jqxDropDownList({ selectedIndex: -1, width: 300, filterable: true, checkboxes: true, source: clientListDataAdapter, displayMember: "display_name", valueMember: "client_name" });
                $("#targetClients").jqxDropDownList('uncheckAll');

                $(".adm-lnk").jqxCheckBox({ checked: false });

                if(rowData) {
                    if(rowData.permission == "Global") {
                        $("#nameofentity").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });

                        if(rowData.adminlinks) {
                                $.each(rowData.adminlinks, function( index, value ) {
                                        $(".adm-lnk[linkid="+value+"]").jqxCheckBox('check');
                                });
                        }
                    } else if(rowData.permission == "Regional") {
                        $("#nameofentity").jqxDropDownList({ source: regions, checkboxes: true, selectedIndex: -1 });
                        $('#testForm').jqxValidator('validateInput', '#nameofentity');
                    } else if(rowData.permission == "Country" || rowData.permission == "Country - Viewer") {
                        $("#nameofentity").jqxDropDownList({ source: markets, checkboxes: true, selectedIndex: -1 });
                        $('#testForm').jqxValidator('validateInput', '#nameofentity');
                    } else {
                        $("#nameofentity").jqxDropDownList({ source: ['/'], checkboxes: false, selectedIndex: 0 });
                    }
                    $("#permission").jqxDropDownList('selectItem', rowData.permission);
                    if(rowData.permission == 'Regional' || rowData.permission == 'Country' || rowData.permission == "Country - Viewer") {
                        nameOfEntities = rowData.nameofentity.split(',');
                        for (i=0; i<nameOfEntities.length; i++) {
                            $("#nameofentity").jqxDropDownList('checkItem', nameOfEntities[i]);
                        }
                    } else {
                        $("#nameofentity").jqxDropDownList('selectItem', rowData.nameofentity);
                    }
                    if(rowData.permission == "Global") {
                        $("#dailySyncMails").jqxCheckBox('enable');
                        $("#weeklySummaryMails").jqxCheckBox('enable');
                        $("#clientPitchMails").jqxCheckBox('enable');
                        $("#targetClients").jqxDropDownList({ disabled: false });
                        if(rowData.targetclients != '') {
                            targetClients = rowData.targetclients.split(',');
                            for (i=0; i<targetClients.length; i++) {
                                $("#targetClients").jqxDropDownList('checkItem', targetClients[i]);
                            }
                        }
                        $(".adm-lnk").jqxCheckBox('enable');
                    } else {
                        $("#dailySyncMails").jqxCheckBox('disable');
                        $("#weeklySummaryMails").jqxCheckBox('disable');
                        $("#clientPitchMails").jqxCheckBox('disable');
                        $("#targetClients").jqxDropDownList({ disabled: true });
                        $("#targetClients").jqxDropDownList('uncheckAll');
                        $(".adm-lnk").jqxCheckBox('disable');
                    }
                }
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
                $("#permission").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#permission').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        if(item.label == "Global") {
                                $("#nameofentity").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(item.label == "Regional") {
                                $("#nameofentity").jqxDropDownList({ source: regions, checkboxes: true, selectedIndex: -1 });
                                $('#testForm').jqxValidator('validateInput', '#nameofentity');
                        } else if(item.label == "Country" || item.label == "Country - Viewer") {
                                $("#nameofentity").jqxDropDownList({ source: markets, checkboxes: true, selectedIndex: -1 });
                                $('#testForm').jqxValidator('validateInput', '#nameofentity');
                        } else {
                                $("#nameofentity").jqxDropDownList({ source: ['/'], checkboxes: false, selectedIndex: 0 });
                        }

                        if(item.label == "Global") {
                                $("#dailySyncMails").jqxCheckBox('enable');
                                $("#weeklySummaryMails").jqxCheckBox('enable');
                                $("#clientPitchMails").jqxCheckBox('enable');
                                $("#targetClients").jqxDropDownList({ disabled: false });
                                $(".adm-lnk").jqxCheckBox('enable');
                        } else {
                                $("#dailySyncMails").jqxCheckBox('disable');
                                $("#weeklySummaryMails").jqxCheckBox('disable');
                                $("#clientPitchMails").jqxCheckBox('disable');
                                $("#targetClients").jqxDropDownList({ disabled: true });
                                $("#targetClients").jqxDropDownList('uncheckAll');
                                $(".adm-lnk").jqxCheckBox('disable');
                        }
                    }
                });
            }

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

                var recordId = $("#recordid").val();
                var url = "/users/save_user/";
                if(recordId) {
                    url = "/users/update_user/";
                }
                var admLinks = new Array();
                if($("#permission").val() == 'Global') {
                        $('.adm-lnk').each(function (i) {
                                if($(this).val()) {
                                        var value = $(this).attr('linkid');
                                        admLinks.push(value);
                                }
                        });
                }
                var row = { displayname: $("#name").val(), username: $("#username").val(), title: $("#title").val(),
                    location: $("#location").val(), email: $("#email").val(), permission: $("#permission").val(),
                    nameofentity: $("#nameofentity").val(), activeflag: $("#active").val(), dailysyncmails: $("#dailySyncMails").val(),
                    weeklysummarymails: $("#weeklySummaryMails").val(), clientpitchmails: $("#clientPitchMails").val(),
                    targetclients: $("#targetClients").val(), adminlinks: admLinks
                };
                $.ajax({
                    type: "POST",
                    url: url,
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#dataTable").jqxDataTable('updateBoundData');
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });
            $("#users").jqxCheckBox({ checked : false });
            $("#users").on('change',function(event){
                var checked = event.args.checked;
                source.data = {checked : checked};
                dataAdapter.dataBind();
            });

            $("#loaderWindow").jqxWindow({
                width: 300, resizable: false,  isModal: true, autoOpen: false, maxWidth: 400, maxHeight: 250, showCloseButton: false, keyboardCloseKey: 'none' 
            });
            $('#exporttoexcelbutton').jqxButton({ theme: theme });
            $('#exporttoexcelbutton').click(function () {
                $("#loaderWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "100px", maxWidth: 400, isModal: true, draggable: false });
                $("#loaderWindow").jqxWindow('open');

                var rows = $("#dataTable").jqxDataTable('getRows');
                $.ajax({
                    type: "POST",
                    url: "/users/export_users_data/",
                    data: JSON.stringify(rows),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#loaderWindow").jqxWindow('close');
                            window.open('/files/Users_Data_<?php echo date('m-d-Y'); ?>.xlsx');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
            });

            deleteClick = function (event) {
                var target = $(event.target);
                // get button's value.
                var value = target.val();
                // get clicked row.
                var rowIndex = parseInt(event.target.getAttribute('data-row'));
                if (isNaN(rowIndex)) {
                    return;
                }
                var selection = $("#dataTable").jqxDataTable('getSelection');
                for (var i = 0; i < selection.length; i++) {
                    // get a selected row.
                    var data = selection[i];
                }
                var row = { UserEmail: data.email } ;
                if(confirm('Are you sure to delete this user?')) {
                    $.ajax({
                        type: "POST",
                        url: "/users/delete_user/",
                        data: JSON.stringify(row),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success : function(result) {
                            if(result.success == true) {
                                $("#dataTable").jqxDataTable('updateBoundData');
                            } else {
                                alert(result.errors);
                                return false;
                            }
                        }
                    });
                }
            }
        });
    </script>
    <div id='jqxWidget'>
            <div style="float:right; margin-bottom:10px; margin-right:10px; <?php if($userRole != 'Global') { ?> display: none; <?php } ?>">
                <div id="users">Show all Users</div>
            </div>
        <div id="dataTable"></div>
            <div style='margin-top: 20px;'>
            <div style='float: right; padding-right: 15px; padding-bottom: 30px;'>
                    <button style="margin-right: 5px; <?php if($userRole != 'Global') { ?> display: none; <?php } ?>" value="Export to Excel" id="exporttoexcelbutton">EXPORT .XLS</button>
                    <button value="Create New User" class='createNew'>CREATE NEW USER</button>
            </div>
        </div>
    </div>

    <div id="loaderWindow">
            <div>Export to excel</div>
            <div style="overflow: hidden;">
                    <div id="divLoader" align="center" style="padding-top: 15px; padding-left: 90px;">
                            <div class="jqx-grid-load" style="float: left; overflow: hidden; width: 32px; height: 32px;"></div>
                            <span style="margin-top: 10px; float: left; display: block; margin-left: 5px;">Please wait...</span>
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
                    <td align="left"><input type="text" id="name" autocomplete="off"/>
                            <input type="hidden" id="recordid"/>
                    </td>
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
            <br/><br/>
            <table width="99%" cellpadding="5" <?php if($userRole != 'Global') { ?> style="display: none;" <?php } ?>>
                <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                    <td>Manage email notifications (Global permission only)</td>
                </tr>
                <tr>
                    <td><div style="display: inline-block" id="dailySyncMails">&nbsp;Receive NBRT daily sync and reconciliation notification</div></td>
                </tr>
                <tr>
                    <td><div style="display: inline-block" id="weeklySummaryMails">&nbsp;Receive weekly summary notification</div></td>
                </tr>
                <tr>
                    <td><div style="display: inline-block" id="clientPitchMails">&nbsp;Receive new/update/delete pitch notification</div></td>
                </tr>
                <tr>
                    <td><span style="vertical-align: top;">Select target clients for receiving notification (Default is all clients)</span>&nbsp;<div style="display: inline-block; margin-top: -5px;" id="targetClients"></div></td>
                </tr>
            </table>
            <br/>
            <table width="99%" cellpadding="5" <?php if($userRole != 'Global') { ?> style="display: none;" <?php } ?>>
                <tr style="height: 25px; border-color: #aaa; background: none repeat scroll 0 0 #e8e8e8; border-style: solid; border-width: 0 1px 0 0; font-family: Verdana,Arial,sans-serif; font-size: 13px; font-style: normal;">
                    <td>Manage administration access (Global permission only)</td>
                </tr>
        <?php if(isset($adminLinks) && !empty($adminLinks)) {
                foreach($adminLinks as $adminLinkId => $adminLinkName) {
        ?>
                <tr>
                        <td><div style="display: inline-block" class="adm-lnk" linkid="<?php echo $adminLinkId; ?>">&nbsp;<?php echo $adminLinkName; ?></div></td>
                </tr>
        <?php
                }
        } ?>
            </table>
        </form>
        <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SaveNewUser" value="Add user">ADD USER</button></div>
        </div>
   </div>
