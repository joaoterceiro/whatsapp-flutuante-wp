<?php
/**
 * Arquivo de desinstalação do plugin WhatsApp Flutuante WP
 *
 * Este arquivo será executado automaticamente quando o plugin for desinstalado
 * via painel administrativo do WordPress.
 *
 * @package WhatsappFlutuanteWP
 */

// Se o arquivo não for chamado pelo WordPress, saia
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remover as opções do banco de dados
delete_option('wpfww_options');

// Remover qualquer transient que o plugin possa ter criado
delete_transient('wpfww_cache');

// Limpar quaisquer outros dados temporários
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpfww_%'");