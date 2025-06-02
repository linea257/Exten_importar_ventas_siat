<?php

class hooks_importar_ventas_siat extends hooks 
{
    var $module_name = "importar_ventas_siat";
    
    function install_extension($check_only = true) 
    {
        error_log("Instalando extensión importar_ventas_siat");
        
        if ($check_only) return true;
        
        // Verificar/crear tabla para las facturas de venta
        $this->verify_database_tables();
        
        // Crear directorio para archivos importados
        $this->create_import_directories();
        
        // Registrar enlaces de menú
        $this->create_menu_entries();
        
        return true;
    }
    
    function uninstall_extension($check_only = true) 
    {
        error_log("Desinstalando extensión importar_ventas_siat");
        
        if ($check_only) return true;
        
        // Eliminar enlaces de menú
        $this->remove_menu_entries();
        
        // NO eliminamos la tabla para preservar los datos
        
        return true;
    }
    
    function activate_extension($company, $check_only = true) 
    {
        error_log("Activando extensión importar_ventas_siat para empresa: " . $company);
        
        if ($check_only) return true;
        
        // Verificar que existe la tabla
        $this->verify_database_tables();
        
        // Registrar enlaces de menú
        $this->create_menu_entries();
        
        return true;
    }
    
    function deactivate_extension($company, $check_only = true) 
    {
        error_log("Desactivando extensión importar_ventas_siat para empresa: " . $company);
        
        if ($check_only) return true;
        
        // Eliminar enlaces de menú
        $this->remove_menu_entries();
        
        return true;
    }
    
