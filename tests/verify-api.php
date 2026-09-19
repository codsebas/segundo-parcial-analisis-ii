<?php
/**
 * ==============================================================================
 * Suite Automatizada de Pruebas de API y Contención (Standalone Runner)
 * Universidad Mariano Gálvez - Análisis de Sistemas II
 * 
 * Requisitos Evaluados:
 * - [RQF-01]: Creación de cita médica
 * - [RQF-03]: Detección de solapamiento / Doble reserva en servidor (HTTP 409)
 * - [RQF-05]: Cancelación preservando registro histórico en BD
 * - [RQF-06]: Filtros de citas por doctor y rango de fechas
 * - [RQF-07]: CRUD de citas y lectura de doctores/pacientes
 * - [RQF-08]: Validación de datos de entrada y contención de errores (HTTP 400)
 * - [RQNF-03]: Códigos HTTP correctos (200, 201, 400, 404, 409)
 * - [RQNF-04]: Arquitectura por capas (Controllers, Services, Repositories)
 * - [RQNF-07]: Validación en servidor
 * ==============================================================================
 */

echo "======================================================================\n";
echo " SUITE AUTOMATIZADA DE PRUEBAS DE API REST Y CONTENCIÓN (LARAVEL 12)  \n";
echo "======================================================================\n";
echo "Fecha y Hora: " . date('Y-m-d H:i:s') . "\n\n";

$cmd = "php artisan test --filter=CitasApiTest 2>&1";
exec($cmd, $output, $returnCode);

foreach ($output as $line) {
    echo $line . "\n";
}

echo "\n======================================================================\n";
if ($returnCode === 0) {
    echo "\033[32m>> RESULTADO: TODAS LAS PRUEBAS DE API Y CONTENCIÓN PASARON SATISFACTORIAMENTE (100%) <<\033[0m\n";
    exit(0);
} else {
    echo "\033[31m>> RESULTADO: SE DETECTARON FALLOS EN LA SUITE DE API <<\033[0m\n";
    exit(1);
}
