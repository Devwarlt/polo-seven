<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:26
 */

if (count($_POST, 0)) {
    header("Location:../index.html");
    return;
}

// to-do: dynamic request handlers