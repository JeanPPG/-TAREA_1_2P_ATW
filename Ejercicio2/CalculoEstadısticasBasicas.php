<?php

/**
 * Ejercicio 4: Calculo de estadısticas basicas
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

   /* function ordenar(&$array) {
        $n = count($array);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($array[$j] > $array[$j + 1]) {
                    // Intercambiar valores
                    $temp = $array[$j];
                    $array[$j] = $array[$j + 1];
                    $array[$j + 1] = $temp;
                }
            }
        }
        // Reindexar el array
        $array = array_values($array);
    }
    */

    public function calcularMediana(array $datos) : float {
        sort($datos, SORT_NUMERIC);
        $nDatos = count($datos);
        $mitad = (int) floor($nDatos / 2);
        if ($nDatos % 2 === 1) {
            //si es impar, devolvemos el valor del medio
            return $datos[$mitad];
        } else {
            //si es par, devolvemos la media de los dos del medio
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
            // Si no hay moda, devolvemos null
            return [];
        }

        return array_keys(array_filter($frecuencias, fn($f) => $f === $frecuenciaMaxima));
    }

    public function generarInforme(array $data):array{
        $informe = [];
        foreach ($data as $id => $datos) {
            if (empty($datos)) {
                $informe[$id] = [
                    'media' => null,
                    'mediana' => null,
                    'moda' => null
                ];
                continue;
            }
            $informe[$id] = [
                'media' => $this->calcularMedia($datos),
                'mediana' => $this->calcularMediana($datos),
                'moda' => $this->calcularModa($datos)
            ];
        }
        return $informe;
    }
}

// Ejemplo de uso

$data = [
    'conjunto1' => [1, 2, 3, 4, 5],
    'conjunto2' => [5, 5, 6, 7, 8],
    'conjunto3' => [10, 20, 30],
    'conjunto4' => [1, 1, 2, 2, 3],
    'conjunto5' => [] // Conjunto vacío
];


$estadistica = new EstadisticaBasica();

$reporte = $estadistica -> generarInforme($data);


print_r($reporte);


?>