<?php

/**
 * TypeDecoder.php
 *
 * PHP version 7.2
 *
 * @category ReportTypes
 * @package  ReportDecoder\ReportTypes
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */

namespace ReportDecoder\ReportTypes;

use ReportDecoder\Exceptions\DecoderException;

/**
 * Includes the decoder chain for decoding a report string
 *
 * @category ReportTypes
 * @package  ReportDecoder\ReportTypes
 * @author   Jamie Thirkell <jamie@jamieco.ca>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html  GNU v3.0
 * @link     https://github.com/TipsyAviator/AviationReportDecoder
 */
abstract class TypeDecoder
{
    protected $decoder = null;
    protected $decoded_report = null;

    /**
     * Consume a chunk
     * 
     * @param String $report Report to decode
     * 
     * @return DecodedReport
     */
    public function consume($report)
    {
        foreach ($this->decoder as $chunk) {
            try {
                $parse_attempt = $this->tryParsing(
                    $chunk,
                    $report,
                    $this->decoded_report
                );

                if (is_null($parse_attempt) || is_null($parse_attempt['result'])) {
                    continue;
                }

                $this->decoded_report->addReportChunk($parse_attempt['result']);
                $report = $parse_attempt['report'];

                if (empty($parse_attempt['report'])) {
                    break;
                }
            } catch (DecoderException $ex) {
                $this->decoded_report->addDecodingException($ex);

                $report = $ex->getRemaining();
            }
        }

        return $this->decoded_report;
    }

    /**
     * Attempt to parse the report
     * 
     * @param Decoder $chunk  Chunk to try on report
     * @param String  $report The report to parse
     * 
     * @return Array 
     */
    protected function tryParsing($chunk, $report, &$decoded)
    {
        try {
            $parse_attempt = $chunk->parse($report, $decoded);
        } catch (DecoderException $primary_exception) {
            try {
                $alternative = $this->_consumeOneChunk($report);
                $parse_attempt = $chunk->parse($alternative, $decoded);
                $decoded->addDecodingException($primary_exception);
            } catch (DecoderException $secondary_exception) {
                throw $primary_exception;
            }
        }

        return $parse_attempt;
    }

    /**
     * Consume one chunk without parsing
     * 
     * @param String $report The report to consume
     * 
     * @return String
     */
    private static function _consumeOneChunk($report)
    {
        $next_space = strpos($report, ' ');
        if ($next_space > 0) {
            return substr($report, $next_space + 1);
        } else {
            return $report;
        }
    }
}
