<?php

/**
 * Ejercicio 5: Resolución de ecuaciones diferenciales
 * Clase abstracta que define el método para resolver ecuaciones diferenciales
 */
abstract class EcuacionDiferencial
{
    protected $condicionesIniciales;
    protected $parametros;

    public function __construct($condicionesIniciales, $parametros)
    {
        $this->condicionesIniciales = $condicionesIniciales;
        $this->parametros = $parametros;
    }

    abstract public function resolverEuler();
}

/**
 * Clase concreta que implementa el método de Euler para resolver ecuaciones diferenciales
 */
class EulerNumerico extends EcuacionDiferencial
{
    private $funcionCallback;

    public function setFuncion($callback)
    {
        $this->funcionCallback = $callback;
    }

    /**
     * Implementa el método de Euler para resolver la ecuación diferencial
     * dy/dx = f(x, y) con condición inicial y(x0) = y0
     */
    public function resolverEuler()
    {
        if (!$this->funcionCallback) {
            throw new Exception("No se ha definido la función de la ecuación diferencial");
        }

        $x0 = $this->condicionesIniciales['x0'];
        $y0 = $this->condicionesIniciales['y0'];
        $h = $this->parametros['h'];        // Tamaño del paso
        $xf = $this->parametros['xf'];      // Valor final de x

        $solucion = [];
        $x = $x0;
        $y = $y0;

        // Agregar el punto inicial
        $solucion[number_format($x, 6)] = $y;

        // Aplicar el método de Euler
        while ($x < $xf) {
            $dydx = call_user_func($this->funcionCallback, $x, $y);
            $y = $y + $h * $dydx;
            $x = $x + $h;

            // Evitar problemas de precisión de punto flotante
            if ($x <= $xf) {
                $solucion[number_format($x, 6)] = $y;
            }
        }

        return $solucion;
    }
}

/**
 * Función principal que aplica el método de Euler
 */
function aplicarMetodo($funcionCallback, $condicionesIniciales, $parametros)
{
    $euler = new EulerNumerico($condicionesIniciales, $parametros);
    $euler->setFuncion($funcionCallback);

    return $euler->resolverEuler();
}


/**
 * Función para mostrar la solución de forma tabular
 */
function mostrarSolucion($solucion, $titulo = "Solución de la Ecuación Diferencial")
{
    echo "\n=== $titulo ===\n";
    echo sprintf("%-12s | %-12s\n", "x", "y");
    echo str_repeat("-", 27) . "\n";

    foreach ($solucion as $x => $y) {
        echo sprintf("%-12.6f | %-12.6f\n", $x, $y);
    }
    echo "\n";
}

/**
 * Función para graficar la solución (representación ASCII)
 */
function graficarSolucion($solucion, $titulo = "Gráfica de la Solución")
{
    echo "\n=== $titulo ===\n";

    $valores_y = array_values($solucion);
    $min_y = min($valores_y);
    $max_y = max($valores_y);
    $rango_y = $max_y - $min_y;

    if ($rango_y == 0) {
        echo "Solución constante: y = " . $valores_y[0] . "\n";
        return;
    }

    $altura = 20; // Altura del gráfico en caracteres

    echo "y\n";
    echo "^\n";

    for ($i = $altura; $i >= 0; $i--) {
        $nivel_y = $min_y + ($rango_y * $i / $altura);
        printf("%8.3f |", $nivel_y);

        foreach ($solucion as $x => $y) {
            $pos_y = round(($y - $min_y) / $rango_y * $altura);
            if ($pos_y == $i) {
                echo "*";
            } else {
                echo " ";
            }
        }
        echo "\n";
    }

    echo "         +";
    echo str_repeat("-", count($solucion));
    echo "> x\n";
    echo "         ";

    $contador = 0;
    foreach ($solucion as $x => $y) {
        if ($contador % 5 == 0) {
            printf("%.1f", $x);
        }
        $contador++;
    }
    echo "\n";
}

/**
 * Función para crear una ecuación diferencial personalizada desde input del usuario
 */
