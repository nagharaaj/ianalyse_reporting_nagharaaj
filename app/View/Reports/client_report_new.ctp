<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.common.min.css" rel="stylesheet" />
<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.rtl.min.css" rel="stylesheet" />
<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.default.min.css" rel="stylesheet" />
<!--<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.uniform.mobile.min.css" rel="stylesheet" />-->
<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.dataviz.min.css" rel="stylesheet" />
<link href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.dataviz.uniform.min.css" rel="stylesheet" />

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://cdn.kendostatic.com/2014.3.1316/js/kendo.all.min.js"></script>
<script src="http://cdn.kendostatic.com/2014.3.1316/js/jszip.min.js"></script>
<script src="http://cdn.kendostatic.com/2014.3.1316/js/kendo.timezones.min.js"></script>


    <script type="text/javascript">
         /*$(document).ready(function () {

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
                sortable: true,
                filterable: true,
                filterMode: 'advanced',
                editable: false,
                autoRowHeight: false,
                columns: [
                  { text: 'RecordId', datafield: 'RecordId', hidden: true },
                  { text: 'Region', datafield: 'Region', width: 200, cellClassName: cellclass },
                  { text: 'Managing Entity', datafield: 'Country', width: 200, cellClassName: cellclass },
                  { text: 'Managing City', datafield: 'City', width: 200, cellClassName: cellclass },
                  { text: 'Lead Agency', datafield: 'LeadAgency', width: 200, cellClassName: cellclass },
                  { text: 'Client', columngroup: 'ClientName', datafield: 'ClientName', width: 250, cellClassName: cellclass },
                  { text: 'Parent Company', columngroup: 'ParentCompany', datafield: 'ParentCompany', width: 250, cellClassName: cellclass },
                  { text: 'Client Category', datafield: 'ClientCategory', width: 200, cellClassName: cellclass },
                  { text: 'Pitch Start', datafield: 'PitchStart', width: 100, cellClassName: cellclass },
                  { text: 'Pitch Leader', columngroup: 'PitchLeader', datafield: 'PitchLeader', width: 250, cellClassName: cellclass },
                  { text: 'Stage', columntype: 'template', datafield: 'PitchStage', width: 150, cellClassName: cellclass },
                  { text: 'Client Since Month', columntype: 'template', datafield: 'ClientMonth', width: 150, cellClassName: cellclass },
                  { text: 'Client Since Year', columntype: 'template', datafield: 'ClientYear', width: 150, cellClassName: cellclass },
                  { text: 'Lost (M-Y)', datafield: 'Lost', width: 100, cellClassName: cellclass },
                  { text: 'Service', datafield: 'Service', width: 200, cellClassName: cellclass },
                  { text: 'Active Markets', columngroup: 'ActiveMarkets', datafield: 'ActiveMarkets', width: 250, cellClassName: cellclass },
                  { text: 'Currency', datafield: 'Currency', width: 100, cellClassName: cellclass },
                  { text: 'Estimated Annual Revenue', columngroup: 'EstimatedRevenue', datafield: 'EstimatedRevenue', width: 200, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
                  { text: 'Actual Annual Revenue', columngroup: 'ActualRevenue', datafield: 'ActualRevenue', width: 200, align: 'right', cellsalign: 'right', cellClassName: cellclass, cellsFormat: 'f2' },
                  { text: 'Comments', columngroup: 'Comments', datafield: 'Comments', width: 250, cellClassName: cellclass }
                ]
            });
        });*/
        
        $(document).ready(function () {
            var dataSource = new kendo.data.DataSource({
                transport: {
                        read: {
                            url: "/reports/get_client_data/",
                            dataType: "json"
                        }
                },
                schema: {
                        model: {
                            id: "id",
                            fields: {
                                RecordId: { type: "string" },
                                Region: { type: "string" },
                                Country: { type: "string" },
                                City: { type: "string" },
                                LeadAgency: { type: "string" },
                                ClientName: { type: "string" },
                                ParentCompany: { type: "string" },
                                ClientCategory: { type: "string" },
                                PitchStart: { type: "date" },
                                PitchLeader: { type: "string" },
                                PitchStage: { type: "string" },
                                ClientMonth: { type: "string" },
                                ClientYear: { type: "string" },
                                Lost: { type: "date" },
                                Service: { type: "string" },
                                ActiveMarkets: { type: "string" },
                                Currency: { type: "string" },
                                EstimatedRevenue: { type: "number" },
                                ActualRevenue: { type: "number" },
                                Comments: { type: "string" }
                            }
                        }
                },
                pageSize: 20
            });

                $("#grid").kendoGrid({
                    toolbar: ["excel"],
                    excel: {
                        allPages: true,
                        fileName: "client_data.xlsx"
                    },
                    dataSource: dataSource,
                    pageable: {
                        buttonCount: 5,
                        pageSizes: [20, 50, 100],
                        info: false
                    },
                    sortable: true,
                    filterable: true,
                    columns: [
                        "RecordId",
                        { title: 'Region', field: 'Region', width: "200px" },
                        { title: 'Managing Entity', field: 'Country', width: "200px" },
                        { title: 'Managing City', field: 'City', width: "200px" },
                        { title: 'Lead Agency', field: 'LeadAgency', width: "200px" },
                        { title: 'Client', field: 'ClientName', width: "250px" },
                        { title: 'Parent Company', field: 'ParentCompany', width: "250px" },
                        { title: 'Client Category', field: 'ClientCategory', width: "200px" },
                        { title: 'Pitch Start', field: 'PitchStart', width: "100px" },
                        { title: 'Pitch Leader', field: 'PitchLeader', width: "250px" },
                        { title: 'Stage', field: 'PitchStage', width: "150px" },
                        { title: 'Client Since Month', field: 'ClientMonth', width: "150px" },
                        { title: 'Client Since Year', field: 'ClientYear', width: "150px" },
                        { title: 'Lost (M-Y)', field: 'Lost', width: "100px" },
                        { title: 'Service', field: 'Service', width: "200px" },
                        { title: 'Active Markets', field: 'ActiveMarkets', width: "250px" },
                        { title: 'Currency', field: 'Currency', width: "100px" },
                        { title: 'Estimated Annual Revenue', format: "{0:n}", field: 'EstimatedRevenue', width: "200px", attributes: { style: "text-align: right;" } },
                        { title: 'Actual Annual Revenue', format: "{0:n}", field: 'ActualRevenue', width: "200px", attributes: { style: "text-align: right;" } },
                        { title: 'Comments', field: 'Comments', width: "250px" }
                    ]
                });
        });
    </script>
    <div id="tab-menu" align="left">
            <div id="-reports-client-report" class="light-grey selected">
                    <a href="/reports/client_report">Search</a>
            </div>
            <div id="-reports-client-data" class="light-grey">
                    <a href="/reports/client_data">Create/Update your records</a>
            </div>
    </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#tab-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>    

<div id="example">
        <div id="grid"></div>
</div>