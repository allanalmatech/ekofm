<?php
require_once __DIR__ . '/_init.php';
auth_logout();
redirect('admin/login.php');
