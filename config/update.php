<?php

return [

    'update' => [
        /*
         *
         * Directories in these folders will not be inserted into the generated json file
         * meaning, that they won't be touched when backup:run is called
         *
         */
        'ignore_dirs' => [
            'storage/',
        ],

        /*
         *
         * These files will be ignored and are therefore not deleted.
         *
         */
        'ignore_files' => [
            '.env',
        ],
    ]
];