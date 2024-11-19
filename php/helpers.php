<?php

function loadComponent(string $name, array $data = [])
{
  $componentPath = __DIR__ . "/../views/{$name}.php";

  if (file_exists($componentPath)) {
    extract($data);
    include $componentPath;
  } else {
    echo "Component {$name} not found!";
    die();
  }
}
