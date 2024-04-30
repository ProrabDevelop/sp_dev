<?

namespace core\engine;

use FastRoute\RouteCollector;

class RouteMapper {
    private $map;
    private $routes;

    public function __construct($map) {
        $this->map = $map;
    }

    public function __invoke(RouteCollector $collector){
        $this->map($collector);
    }

    public function map(RouteCollector $collector){
        foreach ($this->getRoutes() as $row) {
            $collector->addRoute($row[0], $row[1], $row[2]);
        }
    }

    public function pathFor($name, array $vars = null){
        $pattern = false;
        foreach ($this->getRoutes() as $row) {
            if ($row[2]['do'] === $name) {
                $pattern = $row[1];
                break;
            }
        }
        if ($pattern && $vars) {
            foreach ($vars as $k => $v) {
                $pattern = preg_replace('~\{'.$k.'[^}]*\}~', $v, $pattern);
            }
        }
        return $pattern;
    }

    private function &getRoutes(){
        if (!isset($this->routes)) {
            // Route map is array or PHP filename returning array
            if (!is_array($this->map)) {
                $this->map = require($this->map);
            }
            $this->routes = [];
            $this->flatten($this->map, '', ['on' => 'GET']);
        }
        return $this->routes;
    }

    private function flatten(array $nested, $prefix = '', array $bag = []){
        foreach ($nested as $k => $v) {
            if ($k[0] === '/') {
                if (!is_array($v) || (array_keys($v) === [0, 1] && is_string($v[0]) && is_string($v[1]))) {
                    $this->add($prefix.$k, ['do' => $v] + $bag);
                } else {
                    $this->flatten($v, $prefix . $k, $bag);
                }
            } elseif (is_numeric($k) && is_array($v)) {
                $this->flatten($v, $prefix, $bag);
            } elseif ($k === 'do') {
                $this->add($prefix, ['do' => $v] + $bag);
            } else {
                $bag[$k] = $v;
            }
        }
    }

    private function add($key, array $value){
        $method = $value['on'];
        unset($value['on']);
        $this->routes[] = [$method, $key, $value];
    }
}