    // Verificar que existe la tabla para facturas de venta
    private function verify_database_tables() {
        global $db;
        
        // Verificar si la tabla existe
        $sql = "SHOW TABLES LIKE '0_facturas_venta'";
        $result = db_query($sql, "Error al verificar tabla 0_facturas_venta");
        
        if (db_num_rows($result) == 0) {
            // Si no existe, crearla
            $sql = "CREATE TABLE IF NOT EXISTS `0_facturas_venta` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `fecha` date DEFAULT NULL,
                `nro_fact` varchar(50) DEFAULT NULL,
                `nro_auth` varchar(50) DEFAULT NULL,
                `nit` varchar(20) DEFAULT NULL,
                `razon_social` varchar(255) DEFAULT NULL,
                `imp` decimal(15,2) DEFAULT NULL,
                `imp_ice` decimal(15,2) DEFAULT NULL,
                `imp_exc` decimal(15,2) DEFAULT NULL,
                `tasa_cero` decimal(15,2) DEFAULT NULL,
                `dbr` decimal(15,2) DEFAULT NULL,
                `imp_deb_fiscal` decimal(15,2) DEFAULT NULL,
                `deb_fiscal` decimal(15,2) DEFAULT NULL,
                `estado` varchar(20) DEFAULT NULL,
                `cod_control` varchar(50) DEFAULT NULL,
                `debe1` varchar(20) DEFAULT NULL,
                `debe1_dimension1` int(11) DEFAULT NULL,
                `debe1_dimension2` int(11) DEFAULT NULL,
                `debe2` varchar(20) DEFAULT NULL,
                `debe2_dimension1` int(11) DEFAULT NULL,
                `debe2_dimension2` int(11) DEFAULT NULL,
                `haber1` varchar(20) DEFAULT NULL,
                `haber1_dimension1` int(11) DEFAULT NULL,
                `haber1_dimension2` int(11) DEFAULT NULL,
                `haber2` varchar(20) DEFAULT NULL,
                `haber2_dimension1` int(11) DEFAULT NULL,
                `haber2_dimension2` int(11) DEFAULT NULL,
                `haber3` varchar(20) DEFAULT NULL,
                `haber3_dimension1` int(11) DEFAULT NULL,
                `haber3_dimension2` int(11) DEFAULT NULL,
                `memo` text DEFAULT NULL,
                `nro_trans` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_fecha` (`fecha`),
                KEY `idx_nit` (`nit`),
                KEY `idx_nro_fact` (`nro_fact`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
            
            $result = db_query($sql, "Error al crear tabla 0_facturas_venta");
            
            if ($result) {
                error_log("Tabla 0_facturas_venta creada correctamente");
            } else {
                error_log("Error al crear tabla 0_facturas_venta");
            }
        } else {
            error_log("Tabla 0_facturas_venta ya existe");
        }
        
        return true;
    }
    
    // Crear directorios necesarios para importación
    private function create_import_directories() {
        global $path_to_root;
        
        // Directorio principal del módulo
        $module_dir = $path_to_root . "/modules/importar_ventas_siat";
        
        // Directorio para archivos importados
        $import_dir = $module_dir . "/importados";
        $ventas_dir = $import_dir . "/ventas";
        
        // También crear en gl/importados/ventas para compatibilidad
        $gl_import_dir = $path_to_root . "/gl/importados";
        $gl_ventas_dir = $gl_import_dir . "/ventas";
        
        // Crear directorios del módulo
        if (!is_dir($import_dir)) {
            if (mkdir($import_dir, 0755, true)) {
                error_log("Directorio importados creado: " . $import_dir);
            } else {
                error_log("Error al crear directorio importados: " . $import_dir);
            }
        }
        
        if (!is_dir($ventas_dir)) {
            if (mkdir($ventas_dir, 0755, true)) {
                error_log("Directorio ventas creado: " . $ventas_dir);
            } else {
                error_log("Error al crear directorio ventas: " . $ventas_dir);
            }
        }
        
        // Crear directorios en gl para compatibilidad
        if (!is_dir($gl_import_dir)) {
            if (mkdir($gl_import_dir, 0755, true)) {
                error_log("Directorio gl/importados creado: " . $gl_import_dir);
            }
        }
        
        if (!is_dir($gl_ventas_dir)) {
            if (mkdir($gl_ventas_dir, 0755, true)) {
                error_log("Directorio gl/importados/ventas creado: " . $gl_ventas_dir);
            }
        }
        
        // Crear archivo .htaccess para seguridad en ambos directorios
        $htaccess_content = "deny from all\n";
        if (is_dir($import_dir)) {
            file_put_contents($import_dir . "/.htaccess", $htaccess_content);
        }
        if (is_dir($gl_import_dir)) {
            file_put_contents($gl_import_dir . "/.htaccess", $htaccess_content);
        }
    }
    
    // Crear entradas de menú
    private function create_menu_entries() {
        global $path_to_root;
        
        if (function_exists('add_menu_item')) {
            // Añadir al menú de Ventas
            add_menu_item(_("Importar Ventas SIAT"), 
                 "SA_PAYMENT", 
                 "modules/importar_ventas_siat/pages/importar_ventas_siat.php", 
                 "Ventas", 
                 "Transacciones");
                 
            error_log("Entrada de menú 'Importar Ventas SIAT' añadida");
        } else {
            error_log("No se pudo añadir al menú - función add_menu_item no disponible");
        }
    }
    
    // Eliminar entradas de menú
    private function remove_menu_entries() {
        // Si FrontAccounting tiene una función para eliminar entradas de menú, úsala aquí
        if (function_exists('remove_menu_item')) {
            remove_menu_item("Importar Ventas SIAT");
        }
        
        error_log("Entradas de menú eliminadas para importar_ventas_siat");
    }
    
    // Hook opcional: ejecutar después de cada login
    function post_login_check() {
        // Verificar que los directorios existen
        $this->create_import_directories();
        
        // Verificar que la tabla existe
        $this->verify_database_tables();
    }
    
    // Hook para añadir al menú principal
    function install_tabs($app) {
        // Este hook se puede usar para añadir pestañas al menú principal
        // Por ahora no lo usamos, pero lo dejamos disponible
    }
    
    // Hook para definir accesos de seguridad
    function install_access() {
        $security_sections[SS_IMPORTAR_VENTAS] = _("Importar Ventas SIAT");
        $security_areas['SA_IMPORTAR_VENTAS_SIAT'] = array(SS_IMPORTAR_VENTAS|1, _("Importar Ventas desde CSV"));
        
        return array($security_areas, $security_sections);
    }
}

?>