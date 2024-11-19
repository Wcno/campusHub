<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../php/helpers.php';

error_reporting(E_ALL);
session_start();
$userRole = $_SESSION['role'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);
