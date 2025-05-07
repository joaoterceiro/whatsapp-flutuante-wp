<?php
/**
 * Plugin Name: Whatsapp Flutuante WP
 * Plugin URI: https://seusite.com.br/whatsapp-flutuante-wp
 * Description: Botão flutuante de WhatsApp com redirecionamento randômico, painel de controle administrativo e configurações personalizáveis.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com.br
 * Text Domain: whatsapp-flutuante-wp
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definições globais
define('WPFWW_VERSION', '1.0.0');
define('WPFWW_FILE', __FILE__);
define('WPFWW_PATH', plugin_dir_path(__FILE__));
define('WPFWW_URL', plugin_dir_url(__FILE__));
define('WPFWW_BASENAME', plugin_basename(__FILE__));

// Classe principal do plugin
class WhatsappFlutuanteWP {
    // Instância única (Singleton)
    private static $instance = null;
    
    // Opções do plugin
    private $options;
    
    /**
     * Construtor da classe principal
     */
    private function __construct() {
        // Inicializar opções
        $this->options = get_option('wpfww_options', $this->get_default_options());
        
        // Registrar hooks de ativação e desativação
        register_activation_hook(WPFWW_FILE, array($this, 'plugin_activation'));
        register_deactivation_hook(WPFWW_FILE, array($this, 'plugin_deactivation'));
        
        // Adicionar menu na área administrativa
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registrar configurações
        add_action('admin_init', array($this, 'register_settings'));
        
        // Adicionar links na página de plugins
        add_filter('plugin_action_links_' . WPFWW_BASENAME, array($this, 'add_plugin_links'));
        
        // Incluir scripts e estilos na área administrativa
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Incluir o botão flutuante no frontend, se estiver habilitado
        if ($this->options['button_enabled']) {
            add_action('wp_footer', array($this, 'display_whatsapp_button'));
            add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        }
        
        // Adicionar CSS para o ícone do WhatsApp no menu do admin
        add_action('admin_head', array($this, 'add_whatsapp_icon_styles'));
    }
    
    /**
     * Obter a instância única da classe (Singleton)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obter as opções padrão do plugin
     */
    private function get_default_options() {
        return array(
            'button_enabled' => 1,
            'contacts' => array(
                array(
                    'number' => '5561999786444',
                    'enabled' => 1,
                    'message' => 'Oi! Tudo bem? Gostaria de saber mais sobre seus serviços e pedir um orçamento. Como a gente pode fazer isso?'
                ),
                array(
                    'number' => '',
                    'enabled' => 0,
                    'message' => 'Oi! Tudo bem? Gostaria de saber mais sobre seus serviços e pedir um orçamento. Como a gente pode fazer isso?'
                )
            ),
            'button_position' => 'right', // right ou left
            'tooltip_text' => 'Clique para conversar no WhatsApp',
            'button_color' => '#25d366',
            'button_size' => 'medium' // small, medium, large
        );
    }
    
    /**
     * Ações na ativação do plugin
     */
    public function plugin_activation() {
        // Adicionar as opções padrão, se não existirem
        if (!get_option('wpfww_options')) {
            add_option('wpfww_options', $this->get_default_options());
        }
        
        // Limpar o cache de transients
        delete_transient('wpfww_cache');
        
        // Limpar rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Ações na desativação do plugin
     */
    public function plugin_deactivation() {
        // Limpar o cache de transients
        delete_transient('wpfww_cache');
        
        // Limpar rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Adicionar menu administrativo ao WordPress
     */
    public function add_admin_menu() {
        add_menu_page(
            __('WhatsApp Flutuante', 'whatsapp-flutuante-wp'),
            __('WhatsApp', 'whatsapp-flutuante-wp'),
            'manage_options',
            'whatsapp-flutuante-wp',
            array($this, 'admin_page'),
            'dashicons-whatsapp', // Usar ícone do Dashicons
            80 // Posição no menu
        );
    }
    
    /**
     * Registrar as configurações
     */
    public function register_settings() {
        register_setting('wpfww_settings', 'wpfww_options', array($this, 'sanitize_options'));
    }
    
    /**
     * Sanitizar as opções antes de salvar
     */
    public function sanitize_options($input) {
        $sanitized = array();
        
        // Botão habilitado/desabilitado
        $sanitized['button_enabled'] = isset($input['button_enabled']) ? 1 : 0;
        
        // Sanitizar contatos
        $sanitized['contacts'] = array();
        if (isset($input['contacts']) && is_array($input['contacts'])) {
            foreach ($input['contacts'] as $index => $contact) {
                // Sanitizar número de telefone (apenas dígitos)
                $sanitized['contacts'][$index]['number'] = preg_replace('/[^0-9]/', '', sanitize_text_field($contact['number']));
                
                // Sanitizar status de habilitado
                $sanitized['contacts'][$index]['enabled'] = isset($contact['enabled']) ? 1 : 0;
                
                // Sanitizar mensagem
                $sanitized['contacts'][$index]['message'] = sanitize_textarea_field($contact['message']);
            }
        } else {
            // Se não houver contatos, usar os padrões
            $defaults = $this->get_default_options();
            $sanitized['contacts'] = $defaults['contacts'];
        }
        
        // Posição do botão
        $sanitized['button_position'] = sanitize_text_field($input['button_position']);
        if (!in_array($sanitized['button_position'], array('right', 'left'))) {
            $sanitized['button_position'] = 'right';
        }
        
        // Texto da tooltip
        $sanitized['tooltip_text'] = sanitize_text_field($input['tooltip_text']);
        
        // Cor do botão
        $sanitized['button_color'] = sanitize_hex_color($input['button_color']);
        if (empty($sanitized['button_color'])) {
            $sanitized['button_color'] = '#25d366';
        }
        
        // Tamanho do botão
        $sanitized['button_size'] = sanitize_text_field($input['button_size']);
        if (!in_array($sanitized['button_size'], array('small', 'medium', 'large'))) {
            $sanitized['button_size'] = 'medium';
        }
        
        return $sanitized;
    }
    
    /**
     * Adicionar links rápidos na página de plugins
     */
    public function add_plugin_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=whatsapp-flutuante-wp') . '">' . __('Configurações', 'whatsapp-flutuante-wp') . '</a>'
        );
        return array_merge($plugin_links, $links);
    }
    
    /**
     * Carregar scripts e estilos na área administrativa
     */
    public function admin_enqueue_scripts($hook) {
        // Carregar apenas na página do plugin
        if ('toplevel_page_whatsapp-flutuante-wp' !== $hook) {
            return;
        }
        
        // Remover todos os estilos e scripts que possam estar interferindo
        wp_dequeue_style('wp-admin');
        
        // Estilos para a área administrativa
        wp_enqueue_style('wpfww-admin-css', WPFWW_URL . 'assets/css/admin.css', array(), WPFWW_VERSION);
        
        // Estilos extras para corrigir problemas específicos
        wp_enqueue_style('wpfww-admin-extra-css', WPFWW_URL . 'assets/css/admin-extra.css', array('wpfww-admin-css'), WPFWW_VERSION);
        
        // Scripts para a área administrativa
        wp_enqueue_script('wpfww-admin-js', WPFWW_URL . 'assets/js/admin.js', array('jquery'), WPFWW_VERSION, true);
        
        // Color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
    
    /**
     * Carregar scripts e estilos no frontend
     */
    public function frontend_enqueue_scripts() {
        wp_enqueue_style('wpfww-frontend-css', WPFWW_URL . 'assets/css/frontend.css', array(), WPFWW_VERSION);
        wp_enqueue_script('wpfww-frontend-js', WPFWW_URL . 'assets/js/frontend.js', array('jquery'), WPFWW_VERSION, true);
        
        // Passar variáveis para o JavaScript
        $active_contacts = array();
        foreach ($this->options['contacts'] as $contact) {
            if (!empty($contact['number']) && $contact['enabled']) {
                $active_contacts[] = array(
                    'number' => $contact['number'],
                    'message' => $contact['message']
                );
            }
        }
        
        wp_localize_script('wpfww-frontend-js', 'wpfww_data', array(
            'contacts' => $active_contacts,
            'tooltip_text' => $this->options['tooltip_text']
        ));
    }
    
    /**
     * Adicionar CSS para o ícone do WhatsApp no menu do admin
     */
    public function add_whatsapp_icon_styles() {
        ?>
        <style>
            /* Ícone de WhatsApp para o menu do WordPress */
            .dashicons-whatsapp:before {
                content: "\f232"; /* Código do ícone do WhatsApp no Font Awesome */
                font-family: "dashicons";
            }
            
            /* Ícone na lista de plugins */
            .plugins-php .dashicons-whatsapp:before {
                color: #25d366;
            }
            
            /* Ícone no menu */
            #adminmenu .dashicons-whatsapp:before {
                color: #25d366;
            }
            
            /* Estilo do cabeçalho da página admin */
            .wpfww-admin-header {
                position: relative;
                overflow: hidden;
            }
            
            /* Remover texto "enter and activate" */
            .wpfww-admin-header:after {
                content: none !important;
                display: none !important;
            }
            
            /* Entrada de texto específica que está causando erro */
            input[value="enter and activate"] {
                display: none !important;
            }
        </style>
        <?php
    }
    
    /**
     * Exibir o botão flutuante de WhatsApp no frontend
     */
    public function display_whatsapp_button() {
        // Verificar se há pelo menos um contato habilitado e com número
        $has_active_contact = false;
        foreach ($this->options['contacts'] as $contact) {
            if (!empty($contact['number']) && $contact['enabled']) {
                $has_active_contact = true;
                break;
            }
        }
        
        // Se não houver contatos ativos, não exibir o botão
        if (!$has_active_contact) {
            return;
        }
        
        // Definir classes com base nas opções
        $position_class = ($this->options['button_position'] === 'left') ? 'left-position' : 'right-position';
        $size_class = 'size-' . $this->options['button_size'];
        
        // CSS inline para a cor do botão
        $custom_css = '.whatsapp-float { background-color: ' . esc_attr($this->options['button_color']) . '; }';
        $custom_css .= '.whatsapp-float:hover { background-color: ' . esc_attr($this->adjust_brightness($this->options['button_color'], -20)) . '; }';
        
        // Exibir o botão
        ?>
        <style>
            <?php echo $custom_css; ?>
        </style>
        <a id="whatsapp-button" href="#" class="whatsapp-float <?php echo esc_attr($position_class); ?> <?php echo esc_attr($size_class); ?>" target="_blank">
            <div class="tooltip-text"><?php echo esc_html($this->options['tooltip_text']); ?></div>
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 31C23.732 31 30 24.732 30 17C30 9.26801 23.732 3 16 3C8.26801 3 2 9.26801 2 17C2 19.5109 2.661 21.8674 3.81847 23.905L2 31L9.31486 29.3038C11.3014 30.3854 13.5789 31 16 31ZM16 28.8462C22.5425 28.8462 27.8462 23.5425 27.8462 17C27.8462 10.4576 22.5425 5.15385 16 5.15385C9.45755 5.15385 4.15385 10.4576 4.15385 17C4.15385 19.5261 4.9445 21.8675 6.29184 23.7902L5.23077 27.7692L9.27993 26.7569C11.1894 28.0746 13.5046 28.8462 16 28.8462Z" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4904 10.4713C12.2756 9.98513 12.0458 9.97598 11.832 9.96812L11.1464 9.96094C10.9316 9.96094 10.5872 10.0355 10.3021 10.3592C10.017 10.683 9.15137 11.4916 9.15137 13.1386C9.15137 14.7856 10.3473 16.3631 10.5018 16.5783C10.6564 16.7936 12.7955 20.2985 16.2079 21.5765C19.0661 22.6486 19.6203 22.4333 20.2047 22.3638C20.7892 22.2943 22.1525 21.5556 22.3976 20.8164C22.6426 20.0773 22.6426 19.4773 22.5653 19.338C22.488 19.1986 22.2732 19.1291 21.951 18.9902C21.6287 18.8513 20.0068 18.0427 19.7118 17.9534C19.4168 17.864 19.2019 17.8193 18.9871 18.143C18.7723 18.4667 18.1376 19.1986 17.9529 19.4139C17.7681 19.6292 17.5834 19.6515 17.2612 19.5126C16.939 19.3738 15.875 19.0382 14.624 17.9011C13.6487 17.0148 12.9887 15.9166 12.804 15.5929C12.6193 15.2692 12.7837 15.0986 12.9383 14.9402C13.0778 14.7961 13.2475 14.5659 13.4022 14.3804C13.5569 14.195 13.6041 14.0561 13.693 13.8409C13.7819 13.6256 13.7347 13.4401 13.6684 13.3013C13.6022 13.1624 12.9736 11.5078 12.4904 10.4713Z" fill="white"/>
            </svg>
        </a>
        <?php
    }
    
    /**
     * Ajustar o brilho de uma cor hexadecimal
     */
    private function adjust_brightness($hex, $steps) {
        // Remover o # se presente
        $hex = ltrim($hex, '#');
        
        // Converter para RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Ajustar o brilho
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Converter de volta para hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Renderizar a página de administração
     */
    public function admin_page() {
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Salvar as opções se o formulário foi enviado
        if (isset($_POST['submit']) && check_admin_referer('wpfww_save_options', 'wpfww_nonce')) {
            // Preparar os dados do formulário para processamento
            $input = array();
            
            // Botão habilitado/desabilitado
            $input['button_enabled'] = isset($_POST['wpfww_options']['button_enabled']) ? 1 : 0;
            
            // Contatos - importante garantir a preservação do estado habilitado/desabilitado
            $input['contacts'] = array();
            if (isset($_POST['wpfww_options']['contacts']) && is_array($_POST['wpfww_options']['contacts'])) {
                foreach ($_POST['wpfww_options']['contacts'] as $index => $contact) {
                    $input['contacts'][$index] = array(
                        'number' => isset($contact['number']) ? $contact['number'] : '',
                        'enabled' => isset($contact['enabled']) ? 1 : 0,
                        'message' => isset($contact['message']) ? $contact['message'] : ''
                    );
                }
            }
            
            // Posição do botão
            $input['button_position'] = isset($_POST['wpfww_options']['button_position']) ? $_POST['wpfww_options']['button_position'] : 'right';
            
            // Texto da tooltip
            $input['tooltip_text'] = isset($_POST['wpfww_options']['tooltip_text']) ? $_POST['wpfww_options']['tooltip_text'] : '';
            
            // Cor do botão
            $input['button_color'] = isset($_POST['wpfww_options']['button_color']) ? $_POST['wpfww_options']['button_color'] : '#25d366';
            
            // Tamanho do botão
            $input['button_size'] = isset($_POST['wpfww_options']['button_size']) ? $_POST['wpfww_options']['button_size'] : 'medium';
            
            // Sanitizar as opções
            $sanitized_options = $this->sanitize_options($input);
            
            // Salvar as opções no banco de dados
            update_option('wpfww_options', $sanitized_options);
            
            // Atualizar a propriedade de opções
            $this->options = $sanitized_options;
            
            // Limpar o cache
            delete_transient('wpfww_active_contacts');
            
            // Exibir mensagem de sucesso
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Configurações salvas com sucesso!', 'whatsapp-flutuante-wp') . '</p></div>';
        }
        
        // Incluir o template da página administrativa
        include WPFWW_PATH . 'templates/admin-page.php';
    }
}

// Inicializar o plugin
function wpfww_init() {
    return WhatsappFlutuanteWP::get_instance();
}
add_action('plugins_loaded', 'wpfww_init');

// Incluir arquivos adicionais
require_once WPFWW_PATH . 'includes/functions.php';