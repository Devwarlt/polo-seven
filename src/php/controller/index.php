<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:25
 */

include "../PhpUtils.php";

use php\PhpUtils as phputils;

$utils = phputils::getSingleton();
$utils->onRawIndexErr("Invalid request!", "../../index.php");