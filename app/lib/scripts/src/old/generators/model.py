

import os

def create_model(model_name: str):
    filename = os.path.basename(model_name)
    
    dirname = os.path.dirname(model_name)

    content = fr"""<?php

namespace App\Models{fr'\{dirname}' if dirname else ''};

include lib_models_url . 'Model.php';

use Lib\Systems\Models\Model;

class {filename}Model extends Model
{{
    protected $primary_key = '';
}}
"""

    directory_path = "app/models/" + dirname

    if directory_path and not os.path.exists(directory_path):
        os.makedirs(directory_path)

    full_path = 'app/models/' + model_name + 'Model.php'

    with open(full_path, 'w') as file:
        file.write(content)

    print(f'created {filename} at {full_path}')

