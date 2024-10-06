

import os

def create_controller(controller_name: str):
    filename = os.path.basename(controller_name)
    
    dirname = os.path.dirname(controller_name)

    content = fr"""<?php

namespace App\Controllers{fr'\{dirname}' if dirname else ''};

include lib_controllers_url . 'Controller.php';

use Lib\Systems\Controllers\Controller;

class {filename}Controller extends Controller
{{
    public function index()
    {{
    }}
}}
"""

    directory_path = "app/controllers/" + dirname

    if directory_path and not os.path.exists(directory_path):
        os.makedirs(directory_path)

    full_path = 'app/controllers/' + controller_name + 'Controller.php'

    with open(full_path, 'w') as file:
        file.write(content)

    print(f'created {filename} at {full_path}')

