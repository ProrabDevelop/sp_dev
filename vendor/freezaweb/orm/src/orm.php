<?

    class ORM implements ArrayAccess {

        // WHERE and HAVING condition array keys
        const CONDITION_FRAGMENT = 0;
        const CONDITION_VALUES = 1;

        const DEFAULT_CONNECTION = 'default';

        // Limit clause styles
        const LIMIT_STYLE_TOP_N = "top";
        const LIMIT_STYLE_LIMIT = "limit";

        // Class configuration
        protected static $_default_config = array(
            'connection_string' => 'sqlite::memory:',
            'id_column' => 'id',
            'id_column_overrides' => array(),
            'error_mode' => PDO::ERRMODE_EXCEPTION,
            'username' => null,
            'password' => null,
            'driver_options' => null,
            'identifier_quote_character' => null, // if this is null, will be autodetected
            'limit_clause_style' => null, // if this is null, will be autodetected
            'logging' => false,
            'logger' => null,
            'caching' => false,
            'caching_auto_clear' => false,
            'return_result_sets' => false,
        );

        // Map of configuration settings
        protected static $_config = array();

        // Map of database connections, instances of the PDO class
        protected static $_db = array();

        // Last query run, only populated if logging is enabled
        protected static $_last_query;

        // Log of all queries run, mapped by connection key, only populated if logging is enabled
        protected static $_query_log = array();

        // Query cache, only used if query caching is enabled
        protected static $_query_cache = array();

        // Reference to previously used PDOStatement object to enable low-level access, if needed
        protected static $_last_statement = null;

        // Key name of the connections in self::$_db used by this instance
        protected $_connection_name;

        // The name of the table the current ORM instance is associated with
        protected $_table_name;

        // Alias for the table to be used in SELECT queries
        protected $_table_alias = null;

        // Values to be bound to the query
        protected $_values = array();

        // Columns to select in the result
        protected $_result_columns = array('*');

        // Are we using the default result column or have these been manually changed?
        protected $_using_default_result_columns = true;

        // Join sources
        protected $_join_sources = array();

        // Should the query include a DISTINCT keyword?
        protected $_distinct = false;

        // Is this a raw query?
        protected $_is_raw_query = false;

        // The raw query
        protected $_raw_query = '';

        // The raw query parameters
        protected $_raw_parameters = array();

        // Array of WHERE clauses
        protected $_where_conditions = array();

        // LIMIT
        protected $_limit = null;

        // OFFSET
        protected $_offset = null;

        // ORDER BY
        protected $_order_by = array();

        // GROUP BY
        protected $_group_by = array();

        // HAVING
        protected $_having_conditions = array();

        // The data for a hydrated instance of the class
        public $_data = array();

        // Fields that have been modified during the
        // lifetime of the object
        protected $_dirty_fields = array();

        // Fields that are to be inserted in the DB raw
        protected $_expr_fields = array();

        // Is this a new object (has create() been called)?
        protected $_is_new = false;

        // Name of the column to use as the primary key for
        // this instance only. Overrides the config settings.
        protected $_instance_id_column = null;

        public static function configure($key, $value = null, $connection_name = self::DEFAULT_CONNECTION) {
            self::_setup_db_config($connection_name); //ensures at least default config is set

            if (is_array($key)) {
                // Shortcut: If only one array argument is passed,
                // assume it's an array of configuration settings
                foreach ($key as $conf_key => $conf_value) {
                    self::configure($conf_key, $conf_value, $connection_name);
                }
            } else {
                if (is_null($value)) {
                    // Shortcut: If only one string argument is passed, 
                    // assume it's a connection string
                    $value = $key;
                    $key = 'connection_string';
                }
                self::$_config[$connection_name][$key] = $value;
            }
        }

        public static function get_config($key = null, $connection_name = self::DEFAULT_CONNECTION) {
            if ($key) {
                return self::$_config[$connection_name][$key];
            } else {
                return self::$_config[$connection_name];
            }
        }

        public static function reset_config() {
            self::$_config = array();
        }

        public static function for_table($table_name, $connection_name = self::DEFAULT_CONNECTION) {
            self::_setup_db($connection_name);
            return new self($table_name, array(), $connection_name);
        }

        protected static function _setup_db($connection_name = self::DEFAULT_CONNECTION) {
            if (!array_key_exists($connection_name, self::$_db) ||
                !is_object(self::$_db[$connection_name])) {
                self::_setup_db_config($connection_name);

                $db = new PDO(
                    self::$_config[$connection_name]['connection_string'],
                    self::$_config[$connection_name]['username'],
                    self::$_config[$connection_name]['password'],
                    self::$_config[$connection_name]['driver_options']
                );

                $db->setAttribute(PDO::ATTR_ERRMODE, self::$_config[$connection_name]['error_mode']);
                self::set_db($db, $connection_name);
            }
        }

        protected static function _setup_db_config($connection_name) {
            if (!array_key_exists($connection_name, self::$_config)) {
                self::$_config[$connection_name] = self::$_default_config;
            }
        }

        public static function set_db($db, $connection_name = self::DEFAULT_CONNECTION) {
            self::_setup_db_config($connection_name);
            self::$_db[$connection_name] = $db;
            if(!is_null(self::$_db[$connection_name])) {
                self::_setup_identifier_quote_character($connection_name);
                self::_setup_limit_clause_style($connection_name);
            }
        }

        public static function reset_db() {
            self::$_db = array();
        }

        protected static function _setup_identifier_quote_character($connection_name) {
            if (is_null(self::$_config[$connection_name]['identifier_quote_character'])) {
                self::$_config[$connection_name]['identifier_quote_character'] =
                    self::_detect_identifier_quote_character($connection_name);
            }
        }

        public static function _setup_limit_clause_style($connection_name) {
            if (is_null(self::$_config[$connection_name]['limit_clause_style'])) {
                self::$_config[$connection_name]['limit_clause_style'] =
                    self::_detect_limit_clause_style($connection_name);
            }
        }

        protected static function _detect_identifier_quote_character($connection_name) {
            switch(self::get_db($connection_name)->getAttribute(PDO::ATTR_DRIVER_NAME)) {
                case 'pgsql':
                case 'sqlsrv':
                case 'dblib':
                case 'mssql':
                case 'sybase':
                case 'firebird':
                    return '"';
                case 'mysql':
                case 'sqlite':
                case 'sqlite2':
                default:
                    return '`';
            }
        }

        protected static function _detect_limit_clause_style($connection_name) {
            switch(self::get_db($connection_name)->getAttribute(PDO::ATTR_DRIVER_NAME)) {
                case 'sqlsrv':
                case 'dblib':
                case 'mssql':
                    return ORM::LIMIT_STYLE_TOP_N;
                default:
                    return ORM::LIMIT_STYLE_LIMIT;
            }
        }

        public static function get_db($connection_name = self::DEFAULT_CONNECTION) {
            self::_setup_db($connection_name); // required in case this is called before Idiorm is instantiated
            return self::$_db[$connection_name];
        }

        public static function raw_execute($query, $parameters = array(), $connection_name = self::DEFAULT_CONNECTION) {
            self::_setup_db($connection_name);
            return self::_execute($query, $parameters, $connection_name);
        }

        public static function get_last_statement() {
            return self::$_last_statement;
        }

        protected static function _execute($query, $parameters = array(), $connection_name = self::DEFAULT_CONNECTION) {
            $statement = self::get_db($connection_name)->prepare($query);
            self::$_last_statement = $statement;
            $time = microtime(true);

            foreach ($parameters as $key => &$param) {
                if (is_null($param)) {
                    $type = PDO::PARAM_NULL;
                } else if (is_bool($param)) {
                    $type = PDO::PARAM_BOOL;
                } else if (is_int($param)) {
                    $type = PDO::PARAM_INT;
                } else {
                    $type = PDO::PARAM_STR;
                }

                $statement->bindParam(is_int($key) ? ++$key : $key, $param, $type);
            }

            $q = $statement->execute();

            self::_log_query($query, $parameters, $connection_name, (microtime(true)-$time));

            return $q;
        }

        protected static function _log_query($query, $parameters, $connection_name, $query_time) {
            // If logging is not enabled, do nothing
            if (!self::$_config[$connection_name]['logging']) {
                return false;
            }

            if (!isset(self::$_query_log[$connection_name])) {
                self::$_query_log[$connection_name] = array();
            }

            if (empty($parameters)) {
                $bound_query = $query;
            } else {
                // Escape the parameters
                $parameters = array_map(array(self::get_db($connection_name), 'quote'), $parameters);

                if (array_values($parameters) === $parameters) {
                    // ? placeholders
                    // Avoid %format collision for vsprintf
                    $query = str_replace("%", "%%", $query);

                    // Replace placeholders in the query for vsprintf
                    if(false !== strpos($query, "'") || false !== strpos($query, '"')) {
                        $query = ORM_String::str_replace_outside_quotes("?", "%s", $query);
                    } else {
                        $query = str_replace("?", "%s", $query);
                    }

                    // Replace the question marks in the query with the parameters
                    $bound_query = vsprintf($query, $parameters);
                } else {
                    // named placeholders
                    foreach ($parameters as $key => $val) {
                        $query = str_replace($key, $val, $query);
                    }
                    $bound_query = $query;
                }
            }

            self::$_last_query = $bound_query;
            self::$_query_log[$connection_name][] = $bound_query;
            
            
            if(is_callable(self::$_config[$connection_name]['logger'])){
                $logger = self::$_config[$connection_name]['logger'];
                $logger($bound_query, $query_time);
            }
            
            return true;
        }

        public static function get_last_query($connection_name = null) {
            if ($connection_name === null) {
                return self::$_last_query;
            }
            if (!isset(self::$_query_log[$connection_name])) {
                return '';
            }

            return end(self::$_query_log[$connection_name]);
        }

        public static function get_query_log($connection_name = self::DEFAULT_CONNECTION) {
            if (isset(self::$_query_log[$connection_name])) {
                return self::$_query_log[$connection_name];
            }
            return array();
        }

        public static function get_connection_names() {
            return array_keys(self::$_db);
        }

        protected function __construct($table_name, $data = array(), $connection_name = self::DEFAULT_CONNECTION) {
            $this->_table_name = $table_name;
            $this->_data = $data;

            $this->_connection_name = $connection_name;
            self::_setup_db_config($connection_name);
        }

        public function create($data=null) {
            $this->_is_new = true;
            if (!is_null($data)) {
                return $this->hydrate($data)->force_all_dirty();
            }
            return $this;
        }

        public function use_id_column($id_column) {
            $this->_instance_id_column = $id_column;
            return $this;
        }

        protected function _create_instance_from_row($row) {
            $instance = self::for_table($this->_table_name, $this->_connection_name);
            $instance->use_id_column($this->_instance_id_column);
            $instance->hydrate($row);
            return $instance;
        }

        public function find_one($id=null) {
            if (!is_null($id)) {
                $this->where_id_is($id);
            }
            $this->limit(1);
            $rows = $this->_run();

            if (empty($rows)) {
                return false;
            }

            return $this->_create_instance_from_row($rows[0]);
        }

        public function find_many() {
            if(self::$_config[$this->_connection_name]['return_result_sets']) {
                return $this->find_result_set();
            }
            return $this->_find_many();
        }

        protected function _find_many() {
            $rows = $this->_run();
            return array_map(array($this, '_create_instance_from_row'), $rows);
        }

        public function find_result_set() {
            return new ORM_ResultSet($this->_find_many());
        }

        public function find_array() {
            return $this->_run();
        }

        public function find_array_by_col($col_name) {



            return $this->_run_by_col($col_name);
        }

        public function count($column = '*') {
            return $this->_call_aggregate_db_function(__FUNCTION__, $column);
        }

        public function max($column)  {
            return $this->_call_aggregate_db_function(__FUNCTION__, $column);
        }

        public function min($column)  {
            return $this->_call_aggregate_db_function(__FUNCTION__, $column);
        }

        public function avg($column)  {
            return $this->_call_aggregate_db_function(__FUNCTION__, $column);
        }

        public function sum($column)  {
            return $this->_call_aggregate_db_function(__FUNCTION__, $column);
        }

        protected function _call_aggregate_db_function($sql_function, $column) {
            $alias = strtolower($sql_function);
            $sql_function = strtoupper($sql_function);
            if('*' != $column) {
                $column = $this->_quote_identifier($column);
            }
            $result_columns = $this->_result_columns;
            $this->_result_columns = array();
            $this->select_expr("$sql_function($column)", $alias);
            $result = $this->find_one();
            $this->_result_columns = $result_columns;

            $return_value = 0;
            if($result !== false && isset($result->$alias)) {
                if (!is_numeric($result->$alias)) {
                    $return_value = $result->$alias;
                }
                elseif((int) $result->$alias == (float) $result->$alias) {
                    $return_value = (int) $result->$alias;
                } else {
                    $return_value = (float) $result->$alias;
                }
            }
            return $return_value;
        }

        public function hydrate($data=array()) {
            $this->_data = $data;
            return $this;
        }

        public function force_all_dirty() {
            $this->_dirty_fields = $this->_data;
            return $this;
        }

        public function raw_query($query, $parameters = array()) {
            $this->_is_raw_query = true;
            $this->_raw_query = $query;
            $this->_raw_parameters = $parameters;
            return $this;
        }

        public function table_alias($alias) {
            $this->_table_alias = $alias;
            return $this;
        }

        protected function _add_result_column($expr, $alias=null) {
            if (!is_null($alias)) {
                $expr .= " AS " . $this->_quote_identifier($alias);
            }

            if ($this->_using_default_result_columns) {
                $this->_result_columns = array($expr);
                $this->_using_default_result_columns = false;
            } else {
                $this->_result_columns[] = $expr;
            }
            return $this;
        }

        public function count_null_id_columns() {
            if (is_array($this->_get_id_column_name())) {
                return count(array_filter($this->id(), 'is_null'));
            } else {
                return is_null($this->id()) ? 1 : 0;
            }
        }

        public function select($column, $alias=null) {
            $column = $this->_quote_identifier($column);
            return $this->_add_result_column($column, $alias);
        }

        public function select_expr($expr, $alias=null) {
            return $this->_add_result_column($expr, $alias);
        }

        public function select_many() {
            $columns = func_get_args();
            if(!empty($columns)) {
                $columns = $this->_normalise_select_many_columns($columns);
                foreach($columns as $alias => $column) {
                    if(is_numeric($alias)) {
                        $alias = null;
                    }
                    $this->select($column, $alias);
                }
            }
            return $this;
        }

        public function select_many_expr() {
            $columns = func_get_args();
            if(!empty($columns)) {
                $columns = $this->_normalise_select_many_columns($columns);
                foreach($columns as $alias => $column) {
                    if(is_numeric($alias)) {
                        $alias = null;
                    }
                    $this->select_expr($column, $alias);
                }
            }
            return $this;
        }

        protected function _normalise_select_many_columns($columns) {
            $return = array();
            foreach($columns as $column) {
                if(is_array($column)) {
                    foreach($column as $key => $value) {
                        if(!is_numeric($key)) {
                            $return[$key] = $value;
                        } else {
                            $return[] = $value;
                        }
                    }
                } else {
                    $return[] = $column;
                }
            }
            return $return;
        }

        public function distinct() {
            $this->_distinct = true;
            return $this;
        }

        protected function _add_join_source($join_operator, $table, $constraint, $table_alias=null) {

            $join_operator = trim("{$join_operator} JOIN");

            $table = $this->_quote_identifier($table);

            // Add table alias if present
            if (!is_null($table_alias)) {
                $table_alias = $this->_quote_identifier($table_alias);
                $table .= " {$table_alias}";
            }

            // Build the constraint
            if (is_array($constraint)) {
                list($first_column, $operator, $second_column) = $constraint;
                $first_column = $this->_quote_identifier($first_column);
                $second_column = $this->_quote_identifier($second_column);
                $constraint = "{$first_column} {$operator} {$second_column}";
            }

            $this->_join_sources[] = "{$join_operator} {$table} ON {$constraint}";
            return $this;
        }

        public function raw_join($table, $constraint, $table_alias, $parameters = array()) {
            // Add table alias if present
            if (!is_null($table_alias)) {
                $table_alias = $this->_quote_identifier($table_alias);
                $table .= " {$table_alias}";
            }

            $this->_values = array_merge($this->_values, $parameters);

            // Build the constraint
            if (is_array($constraint)) {
                list($first_column, $operator, $second_column) = $constraint;
                $first_column = $this->_quote_identifier($first_column);
                $second_column = $this->_quote_identifier($second_column);
                $constraint = "{$first_column} {$operator} {$second_column}";
            }

            $this->_join_sources[] = "{$table} ON {$constraint}";
            return $this;
        }

        public function join($table, $constraint, $table_alias=null) {
            return $this->_add_join_source("", $table, $constraint, $table_alias);
        }

        public function inner_join($table, $constraint, $table_alias=null) {
            return $this->_add_join_source("INNER", $table, $constraint, $table_alias);
        }

        public function left_outer_join($table, $constraint, $table_alias=null) {
            return $this->_add_join_source("LEFT OUTER", $table, $constraint, $table_alias);
        }

        public function right_outer_join($table, $constraint, $table_alias=null) {
            return $this->_add_join_source("RIGHT OUTER", $table, $constraint, $table_alias);
        }

        public function full_outer_join($table, $constraint, $table_alias=null) {
            return $this->_add_join_source("FULL OUTER", $table, $constraint, $table_alias);
        }

        protected function _add_having($fragment, $values=array()) {
            return $this->_add_condition('having', $fragment, $values);
        }

        protected function _add_simple_having($column_name, $separator, $value) {
            return $this->_add_simple_condition('having', $column_name, $separator, $value);
        }

        public function _add_having_placeholder($column_name, $separator, $values) {
            if (!is_array($column_name)) {
                $data = array($column_name => $values);
            } else {
                $data = $column_name;
            }
            $result = $this;
            foreach ($data as $key => $val) {
                $column = $result->_quote_identifier($key);
                $placeholders = $result->_create_placeholders($val);
                $result = $result->_add_having("{$column} {$separator} ({$placeholders})", $val);    
            }
            return $result;
        }

        public function _add_having_no_value($column_name, $operator) {
            $conditions = (is_array($column_name)) ? $column_name : array($column_name);
            $result = $this;
            foreach($conditions as $column) {
                $column = $this->_quote_identifier($column);
                $result = $result->_add_having("{$column} {$operator}");
            }
            return $result;
        }

        protected function _add_where($fragment, $values=array()) {
            return $this->_add_condition('where', $fragment, $values);
        }

        protected function _add_simple_where($column_name, $separator, $value) {
            return $this->_add_simple_condition('where', $column_name, $separator, $value);
        }

        public function _add_where_placeholder($column_name, $separator, $values) {
            if (!is_array($column_name)) {
                $data = array($column_name => $values);
            } else {
                $data = $column_name;
            }
            $result = $this;
            foreach ($data as $key => $val) {
                $column = $result->_quote_identifier($key);
                $placeholders = $result->_create_placeholders($val);
                $result = $result->_add_where("{$column} {$separator} ({$placeholders})", $val);    
            }
            return $result;
        }

        public function _add_where_no_value($column_name, $operator) {
            $conditions = (is_array($column_name)) ? $column_name : array($column_name);
            $result = $this;
            foreach($conditions as $column) {
                $column = $this->_quote_identifier($column);
                $result = $result->_add_where("{$column} {$operator}");
            }
            return $result;
        }

        protected function _add_condition($type, $fragment, $values=array()) {
            $conditions_class_property_name = "_{$type}_conditions";
            if (!is_array($values)) {
                $values = array($values);
            }
            array_push($this->$conditions_class_property_name, array(
                self::CONDITION_FRAGMENT => $fragment,
                self::CONDITION_VALUES => $values,
            ));
            return $this;
        }

        protected function _add_simple_condition($type, $column_name, $separator, $value) {
            $multiple = is_array($column_name) ? $column_name : array($column_name => $value);
            $result = $this;

            foreach($multiple as $key => $val) {
                // Add the table name in case of ambiguous columns
                if (count($result->_join_sources) > 0 && strpos($key, '.') === false) {
                    $table = $result->_table_name;
                    if (!is_null($result->_table_alias)) {
                        $table = $result->_table_alias;
                    }

                    $key = "{$table}.{$key}";
                }
                $key = $result->_quote_identifier($key);
                $result = $result->_add_condition($type, "{$key} {$separator} ?", $val);
            }
            return $result;
        } 

        protected function _create_placeholders($fields) {
            if(!empty($fields)) {
                $db_fields = array();
                foreach($fields as $key => $value) {
                    // Process expression fields directly into the query
                    if(array_key_exists($key, $this->_expr_fields)) {
                        $db_fields[] = $value;
                    } else {
                        $db_fields[] = '?';
                    }
                }
                return implode(', ', $db_fields);
            }
        }

        protected function _get_compound_id_column_values($value) {
            $filtered = array();
            foreach($this->_get_id_column_name() as $key) {
                $filtered[$key] = isset($value[$key]) ? $value[$key] : null;
            }
            return $filtered;
        }

        protected function _get_compound_id_column_values_array($values) {
            $filtered = array();
            foreach($values as $value) {
                $filtered[] = $this->_get_compound_id_column_values($value);
            }
            return $filtered;
        }

        public function where($column_name, $value=null) {
            return $this->where_equal($column_name, $value);
        }

        public function where_equal($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '=', $value);
        }

        public function where_not_equal($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '!=', $value);
        }

        public function where_id_is($id) {
            return (is_array($this->_get_id_column_name())) ?
                $this->where($this->_get_compound_id_column_values($id), null) :
                $this->where($this->_get_id_column_name(), $id);
        }

        public function where_any_is($values, $operator='=') {
            $data = array();
            $query = array("((");
            $first = true;
            foreach ($values as $value) {
                if ($first) {
                    $first = false;
                } else {
                    $query[] = ") OR (";
                }
                $firstsub = true;
                foreach($value as $key => $item) {
                    $op = is_string($operator) ? $operator : (isset($operator[$key]) ? $operator[$key] : '=');
                    if ($firstsub) {
                        $firstsub = false;
                    } else {
                        $query[] = "AND";
                    }
                    $query[] = $this->_quote_identifier($key);
                    $data[] = $item;
                    $query[] = $op . " ?";
                }
            }
            $query[] = "))";
            return $this->where_raw(join($query, ' '), $data);
        }

        public function where_id_in($ids) {
            return (is_array($this->_get_id_column_name())) ?
                $this->where_any_is($this->_get_compound_id_column_values_array($ids)) :
                $this->where_in($this->_get_id_column_name(), $ids);
        }

        public function where_like($column_name, $value=null) {
            return $this->_add_simple_where($column_name, 'LIKE', $value);
        }

        public function where_not_like($column_name, $value=null) {
            return $this->_add_simple_where($column_name, 'NOT LIKE', $value);
        }

        public function where_gt($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '>', $value);
        }

        public function where_lt($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '<', $value);
        }

        public function where_gte($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '>=', $value);
        }

        public function where_lte($column_name, $value=null) {
            return $this->_add_simple_where($column_name, '<=', $value);
        }

        public function where_in($column_name, $values) {
            return $this->_add_where_placeholder($column_name, 'IN', $values);
        }

        public function where_not_in($column_name, $values) {
            return $this->_add_where_placeholder($column_name, 'NOT IN', $values);
        }

        public function where_null($column_name) {
            return $this->_add_where_no_value($column_name, "IS NULL");
        }

        public function where_not_null($column_name) {
            return $this->_add_where_no_value($column_name, "IS NOT NULL");
        }

        public function where_raw($clause, $parameters=array()) {
            return $this->_add_where($clause, $parameters);
        }

        public function limit($limit) {
            $this->_limit = $limit;
            return $this;
        }

        public function offset($offset) {
            $this->_offset = $offset;
            return $this;
        }

        protected function _add_order_by($column_name, $ordering) {
            $column_name = $this->_quote_identifier($column_name);
            $this->_order_by[] = "{$column_name} {$ordering}";
            return $this;
        }

        public function order_by_desc($column_name) {
            return $this->_add_order_by($column_name, 'DESC');
        }

        public function order_by_asc($column_name) {
            return $this->_add_order_by($column_name, 'ASC');
        }

        public function order_by_expr($clause) {
            $this->_order_by[] = $clause;
            return $this;
        }

        public function group_by($column_name) {
            $column_name = $this->_quote_identifier($column_name);
            $this->_group_by[] = $column_name;
            return $this;
        }

        public function group_by_expr($expr) {
            $this->_group_by[] = $expr;
            return $this;
        }

        public function having($column_name, $value=null) {
            return $this->having_equal($column_name, $value);
        }

        public function having_equal($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '=', $value);
        }

        public function having_not_equal($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '!=', $value);
        }

        public function having_id_is($id) {
            return (is_array($this->_get_id_column_name())) ?
                $this->having($this->_get_compound_id_column_values($id), null) :
                $this->having($this->_get_id_column_name(), $id);
        }

        public function having_like($column_name, $value=null) {
            return $this->_add_simple_having($column_name, 'LIKE', $value);
        }

        public function having_not_like($column_name, $value=null) {
            return $this->_add_simple_having($column_name, 'NOT LIKE', $value);
        }

        public function having_gt($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '>', $value);
        }

        public function having_lt($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '<', $value);
        }

        public function having_gte($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '>=', $value);
        }

        public function having_lte($column_name, $value=null) {
            return $this->_add_simple_having($column_name, '<=', $value);
        }

        public function having_in($column_name, $values=null) {
            return $this->_add_having_placeholder($column_name, 'IN', $values);
        }

        public function having_not_in($column_name, $values=null) {
            return $this->_add_having_placeholder($column_name, 'NOT IN', $values);
        }

        public function having_null($column_name) {
            return $this->_add_having_no_value($column_name, 'IS NULL');
        }

        public function having_not_null($column_name) {
            return $this->_add_having_no_value($column_name, 'IS NOT NULL');
        }

        public function having_raw($clause, $parameters=array()) {
            return $this->_add_having($clause, $parameters);
        }

        protected function _build_select() {
            // If the query is raw, just set the $this->_values to be
            // the raw query parameters and return the raw query
            if ($this->_is_raw_query) {
                $this->_values = $this->_raw_parameters;
                return $this->_raw_query;
            }

            // Build and return the full SELECT statement by concatenating
            // the results of calling each separate builder method.
            return $this->_join_if_not_empty(" ", array(
                $this->_build_select_start(),
                $this->_build_join(),
                $this->_build_where(),
                $this->_build_group_by(),
                $this->_build_having(),
                $this->_build_order_by(),
                $this->_build_limit(),
                $this->_build_offset(),
            ));
        }

        protected function _build_select_start() {
            $fragment = 'SELECT ';
            $result_columns = join(', ', $this->_result_columns);

            if (!is_null($this->_limit) &&
                self::$_config[$this->_connection_name]['limit_clause_style'] === ORM::LIMIT_STYLE_TOP_N) {
                $fragment .= "TOP {$this->_limit} ";
            }

            if ($this->_distinct) {
                $result_columns = 'DISTINCT ' . $result_columns;
            }

            $fragment .= "{$result_columns} FROM " . $this->_quote_identifier($this->_table_name);

            if (!is_null($this->_table_alias)) {
                $fragment .= " " . $this->_quote_identifier($this->_table_alias);
            }
            return $fragment;
        }

        protected function _build_join() {
            if (count($this->_join_sources) === 0) {
                return '';
            }

            return join(" ", $this->_join_sources);
        }

        protected function _build_where() {
            return $this->_build_conditions('where');
        }

        protected function _build_having() {
            return $this->_build_conditions('having');
        }

        protected function _build_group_by() {
            if (count($this->_group_by) === 0) {
                return '';
            }
            return "GROUP BY " . join(", ", $this->_group_by);
        }

        protected function _build_conditions($type) {
            $conditions_class_property_name = "_{$type}_conditions";
            // If there are no clauses, return empty string
            if (count($this->$conditions_class_property_name) === 0) {
                return '';
            }

            $conditions = array();
            foreach ($this->$conditions_class_property_name as $condition) {
                $conditions[] = $condition[self::CONDITION_FRAGMENT];
                $this->_values = array_merge($this->_values, $condition[self::CONDITION_VALUES]);
            }

            return strtoupper($type) . " " . join(" AND ", $conditions);
        }

        protected function _build_order_by() {
            if (count($this->_order_by) === 0) {
                return '';
            }
            return "ORDER BY " . join(", ", $this->_order_by);
        }

        protected function _build_limit() {
            $fragment = '';
            if (!is_null($this->_limit) &&
                self::$_config[$this->_connection_name]['limit_clause_style'] == ORM::LIMIT_STYLE_LIMIT) {
                if (self::get_db($this->_connection_name)->getAttribute(PDO::ATTR_DRIVER_NAME) == 'firebird') {
                    $fragment = 'ROWS';
                } else {
                    $fragment = 'LIMIT';
                }
                $fragment .= " {$this->_limit}";
            }
            return $fragment;
        }

        protected function _build_offset() {
            if (!is_null($this->_offset)) {
                $clause = 'OFFSET';
                if (self::get_db($this->_connection_name)->getAttribute(PDO::ATTR_DRIVER_NAME) == 'firebird') {
                    $clause = 'TO';
                }
                return "$clause " . $this->_offset;
            }
            return '';
        }

        protected function _join_if_not_empty($glue, $pieces) {
            $filtered_pieces = array();
            foreach ($pieces as $piece) {
                if (is_string($piece)) {
                    $piece = trim($piece);
                }
                if (!empty($piece)) {
                    $filtered_pieces[] = $piece;
                }
            }
            return join($glue, $filtered_pieces);
        }

        protected function _quote_one_identifier($identifier) {
            $parts = explode('.', $identifier);
            $parts = array_map(array($this, '_quote_identifier_part'), $parts);
            return join('.', $parts);
        }

        protected function _quote_identifier($identifier) {
            if (is_array($identifier)) {
                $result = array_map(array($this, '_quote_one_identifier'), $identifier);
                return join(', ', $result);
            } else {
                return $this->_quote_one_identifier($identifier);
            }
        }

        protected function _quote_identifier_part($part) {
            if ($part === '*') {
                return $part;
            }

            $quote_character = self::$_config[$this->_connection_name]['identifier_quote_character'];
            // double up any identifier quotes to escape them
            return $quote_character .
                   str_replace($quote_character,
                               $quote_character . $quote_character,
                               $part
                   ) . $quote_character;
        }

        protected static function _create_cache_key($query, $parameters, $table_name = null, $connection_name = self::DEFAULT_CONNECTION) {
            if(isset(self::$_config[$connection_name]['create_cache_key']) and is_callable(self::$_config[$connection_name]['create_cache_key'])){
                return call_user_func_array(self::$_config[$connection_name]['create_cache_key'], array($query, $parameters, $table_name, $connection_name));
            }
            $parameter_string = join(',', $parameters);
            $key = $query . ':' . $parameter_string;
            return sha1($key);
        }

        protected static function _check_query_cache($cache_key, $table_name = null, $connection_name = self::DEFAULT_CONNECTION) {
            if(isset(self::$_config[$connection_name]['check_query_cache']) and is_callable(self::$_config[$connection_name]['check_query_cache'])){
                return call_user_func_array(self::$_config[$connection_name]['check_query_cache'], array($cache_key, $table_name, $connection_name));
            } elseif (isset(self::$_query_cache[$connection_name][$cache_key])) {
                return self::$_query_cache[$connection_name][$cache_key];
            }
            return false;
        }

        public static function clear_cache($table_name = null, $connection_name = self::DEFAULT_CONNECTION) {
            self::$_query_cache = array();
            if(isset(self::$_config[$connection_name]['clear_cache']) and is_callable(self::$_config[$connection_name]['clear_cache'])){
                return call_user_func_array(self::$_config[$connection_name]['clear_cache'], array($table_name, $connection_name));
            }
        }

        protected static function _cache_query_result($cache_key, $value, $table_name = null, $connection_name = self::DEFAULT_CONNECTION) {
            if(isset(self::$_config[$connection_name]['cache_query_result']) and is_callable(self::$_config[$connection_name]['cache_query_result'])){
                return call_user_func_array(self::$_config[$connection_name]['cache_query_result'], array($cache_key, $value, $table_name, $connection_name));
            } elseif (!isset(self::$_query_cache[$connection_name])) {
                self::$_query_cache[$connection_name] = array();
            }
            self::$_query_cache[$connection_name][$cache_key] = $value;
        }

        protected function _run() {
            $query = $this->_build_select();
            $caching_enabled = self::$_config[$this->_connection_name]['caching'];

            if ($caching_enabled) {
                $cache_key = self::_create_cache_key($query, $this->_values, $this->_table_name, $this->_connection_name);
                $cached_result = self::_check_query_cache($cache_key, $this->_table_name, $this->_connection_name);

                if ($cached_result !== false) {
                    return $cached_result;
                }
            }

            self::_execute($query, $this->_values, $this->_connection_name);



            $statement = self::get_last_statement();

            $rows = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }

            if ($caching_enabled) {
                self::_cache_query_result($cache_key, $rows, $this->_table_name, $this->_connection_name);
            }

            // reset Idiorm after executing the query
            $this->_values = array();
            $this->_result_columns = array('*');
            $this->_using_default_result_columns = true;

            return $rows;
        }


        protected function _run_by_col($col_name) {
            $query = $this->_build_select();
            $caching_enabled = self::$_config[$this->_connection_name]['caching'];

            if ($caching_enabled) {
                $cache_key = self::_create_cache_key($query, $this->_values, $this->_table_name, $this->_connection_name);
                $cached_result = self::_check_query_cache($cache_key, $this->_table_name, $this->_connection_name);

                if ($cached_result !== false) {
                    return $cached_result;
                }
            }

            self::_execute($query, $this->_values, $this->_connection_name);
            $statement = self::get_last_statement();

            $rows = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $rows[$row[$col_name]] = $row;
            }

            if ($caching_enabled) {
                self::_cache_query_result($cache_key, $rows, $this->_table_name, $this->_connection_name);
            }

            // reset Idiorm after executing the query
            $this->_values = array();
            $this->_result_columns = array('*');
            $this->_using_default_result_columns = true;

            return $rows;
        }

        public function as_array() {
            if (func_num_args() === 0) {
                return $this->_data;
            }
            $args = func_get_args();
            return array_intersect_key($this->_data, array_flip($args));
        }

        public function get($key) {
            if (is_array($key)) {
                $result = array();
                foreach($key as $column) {
                    $result[$column] = isset($this->_data[$column]) ? $this->_data[$column] : null;
                }
                return $result;
            } else {
                return isset($this->_data[$key]) ? $this->_data[$key] : null;
            }
        }

        protected function _get_id_column_name() {
            if (!is_null($this->_instance_id_column)) {
                return $this->_instance_id_column;
            }
            if (isset(self::$_config[$this->_connection_name]['id_column_overrides'][$this->_table_name])) {
                return self::$_config[$this->_connection_name]['id_column_overrides'][$this->_table_name];
            }
            return self::$_config[$this->_connection_name]['id_column'];
        }

        public function id($disallow_null = false) {
            $id = $this->get($this->_get_id_column_name());

            if ($disallow_null) {
                if (is_array($id)) {
                    foreach ($id as $id_part) {
                        if ($id_part === null) {
                            throw new Exception('Primary key ID contains null value(s)');
                        }
                    }
                } else if ($id === null) {
                    throw new Exception('Primary key ID missing from row or is null');
                }
            }

            return $id;
        }

        public function set($key, $value = null) {
            return $this->_set_orm_property($key, $value);
        }

        public function set_expr($key, $value = null) {
            return $this->_set_orm_property($key, $value, true);
        }

        protected function _set_orm_property($key, $value = null, $expr = false) {
            if (!is_array($key)) {
                $key = array($key => $value);
            }
            foreach ($key as $field => $value) {
                $this->_data[$field] = $value;
                $this->_dirty_fields[$field] = $value;
                if (false === $expr and isset($this->_expr_fields[$field])) {
                    unset($this->_expr_fields[$field]);
                } else if (true === $expr) {
                    $this->_expr_fields[$field] = true;
                }
            }
            return $this;
        }

        public function is_dirty($key) {
            return array_key_exists($key, $this->_dirty_fields);
        }

        public function is_new() {
            return $this->_is_new;
        }

        public function save() {
            $query = array();

            // remove any expression fields as they are already baked into the query
            $values = array_values(array_diff_key($this->_dirty_fields, $this->_expr_fields));

            if (!$this->_is_new) { // UPDATE
                // If there are no dirty values, do nothing
                if (empty($values) && empty($this->_expr_fields)) {
                    return true;
                }
                $query = $this->_build_update();
                $id = $this->id(true);
                if (is_array($id)) {
                    $values = array_merge($values, array_values($id));
                } else {
                    $values[] = $id;
                }
            } else { // INSERT
                $query = $this->_build_insert();
            }

            $success = self::_execute($query, $values, $this->_connection_name);
            $caching_auto_clear_enabled = self::$_config[$this->_connection_name]['caching_auto_clear'];
            if($caching_auto_clear_enabled){
                self::clear_cache($this->_table_name, $this->_connection_name);
            }
            // If we've just inserted a new record, set the ID of this object
            if ($this->_is_new) {
                $this->_is_new = false;
                if ($this->count_null_id_columns() != 0) {
                    $db = self::get_db($this->_connection_name);
                    if($db->getAttribute(PDO::ATTR_DRIVER_NAME) == 'pgsql') {
                        // it may return several columns if a compound primary
                        // key is used
                        $row = self::get_last_statement()->fetch(PDO::FETCH_ASSOC);
                        foreach($row as $key => $value) {
                            $this->_data[$key] = $value;
                        }
                    } else {
                        $column = $this->_get_id_column_name();
                        // if the primary key is compound, assign the last inserted id
                        // to the first column
                        if (is_array($column)) {
                            $column = reset($column);
                        }
                        $this->_data[$column] = $db->lastInsertId();
                    }
                }
            }

            $this->_dirty_fields = $this->_expr_fields = array();

            return $success;
        }

        public function _add_id_column_conditions(&$query) {
            $query[] = "WHERE";
            $keys = is_array($this->_get_id_column_name()) ? $this->_get_id_column_name() : array( $this->_get_id_column_name() );
            $first = true;
            foreach($keys as $key) {
                if ($first) {
                    $first = false;
                }
                else {
                    $query[] = "AND";
                }
                $query[] = $this->_quote_identifier($key);
                $query[] = "= ?";
            }
        }

        protected function _build_update() {
            $query = array();
            $query[] = "UPDATE {$this->_quote_identifier($this->_table_name)} SET";

            $field_list = array();
            foreach ($this->_dirty_fields as $key => $value) {
                if(!array_key_exists($key, $this->_expr_fields)) {
                    $value = '?';
                }
                $field_list[] = "{$this->_quote_identifier($key)} = $value";
            }
            $query[] = join(", ", $field_list);
            $this->_add_id_column_conditions($query);
            return join(" ", $query);
        }

        protected function _build_insert() {
            $query[] = "INSERT INTO";
            $query[] = $this->_quote_identifier($this->_table_name);
            $field_list = array_map(array($this, '_quote_identifier'), array_keys($this->_dirty_fields));
            $query[] = "(" . join(", ", $field_list) . ")";
            $query[] = "VALUES";

            $placeholders = $this->_create_placeholders($this->_dirty_fields);
            $query[] = "({$placeholders})";

            if (self::get_db($this->_connection_name)->getAttribute(PDO::ATTR_DRIVER_NAME) == 'pgsql') {
                $query[] = 'RETURNING ' . $this->_quote_identifier($this->_get_id_column_name());
            }

            return join(" ", $query);
        }

        public function delete() {
            $query = array(
                "DELETE FROM",
                $this->_quote_identifier($this->_table_name)
            );
            $this->_add_id_column_conditions($query);
            return self::_execute(join(" ", $query), is_array($this->id(true)) ? array_values($this->id(true)) : array($this->id(true)), $this->_connection_name);
        }

        public function delete_many() {
            // Build and return the full DELETE statement by concatenating
            // the results of calling each separate builder method.
            $query = $this->_join_if_not_empty(" ", array(
                "DELETE FROM",
                $this->_quote_identifier($this->_table_name),
                $this->_build_where(),
            ));

            return self::_execute($query, $this->_values, $this->_connection_name);
        }

        public function offsetExists($key) {
            return array_key_exists($key, $this->_data);
        }

        public function offsetGet($key) {
            return $this->get($key);
        }

        public function offsetSet($key, $value) {
            if(is_null($key)) {
                throw new InvalidArgumentException('You must specify a key/array index.');
            }
            $this->set($key, $value);
        }

        public function offsetUnset($key) {
            unset($this->_data[$key]);
            unset($this->_dirty_fields[$key]);
        }

        public function __get($key) {
            return $this->offsetGet($key);
        }

        public function __set($key, $value) {
            $this->offsetSet($key, $value);
        }

        public function __unset($key) {
            $this->offsetUnset($key);
        }

        public function __isset($key) {
            return $this->offsetExists($key);
        }

        public function __call($name, $arguments)
        {
            $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

            if (method_exists($this, $method)) {
                return call_user_func_array(array($this, $method), $arguments);
            } else {
                throw new ORM_Method_Missing_Exception("Method $name() does not exist in class " . get_class($this));
            }
        }

        public static function __callStatic($name, $arguments)
        {
            $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

            return call_user_func_array(array('ORM', $method), $arguments);
        }
    }

    class ORM_String {
        protected $subject;
        protected $search;
        protected $replace;

        public static function value($subject) {
            return new self($subject);
        }

        public static function str_replace_outside_quotes($search, $replace, $subject) {
            return self::value($subject)->replace_outside_quotes($search, $replace);
        }

        public function __construct($subject) {
            $this->subject = (string) $subject;
        }

        public function replace_outside_quotes($search, $replace) {
            $this->search = $search;
            $this->replace = $replace;
            return $this->_str_replace_outside_quotes();
        }

        protected function _str_replace_outside_quotes(){
            $re_valid = '/
                # Validate string having embedded quoted substrings.
                ^                           # Anchor to start of string.
                (?:                         # Zero or more string chunks.
                  "[^"\\\\]*(?:\\\\.[^"\\\\]*)*"  # Either a double quoted chunk,
                | \'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'  # or a single quoted chunk,
                | [^\'"\\\\]+               # or an unquoted chunk (no escapes).
                )*                          # Zero or more string chunks.
                \z                          # Anchor to end of string.
                /sx';
            if (!preg_match($re_valid, $this->subject)) {
                throw new ORM_StringException("Subject string is not valid in the replace_outside_quotes context.");
            }
            $re_parse = '/
                # Match one chunk of a valid string having embedded quoted substrings.
                  (                         # Either $1: Quoted chunk.
                    "[^"\\\\]*(?:\\\\.[^"\\\\]*)*"  # Either a double quoted chunk,
                  | \'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'  # or a single quoted chunk.
                  )                         # End $1: Quoted chunk.
                | ([^\'"\\\\]+)             # or $2: an unquoted chunk (no escapes).
                /sx';
            return preg_replace_callback($re_parse, array($this, '_str_replace_outside_quotes_cb'), $this->subject);
        }

        protected function _str_replace_outside_quotes_cb($matches) {
            // Return quoted string chunks (in group $1) unaltered.
            if ($matches[1]) return $matches[1];
            // Process only unquoted chunks (in group $2).
            return preg_replace('/'. preg_quote($this->search, '/') .'/',
                $this->replace, $matches[2]);
        }
    }

    class ORM_ResultSet implements Countable, IteratorAggregate, ArrayAccess, Serializable {

        protected $_results = array();

        public function __construct(array $results = array()) {
            $this->set_results($results);
        }

        public function set_results(array $results) {
            $this->_results = $results;
        }

        public function get_results() {
            return $this->_results;
        }

        public function as_array() {
            return $this->get_results();
        }

        public function count() {
            return count($this->_results);
        }

        public function getIterator() {
            return new ArrayIterator($this->_results);
        }

        public function offsetExists($offset) {
            return isset($this->_results[$offset]);
        }

        public function offsetGet($offset) {
            return $this->_results[$offset];
        }

        public function offsetSet($offset, $value) {
            $this->_results[$offset] = $value;
        }

        public function offsetUnset($offset) {
            unset($this->_results[$offset]);
        }

        public function serialize() {
            return serialize($this->_results);
        }

        public function unserialize($serialized) {
            return unserialize($serialized);
        }

        public function __call($method, $params = array()) {
            foreach($this->_results as $model) {
                if (method_exists($model, $method)) {
                    call_user_func_array(array($model, $method), $params);
                } else {
                    throw new ORM_Method_Missing_Exception("Method $method() does not exist in class " . get_class($this));
                }
            }
            return $this;
        }
    }

    class ORM_StringException extends Exception {}

    class ORM_Method_Missing_Exception extends Exception {}
