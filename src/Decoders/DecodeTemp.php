<?php

/**
 * DecodeTemp.php
 *
 * PHP version 7.2
 *
 * @category Metar
 * @package  ReportDecoder\Decoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */

namespace ReportDecoder\Decoders;

use ReportDecoder\Decoders\Decoder;
use ReportDecoder\Entity\Value;

/**
 * Decodes Temperature chunk
 *
 * @category Metar
 * @package  ReportDecoder\Decoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
class DecodeTemp extends Decoder
{
    /**
     * Returns the expression for matching the chunk
     * 
     * @return String
     */
    public function getExpression()
    {
        return '/^(M?[0-9]{2})\/(M?[0-9]{2})?/';
    }

    /**
     * Parses the chunk using the expression
     * 
     * @param String       $report  Remaining report string
     * @param DecodedMetar $decoded DecodedMetar object
     * 
     * @return Array
     */
    public function parse($report, &$decoded)
    {
        $result = $this->matchChunk($report);
        $match = $result['match'];
        $report = $result['report'];

        if (!$match) {
            $result = null;
        } else {
            $decoded->setAirTemperature(Value::toInt($match[1]));
            $decoded->setDewPointTemperature(Value::toInt($match[2]));

            $result = array(
                'temp' => $match[0],
                'tip' => 'Temperature is ' . Value::toInt($match[1])
                    . ' and due point is ' . Value::toInt($match[2])
            );
        }

        return array(
            'name' => 'temp',
            'result' => $result,
            'report' => $report,
        );
    }
}