function crearEcuacionPersonalizada()
{
    echo "\nDefina su ecuación diferencial dy/dx = f(x, y)\n";
    echo "Ingrese la función usando las variables \$x e \$y\n";
    echo "Ejemplos válidos:\n";
    echo "  \$x + \$y\n";
    echo "  \$x * \$y\n";
    echo "  \$x * \$x + \$y * \$y\n";
    echo "  2 * \$x\n";
    echo "  -\$y\n";
    echo "  sin(\$x) + cos(\$y)\n";
    echo "\nIngrese su función: dy/dx = ";

    $expresion = trim(fgets(STDIN));

    // Crear la función callback
    $funcionPersonalizada = function ($x, $y) use ($expresion) {
        // Reemplazar las variables en la expresión
        $codigo = str_replace(['$x', '$y'], [$x, $y], $expresion);

        // Evaluar la expresión de forma segura
        $resultado = null;
        eval('$resultado = ' . $codigo . ';');

        return $resultado;
    };

    return $funcionPersonalizada;
}

// Programa principal
echo "=== EJERCICIO 5: RESOLUCIÓN DE ECUACIONES DIFERENCIALES (MÉTODO DE EULER) ===\n";

echo "\nSeleccione una opción:\n";
echo "1. Resolver ecuación diferencial\n";
echo "2. Comparar diferentes tamaños de paso\n";
echo "Opción: ";

$opcion = (int)trim(fgets(STDIN));

switch ($opcion) {
    case 1:
        echo "\nRESOLUCIÓN DE ECUACIÓN DIFERENCIAL\n";

        // Definir la función
        $callback = crearEcuacionPersonalizada();

        // Leer condiciones iniciales
        echo "\nCondiciones iniciales:\n";
        echo "x0 (valor inicial de x): ";
        $x0 = (float)trim(fgets(STDIN));
        echo "y0 (valor inicial de y): ";
        $y0 = (float)trim(fgets(STDIN));

        // Leer parámetros
        echo "\nParámetros del método:\n";
        echo "h (tamaño del paso): ";
        $h = (float)trim(fgets(STDIN));
        echo "xf (valor final de x): ";
        $xf = (float)trim(fgets(STDIN));

        $condicionesIniciales = ['x0' => $x0, 'y0' => $y0];
        $parametros = ['h' => $h, 'xf' => $xf];

        echo "\nResolviendo la ecuación diferencial...\n";
        $solucion = aplicarMetodo($callback, $condicionesIniciales, $parametros);

        mostrarSolucion($solucion);
        graficarSolucion($solucion);
        break;

    case 2:
        echo "\nCOMPARACIÓN DE DIFERENTES TAMAÑOS DE PASO\n";

        // El usuario define la ecuación
        $callback = crearEcuacionPersonalizada();

        // Leer condiciones iniciales
        echo "\nCondiciones iniciales:\n";
        echo "x0 (valor inicial de x): ";
        $x0 = (float)trim(fgets(STDIN));
        echo "y0 (valor inicial de y): ";
        $y0 = (float)trim(fgets(STDIN));
        echo "xf (valor final de x): ";
        $xf = (float)trim(fgets(STDIN));

        $condicionesIniciales = ['x0' => $x0, 'y0' => $y0];

        // Leer los diferentes pasos a comparar
        echo "\n¿Cuántos tamaños de paso diferentes desea comparar? ";
        $numPasos = (int)trim(fgets(STDIN));

        $pasos = [];
        for ($i = 1; $i <= $numPasos; $i++) {
            echo "Ingrese el paso #$i: ";
            $pasos[] = (float)trim(fgets(STDIN));
        }

        foreach ($pasos as $h) {
            $parametros = ['h' => $h, 'xf' => $xf];
            $solucion = aplicarMetodo($callback, $condicionesIniciales, $parametros);

            echo "\n" . str_repeat("=", 40) . "\n";
            echo "RESULTADO CON PASO h = $h\n";
            echo "Puntos calculados: " . count($solucion) . "\n";
            echo "Solución final: y(" . $xf . ") ≈ " . number_format(end($solucion), 6) . "\n";

            // Mostrar la tabla completa si hay pocos puntos, sino solo algunos
            if (count($solucion) <= 10) {
                mostrarSolucion($solucion, "Solución Completa");
            } else {
                echo "\nAlgunos puntos de la solución:\n";
                echo sprintf("%-12s | %-12s\n", "x", "y");
                echo str_repeat("-", 27) . "\n";

                $contador = 0;
                $intervalo = max(1, intval(count($solucion) / 8));
                foreach ($solucion as $x => $y) {
                    if ($contador % $intervalo == 0 || $contador == count($solucion) - 1) {
                        echo sprintf("%-12.6f | %-12.6f\n", $x, $y);
                    }
                    $contador++;
                }
            }
        }
        break;

    default:
        echo "Opción no válida.\n";
}
