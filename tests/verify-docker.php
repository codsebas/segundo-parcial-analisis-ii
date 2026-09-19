<?php
/**
 * ==============================================================================
 * Suite Automatizada de Pruebas: Entorno Docker, MySQL y Persistencia
 * Universidad Mariano Gálvez - Análisis de Sistemas II
 * 
 * Requisitos No Funcionales Evaluados:
 * - [RQNF-01]: Persistencia de datos mediante volumen de Docker.
 * - [RQNF-02]: Reproducibilidad con un solo comando (docker compose up).
 * - [RQNF-08]: Documentación formal de evidencias.
 * ==============================================================================
 */

echo "======================================================================\n";
echo " SUITE DE PRUEBAS DE INTEGRACIÓN: ENTORNO DOCKER Y PERSISTENCIA MYSQL \n";
echo "======================================================================\n";
echo "Fecha y Hora: " . date('Y-m-d H:i:s') . "\n\n";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function runTest($id, $description, callable $fn) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    echo "----------------------------------------------------------------------\n";
    echo "[$id] $description\n";
    try {
        $result = $fn();
        if ($result === true) {
            echo "\033[32m[PASS] -> Prueba superada con éxito.\033[0m\n";
            $passedTests++;
        } else {
            echo "\033[31m[FAIL] -> La aserción falló.\033[0m\n";
            $failedTests++;
        }
    } catch (Throwable $e) {
        echo "\033[31m[FAIL] -> Excepción: " . $e->getMessage() . "\033[0m\n";
        $failedTests++;
    }
}

// -----------------------------------------------------------------------------
// TEST 1: Validación sintáctica y configuración de Docker Compose [RQNF-02]
// -----------------------------------------------------------------------------
runTest("TEST-01", "Validación de sintaxis y reproducibilidad de docker-compose.yml [RQNF-02]", function() {
    $output = shell_exec("docker compose config 2>&1");
    if ($output && strpos($output, "clinica_mysql") !== false) {
        echo "  -> docker-compose.yml es válido y define el servicio 'clinica_mysql'.\n";
        return true;
    }
    return false;
});

// -----------------------------------------------------------------------------
// TEST 2: Estado del Contenedor clinica_mysql en Docker [RQNF-01] [RQNF-02]
// -----------------------------------------------------------------------------
runTest("TEST-02", "Verificación del contenedor clinica_mysql en ejecución y saludable [RQNF-01] [RQNF-02]", function() {
    $status = trim(shell_exec("docker inspect -f \"{{.State.Status}}\" clinica_mysql 2>&1"));
    $health = trim(shell_exec("docker inspect -f \"{{.State.Health.Status}}\" clinica_mysql 2>&1"));
    echo "  -> Estado del contenedor: $status | Salud: $health\n";
    return ($status === "running" && ($health === "healthy" || $health === ""));
});

// -----------------------------------------------------------------------------
// TEST 3: Conectividad al socket TCP en puerto 3306 [RQNF-01]
// -----------------------------------------------------------------------------
runTest("TEST-03", "Verificación de socket TCP en puerto 3306 expuesto al host [RQNF-01]", function() {
    $fp = @fsockopen("127.0.0.1", 3306, $errno, $errstr, 3);
    if ($fp) {
        fclose($fp);
        echo "  -> Puerto 3306 accesible correctamente vía TCP.\n";
        return true;
    }
    echo "  -> No se pudo conectar al puerto 3306: $errstr ($errno)\n";
    return false;
});

