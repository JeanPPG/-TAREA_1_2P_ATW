<?php

/**
 * Ejercicio 1: Sistema de ecuaciones lineales
 * Clase abstracta que define el método para resolver sistemas de ecuaciones lineales
 */
abstract class SistemaEcuaciones {
    abstract function calcularResultado(): array;
    abstract function validarConsistencia(): bool;
}

class SistemaLineal extends SistemaEcuaciones {
    private array $ec1;
    private array $ec2;

    public function __construct(array $ec1, array $ec2) {
        $this -> ec1 = $ec1;
        $this -> ec2 = $ec2;
    }

    //Obtenemos solamente las dos variables de la primera ecuacion
    private function obtenerVariables(): array {
        $claves = array_keys($this -> ec1);
        $vars = array_filter($claves, fn($key) => $key !== 'indepediente');
        if (count($vars) !== 2) {
            throw new \InvalidArgumentException("Se necesitan exactamente dos variables mas el termino independiente.");
        }
        return array_values($vars);
    }

    //Extrae a,b,c,d de ax + by = e, cx + dy = f
    private function extraerCoeficientes(): array {
        list($var1, $var2) = $this -> obtenerVariables();
        $a = $this -> ec1[$var1];
        $b = $this -> ec1[$var2];
        $c = $this -> ec2[$var1];
        $d = $this -> ec2[$var2];
        return[$a, $b, $c, $d];
    }

    //Comprueba qeu el determinante no sea cero
    public function validarConsistencia(): bool {
        list($a, $b, $c, $d) = $this -> extraerCoeficientes();
        // Determinante: ad - bc
        return ($a * $d - $b * $c) !== 0;
    }

    //Extrae coeficentes y nombre de variables para el calculo completo
    private function prepararCoeficientesYVariables(): array {
        list($var1, $var2) = $this -> obtenerVariables();
        $a = $this -> ec1[$var1];
        $b = $this -> ec1[$var2];
        $e = $this -> ec1['indepediente'];
        $c = $this -> ec2[$var1];
        $d = $this -> ec2[$var2];
        $f = $this -> ec2['indepediente'];
        return [$a, $b, $e, $c, $d, $f, $var1, $var2];
    }

    public function calcularResultado(): array {
        if (!$this -> validarConsistencia()) {
            throw new \Exception("El sistema de ecuaciones no es consistente.");
        }

        //Extraemos los coeficientes y las variables
        list($a, $b, $e, $c, $d, $f, $var1, $var2) = $this -> prepararCoeficientesYVariables();

        $det = $a * $d - $b * $c;

        //Formulas de Cramer (equivalente al metodo de sustitucion)
        $valor1 = ($e * $d - $b * $f) / $det;
        $valor2 = ($a * $f - $e * $c) / $det;

        return [
            $var1 => $valor1,
            $var2 => $valor2
        ];
    }

    //Metodo publico que invoca al calculo de la solucion
    public function resolverSistema(): array {
        return $this -> calcularResultado();
    }
}

/**
 * Menú principal de interacción por consola.
 * Si se ejecuta bajo CLI, muestra opciones y sale al elegir "2".
 */
if (php_sapi_name() === 'cli') {
    while (true) {
        echo "\n=== Menú Sistema de Ecuaciones 2×2 ===\n";
        echo "1) Resolver un sistema de dos ecuaciones\n";
        echo "2) Salir\n";
        echo "Seleccione una opción: ";
        $opt = trim(fgets(STDIN));

        if ($opt === '1') {
            // Pedir coeficientes de la primera ecuación
            echo "Ecuación 1:\n";
            echo "  Coeficiente de x: ";
            $x1 = (float) trim(fgets(STDIN));
            echo "  Coeficiente de y: ";
            $y1 = (float) trim(fgets(STDIN));
            echo "  Término independiente: ";
            $e1 = (float) trim(fgets(STDIN));

            // Pedir coeficientes de la segunda ecuación
            echo "Ecuación 2:\n";
            echo "  Coeficiente de x: ";
            $x2 = (float) trim(fgets(STDIN));
            echo "  Coeficiente de y: ";
            $y2 = (float) trim(fgets(STDIN));
            echo "  Término independiente: ";
            $e2 = (float) trim(fgets(STDIN));

            // Crear y resolver
            $ec1 = ['x' => $x1, 'y' => $y1, 'indepediente' => $e1];
            $ec2 = ['x' => $x2, 'y' => $y2, 'indepediente' => $e2];
            $sistema = new SistemaLineal($ec1, $ec2);

            try {
                $sol = $sistema->resolverSistema();
                echo "\nSolución:\n";
                echo "  x = {$sol['x']}\n";
                echo "  y = {$sol['y']}\n";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }

        } elseif ($opt === '2') {
            echo "Saliendo...\n";
            exit(0);
        } else {
            echo "Opción no válida, inténtalo de nuevo.\n";
        }
    }
}

?>
