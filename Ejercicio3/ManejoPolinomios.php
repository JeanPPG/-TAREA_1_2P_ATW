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
if (php_sapi_name() === 'cli') {
    while (true) {
        echo "\n===Menu: Manejo de Polinomios ===\n";
        echo "1) Evaluar polinomios\n";
        echo "2) Derivar polinomios\n";
        echo "3) Sumar polinomios\n";
        echo "4) Salir\n";
        $opt = readline("Seleccione una opción: ");

        switch ($opt) {
            case '1':

                echo "Ingrese el grado y coeficiente del polinomio P (ejemplo: 3=>2, 1=>-1, 0=>5): ";
                $inputP = readline();
                $terminosP = [];
                foreach (explode(',', $inputP) as $term) {
                    list($grado, $coeficiente) = explode('=>', trim($term));
                    $terminosP[(int)$grado] = (float)$coeficiente;
                }
                $p = new Polinomio($terminosP);
                $x = readline("Ingrese el valor de x para evaluar P(x): ");
                echo "P($x) = " . $p->evaluar((float)$x) . "\n";
                break;

            case '2':

                echo "Ingrese el grado y coeficiente del polinomio P (ejemplo: 3=>2, 1=>-1, 0=>5): ";
                $inputP = readline();
                $terminosP = [];
                foreach (explode(',', $inputP) as $term) {
                    list($grado, $coeficiente) = explode('=>', trim($term));
                    $terminosP[(int)$grado] = (float)$coeficiente;
                }
                $p = new Polinomio($terminosP);
                $derivadaP = $p->derivada();
                echo "La derivada de P(x) es: ";
                foreach ($derivadaP->getTerminos() as $grado => $coeficiente) {
                    echo ($coeficiente >= 0 ? '+' : '') . $coeficiente . 'x^' . $grado . ' ';
                }

                break;

            case '3':

                echo "Ingrese el grado y coeficiente del polinomio P (ejemplo: 3=>2, 1=>-1, 0=>5): ";
                $inputP = readline();
                $terminosP = [];
                foreach (explode(',', $inputP) as $term) {
                    list($grado, $coeficiente) = explode('=>', trim($term));
                    $terminosP[(int)$grado] = (float)$coeficiente;
                }

                $p = new Polinomio($terminosP);

                echo "Ingrese el grado y coeficiente del polinomio Q (ejemplo: 2=>1, 1=>4, 0=>1): ";
                $inputQ = readline();
                $terminosQ = [];
                foreach (explode(',', $inputQ) as $term) {
                    list($grado, $coeficiente) = explode('=>', trim($term));
                    $terminosQ[(int)$grado] = (float)$coeficiente;
                }

                $q = new Polinomio($terminosQ);
                $sumaPQ = Polinomio::sumarPolinomios(
                    $p->getTerminos(),
                    $q->getTerminos()
                );

                print_r($sumaPQ);
                break;

            case '4':
                echo "Saliendo...\n";
                exit(0);
            
            default:
                echo "Opción no válida. Inténtalo de nuevo.\n";
                break;
        }
    }
}
// Definimos dos polinomios:
//   P(x) = 3x^3 − 2x + 5    como [3=>3, 1=>-2, 0=>5]
//   Q(x) =   x^2 + 4x + 1    como [2=>1, 1=>4, 0=>1]

?>