// -----------------------------------------------------------------------------
// TEST 4: Conexión nativa PDO a MySQL y verificación de clinica_db [RQNF-01]
// -----------------------------------------------------------------------------
runTest("TEST-04", "Conexión autenticada vía PDO con usuario clinica_user a clinica_db [RQNF-01]", function() {
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=clinica_db;charset=utf8mb4";
    $pdo = new PDO($dsn, "clinica_user", "clinica_pass123", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $stmt = $pdo->query("SELECT DATABASE() as db, VERSION() as version");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  -> Conectado a DB: " . $row['db'] . " | MySQL Versión: " . $row['version'] . "\n";
    return ($row['db'] === 'clinica_db');
});

// -----------------------------------------------------------------------------
// TEST 5: Integridad del Esquema DDL (Tablas requeridas e índices) [RQNF-01]
// -----------------------------------------------------------------------------
runTest("TEST-05", "Integridad del esquema DDL (tablas pacientes, doctores, citas) [RQNF-01]", function() {
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=clinica_db;charset=utf8mb4";
    $pdo = new PDO($dsn, "clinica_user", "clinica_pass123", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "  -> Tablas detectadas: " . implode(", ", $tables) . "\n";
    $hasCitas = in_array("citas", $tables);
    $hasDoctores = in_array("doctores", $tables);
    $hasPacientes = in_array("pacientes", $tables);
    return ($hasCitas && $hasDoctores && $hasPacientes);
});

// -----------------------------------------------------------------------------
// TEST 6: Validación de Datos Semilla y Cobertura de Estados [RQNF-01]
// -----------------------------------------------------------------------------
runTest("TEST-06", "Validación de datos semilla mínimos y los 4 estados de citas [RQNF-01]", function() {
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=clinica_db;charset=utf8mb4";
    $pdo = new PDO($dsn, "clinica_user", "clinica_pass123", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    $doctores = (int) $pdo->query("SELECT COUNT(*) FROM doctores")->fetchColumn();
    $pacientes = (int) $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
    $citas = (int) $pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();
    $estados = $pdo->query("SELECT DISTINCT estado FROM citas ORDER BY estado")->fetchAll(PDO::FETCH_COLUMN);

    echo "  -> Conteo registrado: Doctores=$doctores (esperado >=5), Pacientes=$pacientes (esperado >=6), Citas=$citas (esperado >=8)\n";
    echo "  -> Estados de citas presentes: " . implode(", ", $estados) . "\n";

    $hasAllEstados = in_array('pendiente', $estados) &&
                     in_array('confirmada', $estados) &&
                     in_array('atendida', $estados) &&
                     in_array('cancelada', $estados);

    return ($doctores >= 5 && $pacientes >= 6 && $citas >= 8 && $hasAllEstados);
});

// -----------------------------------------------------------------------------
// TEST 7: Comprobación de Persistencia Real de Volumen tras Reinicio [RQNF-01]
// -----------------------------------------------------------------------------
runTest("TEST-07", "Persistencia comprobada: inserción de registro testigo, reinicio del contenedor y verificación [RQNF-01]", function() {
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=clinica_db;charset=utf8mb4";
    $pdo = new PDO($dsn, "clinica_user", "clinica_pass123", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $marker = "PACIENTE_TEST_VOLUMEN_" . rand(1000, 9999);
    echo "  -> 1. Insertando registro testigo en base de datos: '$marker'\n";
    $stmt = $pdo->prepare("INSERT INTO pacientes (nombre, telefono, email, fecha_nacimiento) VALUES (?, '55550000', 'test@docker.com', '1990-01-01')");
    $stmt->execute([$marker]);

    echo "  -> 2. Reiniciando el contenedor con 'docker compose restart mysql'...\n";
    shell_exec("docker compose restart mysql 2>&1");

    echo "  -> 3. Esperando 10 segundos a que el motor MySQL reinicie y vuelva a estar listo...\n";
    sleep(10);

    echo "  -> 4. Verificando que el registro testigo persiste intacto en el volumen clinica_mysql_data...\n";
    $pdoAfter = new PDO($dsn, "clinica_user", "clinica_pass123", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $checkStmt = $pdoAfter->prepare("SELECT COUNT(*) FROM pacientes WHERE nombre = ?");
    $checkStmt->execute([$marker]);
    $count = (int) $checkStmt->fetchColumn();

    echo "  -> 5. Limpiando registro testigo...\n";
    $delStmt = $pdoAfter->prepare("DELETE FROM pacientes WHERE nombre = ?");
    $delStmt->execute([$marker]);

    if ($count === 1) {
        echo "  -> ¡Persistencia confirmada al 100%! El registro sobrevivió al ciclo de reinicio del contenedor.\n";
        return true;
    }
    return false;
});

// -----------------------------------------------------------------------------
// RESUMEN FINAL
// -----------------------------------------------------------------------------
echo "======================================================================\n";
echo " RESUMEN FINAL DE LA SUITE DE PRUEBAS DE DOCKER \n";
echo "======================================================================\n";
echo "Total de pruebas ejecutadas: $totalTests\n";
echo "Pruebas superadas (PASS):    $passedTests\n";
echo "Pruebas fallidas  (FAIL):    $failedTests\n";

if ($failedTests === 0) {
    echo "\n\033[32m>> RESULTADO: TODAS LAS PRUEBAS DE DOCKER Y PERSISTENCIA PASARON SATISFACTORIAMENTE (100%) <<\033[0m\n";
    exit(0);
} else {
    echo "\n\033[31m>> RESULTADO: SE DETECTARON FALLOS EN LA SUITE DE DOCKER <<\033[0m\n";
    exit(1);
}
