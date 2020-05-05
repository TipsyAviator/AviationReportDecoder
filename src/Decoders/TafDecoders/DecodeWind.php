<?php

/**
 * DecodeWind.php
 *
 * PHP version 7.2
 *
 * @category Taf
 * @package  ReportDecoder\Decoders\TafDecoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */

namespace ReportDecoder\Decoders\TafDecoders;

use ReportDecoder\Decoders\Decoder;
use ReportDecoder\Decoders\DecoderInterface;
use ReportDecoder\Entity\EntityWind;
use ReportDecoder\Entity\Value;

/**
 * Decodes Wind chunk
 *
 * @category Taf
 * @package  ReportDecoder\Decoders\TafDecoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
class DecodeWind extends Decoder implements DecoderInterface
{
    /**
     * Returns the expression for matching the chunk
     * 
     * @return String
     */
    public function getExpression()
    {
        return '/^([0-9]{3}|VRB)?( )?([0-9]{2,3})(G?([0-9]{2,3}))'
            . '?(KT|MPH|KPH)( ([0-9]{3})V([0-9]{3}))?/';
    }

    /**
     * Parses the chunk using the expression
     * 
     * @param String        $report  Remaining report string
     * @param DecodedReport $decoded DecodedReport object
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
            $decoded->setSurfaceWind(
                new EntityWind(
                    array(
                        'text' => $match[0],
                        'direction' => $match[1],
                        'speed' => Value::toInt($match[3]),
                        'gust' => Value::toInt($match[5]),
                        'unit' => $match[6],
                        'variable' => isset($match[7]),
                        'var_from' => isset($match[7]) ? $match[8] : 0,
                        'var_to' => isset($match[7]) ? $match[9] : 0
                    )
                )
            );

            $tip = 'Wind direction: ' . trim($match[1]) . '°, ';
            if (isset($match[7])) {
                $tip .= 'Variable from ' . $match[8] . '° to ' . $match[9] . '°, ';
            }
            $tip .= 'Wind speed: ' . Value::toInt($match[3]) . $match[6];
            if (!empty(Value::toInt($match[5]))) {
                $tip .=  ', Wind gust: ' . Value::toInt($match[5]) . $match[6];
            }

            $result = array(
                'text' => $match[0],
                'tip' => $tip
            );
        }

        return array(
            'name' => 'wind',
            'result' => $result,
            'report' => $report,
        );
    }
}
