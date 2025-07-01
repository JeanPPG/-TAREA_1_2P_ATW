<?php
/**
 * Ejercicio 3: Manejo de polinomios
 * Clase abstracta que define métodos abstractos para evaluar un polinomio en un punto x y calcular su derivada.
 */

abstract class PolinomioAbstracto {

    protected array $terminos;

    abstract function evaluar($x): float;
    abstract function derivada(): PolinomioAbstracto;

    public function __construct(array $terminos) {
        $this -> terminos = [];
        foreach ($terminos as $grado => $coeficiente) {
            $g = (int) $grado;
            $c = (float) $coeficiente;
            if ($c !== 0.0) {
                $this -> terminos[$g] = $c;
            }
        }
        krsort($this -> terminos);
    }

    public function getTerminos(): array {
        return $this -> terminos;
    }
}

class Polinomio extends PolinomioAbstracto {
    public function evaluar($x): float {
        $resultado = 0.0;
        foreach ($this -> terminos as $grado => $coeficiente) {
            $resultado += $coeficiente * ($x ** $grado);
        }
        return $resultado;
    }

    public function derivada(): PolinomioAbstracto
    {
        $derivados = [];
        foreach ($this->terminos as $grado => $coef) {
            if ($grado > 0) {
                // derivada de coef·x^grado es (coef*grado)·x^(grado−1)
                $derivados[$grado - 1] = $coef * $grado;
            }
        }
        return new Polinomio($derivados);
    }

     public static function sumarPolinomios(array $p1, array $p2): array
    {
        $suma = $p1;
        foreach ($p2 as $grado => $coef) {
            $g = (int)$grado;
            $c = (float)$coef;
            if (isset($suma[$g])) {
                $suma[$g] += $c;
            } else {
                $suma[$g] = $c;
            }
            // si la suma da cero, lo eliminamos
            if ($suma[$g] === 0.0) {
                unset($suma[$g]);
            }
        }
        // opcional: ordenar de mayor a menor grado
        krsort($suma);
        return $suma;
    }
}

// Definimos dos polinomios:
//   P(x) = 3x^3 − 2x + 5    como [3=>3, 1=>-2, 0=>5]
//   Q(x) =   x^2 + 4x + 1    como [2=>1, 1=>4, 0=>1]
$p = new Polinomio([3 => 3, 1 => -2, 0 => 5]);
$q = new Polinomio([2 => 1, 1 => 4, 0 => 1]);

// 1) Evaluar P(2) y Q(2)
echo "P(2) = " . $p->evaluar(2) . "\n"; // 3·8 − 2·2 + 5 = 24 − 4 + 5 = 25
echo "Q(2) = " . $q->evaluar(2) . "\n"; //    4 + 8 + 1 = 13

// 2) Derivadas
$dp = $p->derivada(); //  P'(x) = 9x^2 − 2
$dq = $q->derivada(); //  Q'(x) = 2x + 4
print_r($dp->getTerminos()); // [2=>9, 0=>-2]
print_r($dq->getTerminos()); // [1=>2, 0=>4]

// 3) Suma de polinomios como arrays
$sumArray = Polinomio::sumarPolinomios(
    $p->getTerminos(),
    $q->getTerminos()
);
// Resultado: 3x^3 + x^2 + (−2+4)x + (5+1)
print_r($sumArray); // [3=>3, 2=>1, 1=>2, 0=>6]

?>