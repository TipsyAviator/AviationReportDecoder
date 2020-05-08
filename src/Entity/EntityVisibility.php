<?php

/**
 * EntityVisibility.php
 *
 * PHP version 7.2
 *
 * @category Entity
 * @package  ReportDecoder\Entity
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */

namespace ReportDecoder\Entity;

/**
 * Visibility information
 *
 * @category Entity
 * @package  ReportDecoder\Entity
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
class EntityVisibility
{
    private $_distance = null;
    private $_unit = null;

    /**
     * Construct
     * 
     * @param Int $distance Distance
     * @param Int $unit     Distance unit
     */
    public function __construct($distance, $unit)
    {
        $this->_distance = $distance;
        $this->_unit = $unit;
    }

    /**
     * Gets the distance
     * 
     * @return Int
     */
    public function getDistance()
    {
        return $this->_distance;
    }

    /**
     * Gets the unit
     * 
     * @return String
     */
    public function getUnit()
    {
        return $this->_unit;
    }
}
