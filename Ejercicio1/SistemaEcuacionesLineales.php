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


//Ejemplo de uso

$ec1 = ['x' => 2, 'y' => 3, 'indepediente' => 5];
$ec2 = ['x' => 4, 'y' => 1, 'indepediente' => 6];

$sistema = new SistemaLineal($ec1, $ec2);
$solucion = $sistema -> resolverSistema();


echo "Sistema de ecuaciones:\n";
echo "Ecuacion 1: $ec1[x]x + $ec1[y]y = $ec1[indepediente]\n";
echo "Ecuacion 2: $ec2[x]x + $ec2[y]y = $ec2[indepediente]\n";

echo "Solucion del sistema de ecuaciones:\n";
echo "x= ".$solucion['x']. "\n";
echo "y= ".$solucion['y']. "\n";

?>