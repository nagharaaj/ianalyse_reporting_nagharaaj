<?php

class GoogleSpreadsheet extends AppModel {
        public $name = 'GoogleSpreadsheet';
        // set datasource
        public $useDbConfig = 'gsheet';
        // $useTable is name of sheet,
        // required if your model name is other than
        // yout sheet name
        public $useTable = 'Sheet1';
        // one column in your sheet with unique
        // values is required, default name is 'id',
        // you can change it by
        public $primaryKey = 'client';

}
