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

function truncateText($text, $limit, $ellipsis = "...")
{
  $words = explode(" ", $text);
  if (count($words) > $limit) {
    $truncated = array_slice($words, 0, $limit);
    return implode(" ", $truncated) . $ellipsis;
  }

  return $text;
}

function baseUrl(string $route = "/")
{
  $route = ltrim($route, "/");

  return rtrim(BASE_ROUTE, "/") . "/" . $route;
}

function currentRouteIncludes(string $searchString): bool
{
  $currentRoute = $_SERVER['REQUEST_URI'] ?? '/';

  return strpos($currentRoute, $searchString) !== false;
}
