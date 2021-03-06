<?php

/**
 * DecodeWind.php
 *
 * PHP version 7.2
 *
 * @category Metar
 * @package  ReportDecoder\Decoders\MetarDecoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */

namespace ReportDecoder\Decoders\MetarDecoders;

use ReportDecoder\Decoders\Decoder;
use ReportDecoder\Decoders\DecoderInterface;
use ReportDecoder\Entity\EntityWind;
use ReportDecoder\Entity\Value;
use ReportDecoder\Exceptions\DecoderException;

/**
 * Decodes Wind chunk
 *
 * @category Metar
 * @package  ReportDecoder\Decoders\MetarDecoders
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
        $direction = '([0-9]{3}|VRB|\/\/\/)';
        $speed = 'P?([\/0-9]{2,3}|\/\/)';
        $speed_variations = '(GP?([0-9]{2,3}))?';
        $unit = '(KT|MPS|KPH)';
        $direction_variations = '( ([0-9]{3})V([0-9]{3}))?';

        return "/^$direction$speed$speed_variations$unit$direction_variations/";
    }

    /**
     * Parses the chunk using the expression
     * 
     * @param String        $report  Remaining report string
     * @param DecodedReport $decoded DecodedReport object
     * 
     * @throws DecoderException
     * 
     * @return Array
     */
    public function parse($report, &$decoded)
    {
        $result = $this->matchChunk($report);
        $match = $result['match'];
        $remaining_report = $result['report'];

        if (!$match) {
            throw new DecoderException(
                $report,
                $remaining_report,
                'Bad format for surface wind information',
                $this
            );
        }

        if ($match[1] == '///' && $match[2] == '//') {
            $tip = 'No information measured for surface wind';
        } else {
            // Get variable direction
            if (isset($match[6])) {
                $var_from = new Value(
                    Value::toInt($match[7]),
                    Value::UNIT_DEGREE
                );

                $var_to = new Value(
                    Value::toInt($match[8]),
                    Value::UNIT_DEGREE
                );
            } else {
                $var_from = null;
                $var_to = null;
            }

            // Get wind unit
            switch ($match[5]) {
                case 'KT':
                    $speed_unit = Value::UNIT_KNOT;
                    break;
                case 'KPH':
                    $speed_unit = Value::UNIT_KILOMETRE_PER_HOUR;
                    break;
                case 'MPS':
                    $speed_unit = Value::UNIT_METRE_PER_SECOND;
                    break;
            }

            // Get gust factor
            $gust_field = Value::toInt($match[4]);
            if (!empty($gust_field)) {
                $gust = new Value(
                    $gust_field,
                    $speed_unit
                );
            } else {
                $gust = null;
            }

            $decoded->setSurfaceWind(
                new EntityWind(
                    new Value(
                        $match[1],
                        Value::UNIT_DEGREE
                    ),
                    new Value(
                        Value::toInt($match[2]),
                        $speed_unit
                    ),
                    $gust,
                    $var_from,
                    $var_to
                )
            );

            $tip = 'Wind direction: ' . trim($match[1]) . '°, ';
            if (isset($match[6])) {
                $tip .= 'Variable from ' . $match[7] . '° to ' . $match[8] . '°, ';
            }
            $tip .= 'Wind speed: ' . Value::toInt($match[2]) . $match[5];
            if (!empty($gust_field)) {
                $tip .=  ', Wind gust: ' . $gust_field . $match[5];
            }
        }

        $result = [
            'text' => $match[0],
            'tip' => $tip
        ];

        return [
            'name' => 'wind',
            'result' => $result,
            'report' => $remaining_report
        ];
    }
}
