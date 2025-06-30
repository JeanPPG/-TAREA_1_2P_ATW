<?php

/**
 * Ejercicio 4: Operaciones con matrices
 * Clase abstracta que define métodos para operaciones con matrices
 */
abstract class MatrizAbstracta
{
    protected $matriz;

    public function __construct($matriz)
    {
        $this->matriz = $matriz;
    }

    abstract public function multiplicar($matriz);
    abstract public function inversa();

    public function getMatriz()
    {
        return $this->matriz;
    }
}

/**
 * Clase concreta que implementa operaciones con matrices
 */
class Matriz extends MatrizAbstracta
{

    /**
     * Multiplica la matriz actual por otra matriz
     */
    public function multiplicar($matriz)
    {
        $filas1 = count($this->matriz);
        $columnas1 = count($this->matriz[0]);
        $filas2 = count($matriz);
        $columnas2 = count($matriz[0]);

        if ($columnas1 !== $filas2) {
            throw new Exception("No se pueden multiplicar las matrices: dimensiones incompatibles");
        }

        $resultado = [];
        for ($i = 0; $i < $filas1; $i++) {
            for ($j = 0; $j < $columnas2; $j++) {
                $resultado[$i][$j] = 0;
                for ($k = 0; $k < $columnas1; $k++) {
                    $resultado[$i][$j] += $this->matriz[$i][$k] * $matriz[$k][$j];
                }
            }
        }

        return $resultado;
    }

    /**
     * Calcula la matriz inversa usando eliminación de Gauss-Jordan
     */
    public function inversa()
    {
        $n = count($this->matriz);

        // Verificar que la matriz sea cuadrada
        if ($n !== count($this->matriz[0])) {
            throw new Exception("La matriz debe ser cuadrada para calcular la inversa");
        }

        // Crear matriz aumentada [A|I]
        $aumentada = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $aumentada[$i][$j] = $this->matriz[$i][$j];
            }
            for ($j = $n; $j < 2 * $n; $j++) {
                $aumentada[$i][$j] = ($j - $n == $i) ? 1 : 0;
            }
        }

        // Eliminación de Gauss-Jordan
        for ($i = 0; $i < $n; $i++) {
            // Buscar el pivote
            $pivote = $aumentada[$i][$i];
            if (abs($pivote) < 1e-10) {
                throw new Exception("La matriz no es invertible (determinante = 0)");
            }

            // Normalizar la fila del pivote
            for ($j = 0; $j < 2 * $n; $j++) {
                $aumentada[$i][$j] /= $pivote;
            }

            // Eliminar la columna
            for ($k = 0; $k < $n; $k++) {
                if ($k != $i) {
                    $factor = $aumentada[$k][$i];
                    for ($j = 0; $j < 2 * $n; $j++) {
                        $aumentada[$k][$j] -= $factor * $aumentada[$i][$j];
                    }
                }
            }
        }

        // Extraer la matriz inversa
        $inversa = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $inversa[$i][$j] = $aumentada[$i][$j + $n];
            }
        }

        return $inversa;
    }
}

/**
 * Función para calcular el determinante de una matriz
 */
function determinante($matriz)
{
    $n = count($matriz);

    if ($n !== count($matriz[0])) {
        throw new Exception("La matriz debe ser cuadrada para calcular el determinante");
    }

    if ($n === 1) {
        return $matriz[0][0];
    }

    if ($n === 2) {
        return $matriz[0][0] * $matriz[1][1] - $matriz[0][1] * $matriz[1][0];
    }

    $det = 0;
    for ($j = 0; $j < $n; $j++) {
        $submatriz = [];
        for ($i = 1; $i < $n; $i++) {
            $fila = [];
            for ($k = 0; $k < $n; $k++) {
                if ($k !== $j) {
                    $fila[] = $matriz[$i][$k];
                }
            }
            $submatriz[] = $fila;
        }

        $cofactor = pow(-1, $j) * $matriz[0][$j] * determinante($submatriz);
        $det += $cofactor;
    }

    return $det;
}

/**
 * Función para mostrar una matriz de forma legible
 */
function mostrarMatriz($matriz, $titulo = "Matriz")
{
    echo "\n$titulo:\n";
    foreach ($matriz as $fila) {
        echo "| ";
        foreach ($fila as $elemento) {
            printf("%8.3f ", $elemento);
        }
        echo "|\n";
    }
    echo "\n";
}

/**
 * Función para leer una matriz desde la consola
 */
function leerMatriz($filas, $columnas, $nombre = "matriz")
{
    $matriz = [];
    echo "Ingrese los elementos de la $nombre ($filas x $columnas):\n";

    for ($i = 0; $i < $filas; $i++) {
        for ($j = 0; $j < $columnas; $j++) {
            echo "Elemento [$i][$j]: ";
            $matriz[$i][$j] = (float)trim(fgets(STDIN));
        }
    }

    return $matriz;
}

// Programa principal
echo "=== EJERCICIO 4: OPERACIONES CON MATRICES ===\n";

echo "\nSeleccione una opción:\n";
echo "1. Multiplicación de matrices\n";
echo "2. Calcular inversa de una matriz\n";
echo "3. Calcular determinante\n";
echo "Opción: ";

$opcion = (int)trim(fgets(STDIN));

switch ($opcion) {
    case 1:
        echo "\nMULTIPLICACIÓN DE MATRICES\n";
        echo "Dimensiones de la primera matriz:\n";
        echo "Filas: ";
        $filas1 = (int)trim(fgets(STDIN));
        echo "Columnas: ";
        $columnas1 = (int)trim(fgets(STDIN));

        $matriz1_array = leerMatriz($filas1, $columnas1, "primera matriz");
        $matriz1 = new Matriz($matriz1_array);

        echo "\nDimensiones de la segunda matriz:\n";
        echo "Filas: ";
        $filas2 = (int)trim(fgets(STDIN));
        echo "Columnas: ";
        $columnas2 = (int)trim(fgets(STDIN));

        $matriz2_array = leerMatriz($filas2, $columnas2, "segunda matriz");

        mostrarMatriz($matriz1_array, "Primera Matriz");
        mostrarMatriz($matriz2_array, "Segunda Matriz");

        $resultado = $matriz1->multiplicar($matriz2_array);
        mostrarMatriz($resultado, "Resultado de la Multiplicación");
        break;

    case 2:
        echo "\nCÁLCULO DE MATRIZ INVERSA\n";
        echo "Tamaño de la matriz cuadrada: ";
        $n = (int)trim(fgets(STDIN));

        $matriz_array = leerMatriz($n, $n, "matriz");
        $matriz = new Matriz($matriz_array);

        mostrarMatriz($matriz_array, "Matriz Original");

        $inversa = $matriz->inversa();
        mostrarMatriz($inversa, "Matriz Inversa");

        // Verificación
        $verificacion = $matriz->multiplicar($inversa);
        mostrarMatriz($verificacion, "Verificación (A × A⁻¹)");
        break;

    case 3:
        echo "\nCÁLCULO DEL DETERMINANTE\n";
        echo "Tamaño de la matriz cuadrada: ";
        $n = (int)trim(fgets(STDIN));

        $matriz_array = leerMatriz($n, $n, "matriz");

        mostrarMatriz($matriz_array, "Matriz");

        $det = determinante($matriz_array);
        echo "Determinante: $det\n";
        break;



    default:
        echo "Opción no válida.\n";
}
