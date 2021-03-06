<?php

/**
 * DecodeCloud.php
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
use ReportDecoder\Entity\EntityCloud;
use ReportDecoder\Entity\Value;
use ReportDecoder\Exceptions\DecoderException;

/**
 * Decodes Cloud chunk
 *
 * @category Taf
 * @package  ReportDecoder\Decoders\TafDecoders
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
class DecodeCloud extends Decoder implements DecoderInterface
{
    /**
     * Returns the expression for matching the chunk
     * 
     * @return String
     */
    public function getExpression()
    {
        $no_cloud = "(NSC|NCD|CLR|SKC)";
        $layer = "(VV|FEW|SCT|BKN|OVC|\/\/\/)([0-9]{2,3}|\/\/\/)(CB|TCU|\/\/\/)?";
        return "/^($no_cloud|($layer)( $layer)?( $layer)?( $layer)?)( )/";
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
                'Bad format for cloud information',
                $this
            );
        }

        $match = array_map('trim', $match);

        if (
            $match[0] == 'SKC'
            || $match[0] == 'CLR'
            || $match[0] == 'NCD'
            || $match[0] == 'NSC'
        ) {
            $result = [
                'text' => 'SKC',
                'tip' => 'Sky clear'
            ];
        } else {
            $clouds = [];
            $clouds_text = [];
            $tips = [];

            for ($i = 4; $i <= sizeof($match); $i += 3) {
                if (empty($match[$i])) {
                    continue;
                }

                $altitude_value = new Value(
                    Value::toInt($match[$i + 1] . '00'),
                    Value::UNIT_FEET
                );

                $clouds[] = new EntityCloud(
                    $match[$i],
                    $altitude_value
                );
                $clouds_text[] = $match[$i] . $match[$i + 1];
                $tips[] = $match[$i] . ' ' . Value::toInt($match[$i + 1])
                    . '00ft AGL';

                ++$i;
            }

            $decoded->setClouds($clouds);

            $result = [
                'text' => $clouds_text,
                'tip' => $tips
            ];
        }

        return [
            'name' => 'clouds',
            'result' => $result,
            'report' => $remaining_report
        ];
    }
}
