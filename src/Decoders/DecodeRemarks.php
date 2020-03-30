<?php
namespace ReportDecoder\Decoders;

use ReportDecoder\Decoders\Decoder;

class DecodeRemarks extends Decoder {
    public function getExpression() {
        return '/RMK.*/';
    }

    public function parse($report, &$decoded) {
        $result = $this->match_chunk($report);
        $match = $result['match'];
        $report = $result['report'];
        
        if(!$match) {
            $result = null;
        } else {
            $decoded->setRemarks($match[0]);
            $result = $match[0];
        }

        return array(
            'name' => 'remarks',
            'result' => $result,
            'report' => $report,
        );
    }
}
?>