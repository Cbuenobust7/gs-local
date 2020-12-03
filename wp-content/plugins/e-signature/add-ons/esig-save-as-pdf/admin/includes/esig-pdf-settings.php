<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class esigPdfSetting {

    static function fontConfig() {
        $fontdata = [
            "dejavusanscondensed" => array(
                'R' => "DejaVuSansCondensed.ttf",
                'B' => "DejaVuSansCondensed-Bold.ttf",
                'I' => "DejaVuSansCondensed-Oblique.ttf",
                'BI' => "DejaVuSansCondensed-BoldOblique.ttf",
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ),
            "1" => array(
                'R' => "LaBelleAurore.ttf",
            ),
            "2" => array(
                'R' => "ShadowsIntoLight.ttf",
            ),
            "3" => array(
                'R' => "NothingYouCouldDo.ttf",
            ),
            "4" => array(
                'R' => "Zeyada.ttf",
            ),
            "5" => array(
                'R' => "DawningofaNewDay.ttf",
            ),
            "6" => array(
                'R' => "HerrVonMuellerhoff-Regular.ttf",
            ),
            "7" => array(
                'R' => "OvertheRainbow.ttf",
            )
        ];
        return $fontdata;
    }

}
