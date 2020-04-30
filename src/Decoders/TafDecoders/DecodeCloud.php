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
use ReportDecoder\Entity\Value;

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
        return '/^((NSC|NCD|CLR|SKC)|((VV|FEW|SCT|BKN|OVC)([0-9]{3})'
            . '(CB|TCU)?)( (VV|FEW|SCT|BKN|OVC)([0-9]{3})(CB|TCU)?)?'
            . '( (VV|FEW|SCT|BKN|OVC)([0-9]{3})(CB|TCU)?)?( (VV|FEW'
            . '|SCT|BKN|OVC)([0-9]{3})(CB|TCU)?)?)/';
    }

    /**
     * Parses the chunk using the expression
     * 
     * @param String        $report  Remaining report string
     * @param DecodedReport $decoded DecodedReport object
     * 
     * @return Array
     */
    public function parse($report, &$decoded, $edit_decoder = true)
    {
        $result = $this->matchChunk($report);
        $match = $result['match'];
        $report = $result['report'];

        if (!$match) {
            $result = null;
        } else {
            $match = array_map('trim', $match);

            $clouds = array();
            $tips = array();

            for ($i = 4; $i <= sizeof($match); $i += 3) {
                if (empty($match[$i])) {
                    continue;
                }

                $clouds[] = $match[$i] . $match[$i + 1];
                $tips[] = $match[$i] . ' ' . Value::toInt($match[$i + 1])
                    . '00ft AGL';

                ++$i;
            }

            if ($edit_decoder) {
                $decoded->setClouds($clouds);
            }
            $result = array(
                'text' => $clouds,
                'tip' => $tips
            );
        }

        return array(
            'name' => 'clouds',
            'result' => $result,
            'report' => $report,
        );
    }
}
