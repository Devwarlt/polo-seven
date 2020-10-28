<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:35
 */

include "../PhpUtils.php";

use php\PhpUtils as phputils;

$utils = phputils::getSingleton();
$utils->onRawIndexErr("Invalid request!", "../../index.php");