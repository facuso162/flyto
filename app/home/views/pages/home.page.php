<?php

$vuelos = require __DIR__ . '/../../../shared/mocks/vuelos.mock.php';
$flight = $vuelos[0] ?? [];

require __DIR__ . '/../sections/hero.section.php';
require __DIR__ . '/../sections/latest-news.section.php';
require __DIR__ . '/../sections/how-to-book.section.php';
require __DIR__ . '/../sections/cta.section.php';
require __DIR__ . '/../sections/contact.section.php';
