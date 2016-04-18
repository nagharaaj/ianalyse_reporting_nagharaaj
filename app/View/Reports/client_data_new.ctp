    <script type="text/javascript">
         $(document).ready(function () {

             var cities = jQuery.parseJSON('<?php echo $cities; ?>');
             var arrCities = $.map(cities, function(el) { return el; });
             var categories = jQuery.parseJSON('<?php echo $categories; ?>');
             var arrCategories = $.map(categories, function(el) { return el; });
             var countries = jQuery.parseJSON('<?php echo $countries; ?>');
             var arrCountries = $.map(countries, function(el) { return el; });
             var currencies = jQuery.parseJSON('<?php echo $currencies; ?>');
             var arrCurrencies = $.map(currencies, function(el) { return el; });
             var agencies = jQuery.parseJSON('<?php echo $agencies; ?>');
             var arrAgencies = $.map(agencies, function(el) { return el; });
             var services = jQuery.parseJSON('<?php echo $services; ?>');
             var arrServices = $.map(services, function(el) { return el; });
             var markets = jQuery.parseJSON('<?php echo $markets; ?>');
             var regions = jQuery.parseJSON('<?php echo $regions; ?>');
             var arrRegions = $.map(regions, function(el) { return el; });
             var stages = ['Live - aggressive', 'Live - defensive', 'Lost - current client', 'Lost - new business', 'Won - new business', 'Won - retained'];
             var arrStages = $.map(stages, function(el) { return el; });
             
             var months = [
                     {value: 1, label: "Jan (1)"}, 
                     {value: 2, label: "Feb (2)"}, 
                     {value: 3, label: "Mar (3)"}, 
                     {value: 4, label: "Apr (4)"}, 
                     {value: 5, label: "May (5)"}, 
                     {value: 6, label: "Jun (6)"}, 
                     {value: 7, label: "Jul (7)"}, 
                     {value: 8, label: "Aug(8)"}, 
                     {value: 9, label: "Sep (9)"}, 
                     {value: 10, label: "Oct (10)"}, 
                     {value: 11, label: "Nov (11)"}, 
                     {value: 12, label: "Dec (12)"}
             ];
             var monthsSource =
             {
                 datatype: "array",
                 datafields: [
                     { name: 'label', type: 'string' },
                     { name: 'value', type: 'number' }
                 ],
                 localdata: months
             };
             var monthsAdapter = new $.jqx.dataAdapter(monthsSource, {
                autoBind: true
             });
             var years = [];
             var currDate = new Date();
             var currYear = currDate.getFullYear();
             for(i = 1995; i <= currYear; i++) {
                     years[years.length] = {value: i, label: i.toString()};
             }

             var theme = 'base';
             var classWon = 'stage-won';
             var classLost = 'stage-lost';
             var classLive = 'stage-live';
             // renderer for grid cells.
             var numberrenderer = function (row, column, value) {
                 return '<div style="text-align: center; margin-top: 5px;">' + (1 + value) + '</div>';
             }

             var source =
             {
                dataType: "json",
                id: 'id',
                url: "/reports/get_client_data/",
                datafields: [
                    { name: 'RecordId', type: 'number' },
                    { name: 'Region', type: 'string' },
                    { name: 'Country', type: 'string' },
                    { name: 'City', type: 'string' },
                    { name: 'LeadAgency', type: 'string' },
                    { name: 'ClientName', type: 'string' },
                    { name: 'ParentCompany', type: 'string' },
                    { name: 'ClientCategory', type: 'string' },
                    { name: 'PitchStart', type: 'string' },
                    { name: 'PitchLeader', type: 'string' },
                    { name: 'PitchStage', type: 'string' },
                    { name: 'ClientMonth', type: 'string' },
                    { name: 'ClientYear', type: 'number' },
                    { name: 'Lost', type: 'string' },
                    { name: 'Service', type: 'string' },
                    { name: 'ActiveMarkets', type: 'string' },
                    { name: 'Currency', type: 'string' },
                    { name: 'EstimatedRevenue', type: 'number' },
                    { name: 'ActualRevenue', type: 'number' },
                    { name: 'Comments', type: 'string' }
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

             var dataAdapter = new $.jqx.dataAdapter(source);
             var cellclass = function (row, datafield, value, rowdata) {
                //console.log(rowdata);
                var stage = rowdata.PitchStage;
                if(stage.match(/Won/g)) {
                        return classWon;
                } else if (stage.match(/Lost/g)) {
                        return classLost;
                } else if (stage.match(/Live/g)) {
                        return classLive;
                } else {
                        return '';
                }
            }

             // initialize jqxGrid
             $("#dataTable").jqxDataTable(
             {
                width: 1250,
                source: dataAdapter,
                pageable: true,
                pageSize: 20,
                sortable: false,
                editable: true,
                autoRowHeight: false,
                editSettings: { saveOnPageChange: true, saveOnBlur: true, saveOnSelectionChange: false, cancelOnEsc: true, saveOnEnter: true, editOnDoubleClick: false, editOnF2: false },
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
                            if (value == "Edit") {
                                // begin edit.
                                $("#dataTable").jqxDataTable('beginRowEdit', rowIndex);
                                target.parent().find('.cancelButtons').show();
                                target.val("Save");
                            } else {
                                $("#dataTable").jqxDataTable('endRowEdit', rowIndex);
                                if(target.parent().parent().find('.jqx-grid-validation-label').length > 0) {
                                        var rowIndex = parseInt(event.target.getAttribute('data-row'));
                                        $("#dataTable").jqxDataTable('beginRowEdit', rowIndex);
                                        return false;
                                }

                                var recordid = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'RecordId');
                                var clientname = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientName');
                                var parentcompany = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ParentCompany');
                                var region = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Region');
                                var country = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Country');
                                var city = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'City');
                                var leadagency = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'LeadAgency');
                                var clientcategory = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ClientCategory');
                                var pitchstart = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchStart');
                                var pitchleader = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchLeader');
                                var pitchstage = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'PitchStage');
                                var service = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Service');
                                var activemarkets = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'ActiveMarkets');
                                var currency = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Currency');
                                var estimatedrevenue = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'EstimatedRevenue');
                                var comments = $("#dataTable").jqxDataTable('getCellValue', rowIndex, 'Comments');
                                
                                var row = { RecordId: recordid, ClientName: clientname, ParentCompany: parentcompany, Region: region,
                                        Country: country, City: city, LeadAgency: leadagency, ClientCategory: clientcategory, 
                                        PitchStart: pitchstart, PitchLeader: pitchleader, PitchStage: pitchstage,
                                        Service: service, ActiveMarkets: activemarkets, Currency: currency,
                                        EstimatedRevenue: estimatedrevenue, Comments: comments
                                };
                                $.ajax({
                                        type: "POST",
                                        url: "/reports/update_client_record/",
                                        data: JSON.stringify(row),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json",
                                        success : function(result) {
                                            if(result.success == true) {
                                                target.parent().find('.cancelButtons').hide();
                                                target.val("Edit");
                                                alert("Data saved successfully...");
                                                // end edit and save changes.
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
                  { text: 'RecordId', datafield: 'RecordId', hidden: true, editable: false },
                  { text: 'Region'/*, columnType: 'template'*/, datafield: 'Region', width: 200, cellClassName: cellclass, editable: false,
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrRegions) == -1) {
                                        return { result: false, message: "Please select valid region..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: regions, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Managing Entity'/*, columntype: 'template'*/, datafield: 'Country', width: 200, cellClassName: cellclass, editable: false,
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrCountries) == -1) {
                                        return { result: false, message: "Please select valid country..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: markets, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Managing City'/*, columntype: 'template'*/, datafield: 'City', width: 200, cellClassName: cellclass, editable: false,
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrCities) == -1) {
                                        return { result: false, message: "Please select valid city..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        var entity = $('#dataTable').jqxDataTable('getCellValue', row, "Country");
                                        if(entity == "Global") {
                                                editor.jqxDropDownList({ source: ['Global'], checkboxes: false, width: width, height: height, selectedIndex: 0 });
                                        } else if(entity.match(/Regional/g)) {
                                                editor.jqxDropDownList({ source: [entity], checkboxes: false, width: width, height: height, selectedIndex: 0 });
                                        } else {
                                                arrCities = cities[entity];
                                                editor.jqxDropDownList({ source: arrCities, checkboxes: false, width: width, height: height });
                                                editor.val(cellvalue);
                                        }
                                }
                        }*/
                  },
                  { text: 'Lead Agency'/*, columntype: 'template'*/, datafield: 'LeadAgency', width: 200, cellClassName: cellclass, editable: false, 
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrAgencies) == -1) {
                                        return { result: false, message: "Please select valid Agency..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, source: agencies, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass, editable: false,
                      validation: function (cell, value) {
                          if (value == '' || value == null) {
                                return { message: "Name is required!", result: false };
                          }
                          return true;
                      }
                  },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass, editable: false,
                      validation: function (cell, value) {
                          if (value == '' || value == null) {
                                return { message: "Parent Company is required!", result: false };
                          }
                          return true;
                      }
                  },
                  { text: 'Client Category'/*, columntype: 'template'*/, datafield: 'ClientCategory', width: 200, cellClassName: cellclass, editable: false, 
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrCategories) == -1) {
                                        return { result: false, message: "Please select valid category..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: categories, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Pitch Start', columntype: 'template', datafield: 'PitchStart', width: 100, cellClassName: cellclass,
                        validation: function (cell, value) {
                                if (value == '' || value == null) {
                                        return { message: "Pitch Start is required!", result: false };
                                }
                                return true;
                        },
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDateTimeInput({ formatString: 'MM/yyyy', width: width, height: height });
                                var cellValue = cellvalue.split('/');
                                editor.val(new Date(cellValue[1], (parseInt(cellValue[0])-1), 1));
                        }
                  },
                  { text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 250, cellClassName: cellclass,
                      validation: function (cell, value) {
                                if (value == '' || value == null) {
                                        return { message: "Pitch Leader is required!", result: false };
                                }
                                return true;
                      }
                  },
                  { text: 'Stage', columntype: 'template', datafield: 'PitchStage', width: 150, cellClassName: cellclass,
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrStages) == -1) {
                                        return { result: false, message: "Please select valid stage..." };
                                }
                                return true;
                        },
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: stages, width: width, height: height });
                                editor.val(cellvalue);
                        }
                  },
                  { text: 'Client Since Month', columntype: 'template', datafield: 'ClientMonth', width: 150, cellClassName: cellclass,
                        validation: function (cell, value) {
                                var row = cell.column.owner.rowsByKey[parseInt(cell.row)];
                                var stage = row.PitchStage;
                                if(stage.match(/Live/g) || stage.match(/Won/g)) {
                                        if (value == '' || value == '0') {
                                                return { result: false, message: "Please select valid month..." };
                                        }
                                }
                                return true;
                        },
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, source: monthsAdapter, displayMember: 'label', valueMember: 'value', width: width, height: height });
                                editor.val(cellvalue);
                        }
                  },
                  { text: 'Client Since Year', columntype: 'template', datafield: 'ClientYear', width: 150, cellClassName: cellclass, 
                        validation: function (cell, value) {
                                var row = cell.column.owner.rowsByKey[parseInt(cell.row)];
                                var stage = row.PitchStage;
                                if(stage.match(/Live/g) || stage.match(/Won/g)) {
                                        if (value == '' || value == '0') {
                                                return { result: false, message: "Please select valid year..." };
                                        }
                                }
                                return true;
                        },
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDropDownList({ autoDropDownHeight: false, source: years, width: width, height: height });
                                editor.val(cellvalue);
                        }
                  },
                  { text: 'Lost (M-Y)', columntype: 'template', datafield: 'Lost', width: 100, cellClassName: cellclass,
                        validation: function (cell, value) {
                                var row = cell.column.owner.rowsByKey[parseInt(cell.row)];
                                var stage = row.PitchStage;
                                if(stage.match(/Lost/g)) {
                                        if (value == '' || value == null) {
                                                return { result: false, message: "Please select valid lost date..." };
                                        }
                                }
                                return true;
                        },
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDateTimeInput({ formatString: 'MM/yyyy', width: width, height: height });
                                var cellValue = cellvalue.split('/');
                                editor.val(new Date(cellValue[1], (parseInt(cellValue[0])-1), 1));
                        }
                  },
                  { text: 'Service'/*, columntype: 'template'*/, datafield: 'Service', width: 200, cellClassName: cellclass, editable: false, 
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrServices) == -1) {
                                        return { result: false, message: "Please select valid service..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: services, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Active Markets', columntype: 'template', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 250, cellClassName: cellclass, 
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                editor.jqxDropDownList({ source: countries, checkboxes: true, width: width, height: height });
                                if (cellvalue != "Please Choose:") {
                                        entities = cellvalue.split(',');
                                        for(key in entities) {
                                                if(arrCountries.indexOf(entities[key]) != -1) {
                                                       index = arrCountries.indexOf(entities[key]);
                                                       editor.jqxDropDownList('checkIndex', index);
                                                }
                                        }
                                }
                        },
                        geteditorvalue: function (row, cellvalue, editor) {
                          // return the editor's value.
                          return editor.val();
                        }
                  },
                  { text: 'Currency'/*, columntype: 'template'*/, datafield: 'Currency', width: 100, cellClassName: cellclass, editable: false, 
                        validation: function (cell, value) {
                                if (jQuery.inArray(value, arrCurrencies) == -1) {
                                        return { result: false, message: "Please select valid currency..." };
                                }
                                return true;
                        }/*,
                        initeditor: function (row, cellvalue, editor, cellText, width, height) {
                                if(cellvalue == '') {
                                        editor.jqxDropDownList({ autoDropDownHeight: false, filterable: true, source: currencies, width: width, height: height });
                                        editor.val(cellvalue);
                                }
                        }*/
                  },
                  { text: 'Estimated Annual Revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 200, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2',
                        cellBeginEdit: function (row, datafield, columntype) {
                                var stage = $('#dataTable').jqxDataTable('getCellValue', row, "PitchStage");
                                if (stage.match(/Lost/g))
                                    return false;
                        }
                  },
                  { text: 'Actual Annual Revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 200, align: 'right', cellsalign: 'right', cellClassName: cellclass, editable: (cellclass == classLost ? false : true), cellsFormat: 'f2' },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 250, cellClassName: cellclass },
                  {
                      text: 'Edit', cellsAlign: 'center', align: "center", columnType: 'none', width: 150, editable: false, sortable: false, dataField: null, 
                      cellsRenderer: function (row, column, value) {
                          // render custom column.
                          return "<button data-row='" + row + "' class='editButtons'>Edit</button><button style='display: none; margin-left: 5px;' data-row='" + row + "' class='cancelButtons'>Cancel</button>";
                      }
                  }
                ]
            });

            $("#popupWindow").jqxWindow({
                width: 600, resizable: false,  isModal: true, autoOpen: false, cancelButton: $("#Cancel"), maxWidth: 700, maxHeight: 700, showCloseButton: false 
            });
            $("#createNew").jqxButton({ theme: theme });
            $("#createNew").click(function () {
                var offset = $("#dataTable").offset();
                $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "650px", maxWidth: 700, isModal: true });
                $("#region").jqxDropDownList({ source: regions, selectedIndex: -1 });
                $("#nameofentity").jqxDropDownList({ source: markets, selectedIndex: -1 });
                $("#city").jqxDropDownList();
                $("#agency").jqxDropDownList({ source: agencies, selectedIndex: -1 });
                $("#advertisername").jqxInput({ placeHolder: "Enter a Name", height: 25, width: 200, minLength: 1, 
                        source: function (query, response) {
                                var dataAdapter = new $.jqx.dataAdapter
                                (
                                        {
                                                datatype: "json",
                                                type: "POST",
                                                datafields:
                                                [
                                                        { name: 'advertiser_name' },
                                                        { name: 'parent_company' }
                                                ],
                                                url: "/reports/search_client/",
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
                                                                                label: item.advertiser_name + (item.parent_company ? ", " + item.parent_company : ""),
                                                                                value: item.advertiser_name + ", " + item.parent_company
                                                                        }
                                                                }));
                                                        }
                                                }
                                        }
                                );
                        }
                }).val('');
                $("#advertisername").on('select', function (event) {
                    if (event.args) {
                        var item = event.args.item;
                        if (item) {
                            var terms = item.value.split(/,\s*/);
                            $("#advertisername").jqxInput('val', terms[0]);
                            $("#parentcompany").jqxInput('val', terms[1]);
                        }
                    }
                });
                $("#parentcompany").jqxInput({ height: 25, width: 175 }).val('');
                $("#category").jqxDropDownList({ source: categories, selectedIndex: -1 });
                $("#pitchstart").jqxDateTimeInput({ formatString: 'MM/yyyy', width: 100, height: 25 });
                $("#pitchleader").jqxInput({ height: 25, width: 200 }).val('');
                $("#pitchstage").jqxDropDownList({ source: ['Live - aggressive', 'Live - defensive', 'Lost - current client', 'Lost - new business', 'Won - new business', 'Won - retained'], selectedIndex: -1 });
                $("#service").jqxDropDownList({ source: services, selectedIndex: -1 });
                $("#activemarket").jqxDropDownList({ source: countries, checkboxes: true, selectedIndex: -1 });
                $("#currency").jqxDropDownList({ source: currencies, selectedIndex: -1 });
                $("#estrevenue").jqxInput({ height: 25, width: 100, rtl:true }).val('');
                $("#notes").jqxInput({ height: 25, width: 200 }).val('');
                // show the popup window.
                $("#popupWindow").jqxWindow('open');
                $("#nameofentity").bind('select', function (event) {
                    var args = event.args;
                    var item = $('#nameofentity').jqxDropDownList('getItem', args.index);
                    if(item != null) {
                        if(item.label == "Global") {
                                $("#city").jqxDropDownList({ source: ['Global'], checkboxes: false, selectedIndex: 0 });
                        } else if(item.label.match(/Regional/g)) {
                                $("#city").jqxDropDownList({ source: [item.label], checkboxes: false, selectedIndex: 0 });
                        } else {
                                arrCities = cities[item.label];
                                $("#city").jqxDropDownList({ source: arrCities, checkboxes: false, selectedIndex: -1 });
                        }
                    }
                });
            });
            
            $('#testForm').jqxValidator({ position: 'right', rules: [
                        { input: '#advertisername', message: 'Advertiser name is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#parentcompany', message: 'Parent company is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#region', message: 'Region is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#city', message: 'City is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#agency', message: 'Lead agency is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#nameofentity', message: 'Entity is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#category', message: 'Category is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#pitchleader', message: 'Pitch leader is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#pitchstage', message: 'Stage is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#service', message: 'Service is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#activemarket', message: 'Active Market is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#currency', message: 'Currency is required!', action: 'change', rule: function (input) {
                                if (input.val() != '') {
                                        return true;
                                }
                                return false;
                            } 
                        },
                        { input: '#estrevenue', message: 'iP estimated revenue is required!', action: 'keyup, blur', rule: 'required' },
                        { input: '#estrevenue', message: 'iP estimated revenue should be numeric!', action: 'keyup, blur', rule: function (input) {
                                if (!isNaN(parseFloat(input.val())) && isFinite(input.val())) {
                                        return true;
                                }
                                return false;
                            } 
                        }
                ]
            });
            
            $("#Cancel").jqxButton({ theme: theme });
            $("#Save").jqxButton({ theme: theme });
            // update the edited row when the user clicks the 'Save' button.
            $("#Save").click(function () {
                if(!$('#testForm').jqxValidator('validate')) {
                        return false;
                }
                
                var row = { ClientName: $("#advertisername").val(), ParentCompany: $("#parentcompany").val(), Region: $("#region").val(),
                    Country: $("#nameofentity").val(), City: $("#city").val(), LeadAgency: $("#agency").val(), ClientCategory: $("#category").val(), 
                    PitchStart: $("#pitchstart").val(), PitchLeader: $("#pitchleader").val(), PitchStage: $("#pitchstage").val(),
                    Service: $("#service").val(), ActiveMarkets: $("#activemarket").val(), Currency: $("#currency").val(),
                    EstimatedRevenue: $("#estrevenue").val(), Comments: $("#notes").val()
                };
                $.ajax({
                    type: "POST",
                    url: "/reports/save_client_record/",
                    data: JSON.stringify(row),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            //alert("Data saved successfully...");
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
                
                $("#dataTable").jqxDataTable('updateBoundData');
                
                $("#popupWindow").jqxWindow('hide');
            });
        });
    </script>
    <div id="tab-menu" align="left">
            <div id="-reports-client-report" class="light-grey">
                    <a href="/reports/client_report">Search</a>
            </div>
            <div id="-reports-client-data" class="light-grey selected">
                    <a href="/reports/client_data">Create/Update your records</a>
            </div>
    </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>    
