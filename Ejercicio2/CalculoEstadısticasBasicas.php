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

// Menú principal usando readline() directamente
if (php_sapi_name() === 'cli') {
    $estad = new EstadisticaBasica();
    $dataSets = [];

    while (true) {
        echo "\n=== Menú Estadísticas Básicas ===\n";
        echo "1) Agregar/Actualizar un conjunto de datos\n";
        echo "2) Calcular estadísticas de un conjunto\n";
        echo "3) Generar informe de todos los conjuntos\n";
        echo "4) Salir\n";

        $opt = readline("Selecciona opción: ");
        if ($opt !== false && function_exists('readline_add_history')) readline_add_history($opt);

        switch (trim($opt)) {
            case '1':
                $id  = readline("Identificador del conjunto: "); if ($id !== false) readline_add_history($id);
                $raw = readline("Valores (separados por comas): "); if ($raw !== false) readline_add_history($raw);
                $vals = array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
                $dataSets[$id] = array_map('floatval', $vals);
                echo "Conjunto '$id' guardado con " . count($dataSets[$id]) . " valores.\n";
                break;

            case '2':
                if (empty($dataSets)) {
                    echo "No hay conjuntos cargados. Añade uno primero.\n";
                    break;
                }
                $id = readline("¿De qué conjunto quieres las estadísticas? "); if ($id !== false) readline_add_history($id);
                if (!isset($dataSets[$id])) {
                    echo "Conjunto '$id' no existe.\n";
                    break;
                }
                $stats = $estad->generarInforme([$id => $dataSets[$id]]); 
                $s = $stats[$id];
                echo "\nEstadísticas para '$id':\n";
                echo "  Media:   " . ($s['media']   ?? '—') . "\n";
                echo "  Mediana: " . ($s['mediana'] ?? '—') . "\n";
                $m = $s['moda'];
                echo "  Moda:    " . (empty($m) ? '—' : implode(', ', $m)) . "\n";
                break;

            case '3':
                if (empty($dataSets)) {
                    echo "No hay datos para generar informe.\n";
                    break;
                }
                $informe = $estad->generarInforme($dataSets);
                echo "\n=== Informe Completo ===\n";
                foreach ($informe as $id => $s) {
                    echo "- $id:\n";
                    echo "    Media:   " . ($s['media']   ?? '—') . "\n";
                    echo "    Mediana: " . ($s['mediana'] ?? '—') . "\n";
                    echo "    Moda:    " . (empty($s['moda']) ? '—' : implode(', ', $s['moda'])) . "\n";
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
