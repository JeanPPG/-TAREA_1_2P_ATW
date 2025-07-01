<?php

/**
 * Ejercicio 2: Calculo de estadísticas basicas
 * Clase abstracta que define métodos abstractos para calcular estadísticas básicas
 * como media, mediana y moda.
 */

abstract class Estadistica {
    abstract function calcularMedia(array $datos): float;
    abstract function calcularMediana(array $datos): float;
    abstract function calcularModa(array $datos): array;
}

class EstadisticaBasica extends Estadistica {
    public function calcularMedia(array $datos): float {
        return array_sum($datos) / count($datos);
    }

    public function calcularMediana(array $datos) : float {
        sort($datos, SORT_NUMERIC);
        $nDatos = count($datos);
        $mitad = (int) floor($nDatos / 2);
        if ($nDatos % 2 === 1) {
            return $datos[$mitad];
        } else {
            return ($datos[$mitad -1] + $datos[$mitad]) / 2;
        }
    }

    public function calcularModa(array $datos): array {
        $frecuencias = [];
        foreach ($datos as $valor) {
            $frecuencias[$valor] = ($frecuencias[$valor] ?? 0) + 1;
        }

        arsort($frecuencias);
        $frecuenciaMaxima = reset($frecuencias);

        if ($frecuenciaMaxima <= 1) {
            return [];
        }

        return array_keys(array_filter($frecuencias, fn($f) => $f === $frecuenciaMaxima));
    }

    public function generarInforme(array $data): array {
        $informe = [];
        foreach ($data as $id => $datos) {
            if (empty($datos)) {
                $informe[$id] = [
                    'media'   => null,
                    'mediana' => null,
                    'moda'    => []
                ];
            } else {
                $informe[$id] = [
                    'media'   => $this->calcularMedia($datos),
                    'mediana' => $this->calcularMediana($datos),
                    'moda'    => $this->calcularModa($datos)
                ];
            }
        }
        return $informe;
    }
}

// Función para leer con readline()
function leerLinea(string $prompt): string {
    $line = readline($prompt);
    if ($line !== false && function_exists('readline_add_history')) {
        readline_add_history($line);
    }
    return trim((string)$line);
}

// Menú principal
if (php_sapi_name() === 'cli') {
    $estad = new EstadisticaBasica();
    $dataSets = [];
    while (true) {
        echo "\n=== Menú Estadísticas Básicas ===\n";
        echo "1) Agregar/Actualizar un conjunto de datos\n";
        echo "2) Calcular estadísticas de un conjunto\n";
        echo "3) Generar informe de todos los conjuntos\n";
        echo "4) Salir\n";
        $opt = leerLinea("Selecciona opción: ");
        switch ($opt) {
            case '1':
                $id = leerLinea("Identificador del conjunto: ");
                $raw = leerLinea("Valores (separados por comas): ");
                $vals = array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
                $nums = array_map('floatval', $vals);
                $dataSets[$id] = $nums;
                echo "Conjunto '$id' guardado con " . count($nums) . " valores.\n";
                break;
            case '2':
                if (empty($dataSets)) {
                    echo "No hay conjuntos cargados. Añade uno primero.\n";
                    break;
                }
                $id = leerLinea("¿De qué conjunto quieres las estadísticas? ");
                if (!isset($dataSets[$id])) {
                    echo "Conjunto '$id' no existe.\n";
                    break;
                }
                $datos = $dataSets[$id];
                echo "\nEstadísticas para '$id':\n";
                echo "  Media:   " . $estad->calcularMedia($datos) . "\n";
                echo "  Mediana: " . $estad->calcularMediana($datos) . "\n";
                $moda = $estad->calcularModa($datos);
                echo "  Moda:    " . (empty($moda) ? '—' : implode(', ', $moda)) . "\n";
                break;
            case '3':
                if (empty($dataSets)) {
                    echo "No hay datos para generar informe.\n";
                    break;
                }
                $informe = $estad->generarInforme($dataSets);
                echo "\n=== Informe Completo ===\n";
                foreach ($informe as $id => $stats) {
                    echo "- $id:\n";
                    echo "    Media:   " . ($stats['media']   === null ? '—' : $stats['media']) . "\n";
                    echo "    Mediana: " . ($stats['mediana'] === null ? '—' : $stats['mediana']) . "\n";
                    $m = $stats['moda'];
                    echo "    Moda:    " . (empty($m) ? '—' : implode(', ', $m)) . "\n";
                }
                break;
            case '4':
                echo "¡Hasta luego!\n";
                exit(0);
            default:
                echo "Opción no válida. Intenta de nuevo.\n";
        }
    }
}

?>
