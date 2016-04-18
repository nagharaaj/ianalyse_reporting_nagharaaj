    <script type="text/javascript">
         $(document).ready(function () {

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

<div id='jqxWidget'>
        <div id="dataTable"></div>
            <div style='margin-top: 20px;'>
        </div>
</div>
