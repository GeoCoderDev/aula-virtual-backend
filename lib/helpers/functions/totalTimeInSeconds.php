<?php
function totalTimeInSeconds($dias, $horas, $minutos, $segundos) {
    // Convierte los días, horas, minutos y segundos en segundos
    $segundosTotales = 0;
    $segundosTotales += $dias * 86400; // 86400 segundos en un día (24 horas * 60 minutos * 60 segundos)
    $segundosTotales += $horas * 3600; // 3600 segundos en una hora (60 minutos * 60 segundos)
    $segundosTotales += $minutos * 60; // 60 segundos en un minuto

    // Agrega los segundos adicionales
    $segundosTotales += $segundos;

    return $segundosTotales;
}
