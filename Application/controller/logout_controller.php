<?php
session_start();
session_destroy();
header('Location: ../estagio');
exit();