<div id='jqxWidget'>
                    
        <div id="dataTable"></div>
            <div style='margin-top: 20px;'>
            <div style='float: right; padding-right: 15px;'>
                <input type="button" value="Add a new record" id='createNew' />
            </div>
        </div>
    </div>
    
    <div id="popupWindow">
        <div>Add a new record</div>
        <div style="overflow: hidden;">
        <form id="testForm" action="./">
            <table>
                <tr>
                    <td align="right"></td>
                    <td colspan="2" style="padding-bottom: 10px;" align="right"><input id="Cancel" type="button" value="Cancel" /></td>
                </tr>
                <tr>
                    <td align="right" style="width: 200px;">Region</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="region"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Entity</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="nameofentity"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">City</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="city"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Lead Agency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="agency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Advertiser Name</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="advertisername" autocomplete="off"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Parent Company</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="parentcompany"/></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Category</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="category"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Pitch Start</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstart"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Pitch Leader</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="pitchleader"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Stage</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="pitchstage"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Service</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="service"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Active Market</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="activemarket"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td align="left" style="padding-bottom: 5px;"><div id="currency"></div></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">iP estimated revenue</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="estrevenue"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right">Notes</td>
                    <td align="left" style="padding-bottom: 5px;"><input type="text" id="notes"/></td>
                    <td style="width: 150px"></td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td colspan="2" style="padding-top: 10px;" align="right"><input style="margin-right: 5px;" type="button" id="Save" value="Add to existing records" /></td>
                </tr>
            </table>
        </form>
        </div>
   </div>
