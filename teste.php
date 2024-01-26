<?php
    session_start();
    include_once 'areaEscolas/manipuladorPauta.php';
    $m = new manipuladorPauta();

    $m->conDb("teste");
