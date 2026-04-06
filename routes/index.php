<<<<<<< HEAD
<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/' => (new HomeController)->index(),
=======
<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController)->index(),
>>>>>>> 3801a107b199a547d599a7e4aa9e07da6c5f5fb3
};