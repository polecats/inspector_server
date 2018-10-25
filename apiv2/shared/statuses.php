<?php
/**
 *
 * @author Dennis the menace
 */
// instantiate abstract class object (must be relative to the main caller class if not using __DIR__ .)
include_once __DIR__ . "/BasicEnum.php";

abstract class Status extends BasicEnum
{ 
    const NEW_ENTRY = 0;
    const ACTIVE = 1;
    const ARCHIVE = 2;
}

// SAMPLE USAGE
// Status::isValidName("newt");                  // false
// Status::isValidName("NEW");                   // true
// Status::isValidName("new");                   // true
// Status::isValidName("new", $strict = true);   // false
// Status::isValidName(0);                       // false

// Status::isValidValue(0);                      // true
// Status::isValidValue(2);                      // true
// Status::isValidValue(7);                      // false
// Status::isValidValue("ARCHIVE");              // false
?>