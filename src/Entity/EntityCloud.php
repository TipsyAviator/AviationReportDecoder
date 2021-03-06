<?php

/**
 * EntityCloud.php
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
 * Cloud information
 *
 * @category Entity
 * @package  ReportDecoder\Entity
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
class EntityCloud
{

    private $_cloud_abbv = null;
    private $_cloud_type = null;
    private $_altitude = null;

    /**
     * Construct
     * 
     * @param String $cloud_abbv Abbreviation for the cloud type
     * @param Value  $altitude   Cloud altitude in feet
     */
    public function __construct($cloud_abbv, $altitude)
    {
        $this->_cloud_abbv = $cloud_abbv;
        $this->_altitude = $altitude;
        if (isset(Value::CLOUD_TEXT[$cloud_abbv])) {
            $this->_cloud_type = Value::CLOUD_TEXT[$cloud_abbv];
        }
    }

    /**
     * Gets the abbreviation
     * 
     * @return String
     */
    public function getAbbv()
    {
        return $this->_cloud_abbv;
    }

    /**
     * Gets the cloud type
     * 
     * @return String|Null
     */
    public function getType()
    {
        return $this->_cloud_type;
    }

    /**
     * Gets the cloud altitude
     * 
     * @return Value
     */
    public function getAltitude()
    {
        return $this->_altitude;
    }
}
