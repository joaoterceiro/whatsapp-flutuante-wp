<?php
/**
 * Funções auxiliares do plugin Whatsapp Flutuante WP
 *
 * @package WhatsappFlutuanteWP
 */

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Verifica se um número de telefone está no formato correto
 *
 * @param string $phone_number Número de telefone para validar
 * @return boolean Verdadeiro se for válido, falso caso contrário
 */
function wpfww_is_valid_phone_number($phone_number) {
    // Remover caracteres não numéricos
    $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
    
    // Verificar se tem entre 10 e 15 dígitos (padrão internacional)
    return (strlen($phone_number) >= 10 && strlen($phone_number) <= 15);
}

/**
 * Verifica se o plugin está ativo e tem pelo menos um contato configurado
 *
 * @return boolean Verdadeiro se estiver ativo e configurado, falso caso contrário
 */
function wpfww_is_active_and_configured() {
    $options = get_option('wpfww_options');
    
    if (!$options || !isset($options['button_enabled']) || !$options['button_enabled']) {
        return false;
    }
    
    // Verificar se há pelo menos um contato ativo e válido
    if (!isset($options['contacts']) || !is_array($options['contacts'])) {
        return false;
    }
    
    foreach ($options['contacts'] as $contact) {
        if (isset($contact['enabled']) && $contact['enabled'] && !empty($contact['number'])) {
            return true;
        }
    }
    
    return false;
}

/**
 * Formata o número de telefone para o formato WhatsApp
 *
 * @param string $phone_number Número de telefone
 * @return string Número formatado
 */
function wpfww_format_phone_number($phone_number) {
    // Remover caracteres não numéricos
    return preg_replace('/[^0-9]/', '', $phone_number);
}

/**
 * Cria uma máscara para o input de números de telefone
 * 
 * @param string $phone_number Número de telefone
 * @return string Número formatado com máscara
 */
function wpfww_display_phone_number($phone_number) {
    $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
    
    // Se for um número brasileiro (começa com 55)
    if (substr($phone_number, 0, 2) === '55') {
        $ddd = substr($phone_number, 2, 2);
        $part1 = substr($phone_number, 4, 5);
        $part2 = substr($phone_number, 9);
        
        return "+55 ($ddd) $part1-$part2";
    }
    
    // Formato internacional genérico
    return '+' . $phone_number;
}

/**
 * Registra os assets do plugin (CSS e JS)
 */
function wpfww_register_assets() {
    // Verificar se os diretórios existem
    $css_dir = WPFWW_PATH . 'assets/css';
    $js_dir = WPFWW_PATH . 'assets/js';
    
    if (!file_exists($css_dir)) {
        wp_mkdir_p($css_dir);
    }
    
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    
    // Criar arquivos CSS e JS, se não existirem
    // CSS do Admin
    if (!file_exists($css_dir . '/admin.css')) {
        $admin_css = "/* Estilos para o painel administrativo do WhatsApp Flutuante WP */
.wpfww-admin-container {
    max-width: 900px;
    margin: 20px 0;
}

.wpfww-admin-header {
    background-color: #25d366;
    color: #fff;
    padding: 20px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
}

/* Remover texto indesejado */
.wpfww-admin-header::after,
.wpfww-admin-header::before {
    content: none !important;
    display: none !important;
}

.wpfww-admin-header h1 {
    color: #fff;
    margin: 0;
    font-size: 24px;
}

.wpfww-admin-header .whatsapp-icon {
    margin-right: 15px;
    width: 40px;
    height: 40px;
}

.wpfww-admin-content {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 5px 5px;
}

.wpfww-form-group {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.wpfww-form-group:last-child {
    border-bottom: none;
}

.wpfww-form-group h2 {
    font-size: 18px;
    margin-top: 0;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.wpfww-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    margin-right: 10px;
}

.wpfww-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.wpfww-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.wpfww-slider:before {
    position: absolute;
    content: '';
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .wpfww-slider {
    background-color: #25d366;
}

input:focus + .wpfww-slider {
    box-shadow: 0 0 1px #25d366;
}

input:checked + .wpfww-slider:before {
    transform: translateX(26px);
}

.wpfww-contact-card {
    background-color: #f9f9f9;
    border: 1px solid #eee;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}

.wpfww-contact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.wpfww-contact-title {
    font-weight: bold;
    display: flex;
    align-items: center;
}

.wpfww-preview {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    margin-top: 20px;
}

.wpfww-preview-button {
    display: inline-block;
    width: 60px;
    height: 60px;
    background-color: #25d366;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 60px;
    font-size: 24px;
}

.wpfww-settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 782px) {
    .wpfww-settings-grid {
        grid-template-columns: 1fr;
    }
}

.wpfww-settings-field {
    margin-bottom: 15px;
}

.wpfww-settings-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.wpfww-settings-field input[type='text'],
.wpfww-settings-field input[type='number'],
.wpfww-settings-field textarea,
.wpfww-settings-field select {
    width: 100%;
}

.wpfww-settings-field textarea {
    min-height: 100px;
}

.wpfww-submit-btn {
    margin-top: 20px;
}

.wpfww-help-text {
    font-style: italic;
    color: #666;
    margin-top: 5px;
}

.wpfww-error {
    border-color: #dc3232 !important;
}

.wpfww-success-notice {
    background: #f7fff7;
    border-left: 4px solid #25d366;
    padding: 10px;
    margin: 15px 0;
}

/* Correção para o menu lateral do WordPress */
#adminmenu .dashicons-whatsapp:before {
    content: \"\\f232\";
    font-family: dashicons;
    color: #25d366;
}";
        file_put_contents($css_dir . '/admin.css', $admin_css);
    }
    
    // CSS do Frontend
    if (!file_exists($css_dir . '/frontend.css')) {
        $frontend_css = "/* Estilos para o botão flutuante de WhatsApp */
.whatsapp-float {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 40px;
    background-color: #25d366;
    color: #fff;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.whatsapp-float.left-position {
    right: auto;
    left: 40px;
}

.whatsapp-float:hover {
    background-color: #20ba5a;
    transform: scale(1.1);
    box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);
}

.whatsapp-float .tooltip-text {
    visibility: hidden;
    width: 180px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    bottom: 70px;
    right: 0;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.whatsapp-float.left-position .tooltip-text {
    right: auto;
    left: 0;
}

.whatsapp-float:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

.whatsapp-float svg {
    width: 30px;
    height: 30px;
}

/* Tamanhos do botão */
.whatsapp-float.size-small {
    width: 50px;
    height: 50px;
}

.whatsapp-float.size-small svg {
    width: 25px;
    height: 25px;
}

.whatsapp-float.size-large {
    width: 70px;
    height: 70px;
}

.whatsapp-float.size-large svg {
    width: 35px;
    height: 35px;
}

/* Estilos para dispositivos móveis */
@media screen and (max-width: 767px) {
    .whatsapp-float {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
    }
    
    .whatsapp-float.left-position {
        right: auto;
        left: 20px;
    }
    
    .whatsapp-float svg {
        width: 25px;
        height: 25px;
    }
    
    .whatsapp-float.size-large {
        width: 60px;
        height: 60px;
    }
    
    .whatsapp-float.size-large svg {
        width: 30px;
        height: 30px;
    }
}";
        file_put_contents($css_dir . '/frontend.css', $frontend_css);
    }
    
    // JS Admin
    if (!file_exists($js_dir . '/admin.js')) {
        $admin_js = "/**
 * Script para o painel administrativo do WhatsApp Flutuante WP
 */

(function($) {
    'use strict';
    
    // Função principal
    $(document).ready(function() {
        // Inicializar o color picker
        if ($.fn.wpColorPicker) {
            $('.wpfww-color-picker').wpColorPicker();
        }
        
        // Toggle para mostrar/esconder as configurações do botão
        $('#wpfww_button_enabled').on('change', function() {
            if ($(this).is(':checked')) {
                $('.wpfww-button-settings').slideDown();
            } else {
                $('.wpfww-button-settings').slideUp();
            }
        });
        
        // Trigger inicial para mostrar/esconder as configurações do botão
        if (!$('#wpfww_button_enabled').is(':checked')) {
            $('.wpfww-button-settings').hide();
        }
        
        // Toggle para mostrar/esconder as configurações de cada contato
        $('.wpfww-contact-toggle').on('change', function() {
            const contactId = $(this).data('contact-id');
            
            if ($(this).is(':checked')) {
                $(`.wpfww-contact-settings-${contactId}`).slideDown();
            } else {
                $(`.wpfww-contact-settings-${contactId}`).slideUp();
            }
        });
        
        // Trigger inicial para mostrar/esconder as configurações de cada contato
        $('.wpfww-contact-toggle').each(function() {
            const contactId = $(this).data('contact-id');
            
            if (!$(this).is(':checked')) {
                $(`.wpfww-contact-settings-${contactId}`).hide();
            }
        });
        
        // Atualizar o preview conforme as configurações são alteradas
        function updatePreview() {
            const buttonColor = $('#wpfww_button_color').val();
            const buttonPosition = $('input[name=\"wpfww_options[button_position]\"]:checked').val();
            const buttonSize = $('#wpfww_button_size').val();
            
            // Atualizar cor
            $('.wpfww-preview-button').css('background-color', buttonColor);
            
            // Atualizar posição
            $('.wpfww-preview').css('text-align', buttonPosition === 'left' ? 'left' : 'right');
            
            // Atualizar tamanho
            let buttonWidth = 60;
            let buttonHeight = 60;
            let iconSize = 30;
            
            if (buttonSize === 'small') {
                buttonWidth = 50;
                buttonHeight = 50;
                iconSize = 25;
            } else if (buttonSize === 'large') {
                buttonWidth = 70;
                buttonHeight = 70;
                iconSize = 35;
            }
            
            $('.wpfww-preview-button').css({
                'width': buttonWidth + 'px',
                'height': buttonHeight + 'px',
                'line-height': buttonHeight + 'px'
            });
            
            $('.wpfww-preview-button svg').css({
                'width': iconSize + 'px',
                'height': iconSize + 'px'
            });
        }
        
        // Atualizar preview ao carregar a página
        updatePreview();
        
        // Atualizar preview quando as configurações mudam
        $('#wpfww_button_color').wpColorPicker({
            change: function(event, ui) {
                setTimeout(updatePreview, 100);
            }
        });
        
        $('input[name=\"wpfww_options[button_position]\"]').on('change', updatePreview);
        $('#wpfww_button_size').on('change', updatePreview);
        
        // Validação dos números de telefone
        $('.wpfww-phone-number').on('blur', function() {
            const phoneNumber = $(this).val().replace(/[^0-9]/g, '');
            
            if (phoneNumber && (phoneNumber.length < 10 || phoneNumber.length > 15)) {
                $(this).addClass('wpfww-error');
                alert('O número de telefone deve ter entre 10 e 15 dígitos.');
            } else {
                $(this).removeClass('wpfww-error');
            }
        });
        
        // Confirmar antes de desativar o botão
        $('#wpfww-form').on('submit', function(e) {
            if ($('#wpfww_button_enabled').is(':checked')) {
                // Verificar se há pelo menos um contato ativo e com número
                let hasActiveContact = false;
                
                $('.wpfww-contact-toggle:checked').each(function() {
                    const contactId = $(this).data('contact-id');
                    const phoneNumber = $(`.wpfww-phone-number[data-contact-id=\"${contactId}\"]`).val();
                    
                    if (phoneNumber) {
                        hasActiveContact = true;
                        return false; // Sair do loop each
                    }
                });
                
                if (!hasActiveContact) {
                    e.preventDefault();
                    alert('Você precisa ter pelo menos um contato habilitado e com número de telefone válido.');
                    return false;
                }
            }
        });
    });
    
})(jQuery);";
        file_put_contents($js_dir . '/admin.js', $admin_js);
    }
    
    // JS Frontend
    if (!file_exists($js_dir . '/frontend.js')) {
        $frontend_js = "/**
 * Script para o botão flutuante de WhatsApp no frontend
 * WhatsApp Flutuante WP
 */

(function($) {
    'use strict';
    
    // Função principal
    function initWhatsAppButton() {
        const whatsappButton = document.getElementById('whatsapp-button');
        
        if (!whatsappButton) {
            return;
        }
        
        // Verificar se temos contatos disponíveis nos dados passados pelo PHP
        if (!wpfww_data || !wpfww_data.contacts || !wpfww_data.contacts.length) {
            console.warn('WhatsApp Flutuante WP: Nenhum contato ativo configurado.');
            return;
        }
        
        // Adicionar evento de clique no botão
        whatsappButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obter um contato aleatório
            const randomContact = getRandomContact();
            
            // Criar URL do WhatsApp e abrir em nova janela
            const whatsappURL = getWhatsAppURL(randomContact);
            window.open(whatsappURL, '_blank');
        });
    }
    
    // Função para obter um contato aleatório
    function getRandomContact() {
        const randomIndex = Math.floor(Math.random() * wpfww_data.contacts.length);
        return wpfww_data.contacts[randomIndex];
    }
    
    // Função para criar URL do WhatsApp
    function getWhatsAppURL(contact) {
        const encodedMessage = encodeURIComponent(contact.message);
        return `https://wa.me/${contact.number}?text=${encodedMessage}`;
    }
    
    // Inicializar quando o DOM estiver pronto
    $(document).ready(function() {
        initWhatsAppButton();
    });
    
    // Inicializar também após o carregamento da página (para compatibilidade com temas que carregam conteúdo de forma assíncrona)
    $(window).on('load', function() {
        initWhatsAppButton();
    });
    
})(jQuery);";
        file_put_contents($js_dir . '/frontend.js', $frontend_js);
    }
}

/**
 * Cria a estrutura de diretórios do plugin na ativação
 */
function wpfww_create_plugin_structure() {
    // Diretórios necessários
    $dirs = array(
        WPFWW_PATH . 'assets',
        WPFWW_PATH . 'assets/css',
        WPFWW_PATH . 'assets/js',
        WPFWW_PATH . 'templates'
    );
    
    // Criar diretórios, se não existirem
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
    
    // Registrar os assets
    wpfww_register_assets();
}

// Criar a estrutura do plugin na ativação
add_action('activate_' . WPFWW_BASENAME, 'wpfww_create_plugin_structure');

/**
 * Adiciona recursos de segurança ao plugin
 */
function wpfww_add_security_features() {
    // Verificar o AJAX nonce
    add_action('wp_ajax_wpfww_admin_action', function() {
        check_ajax_referer('wpfww_ajax_nonce', 'security');
        // Processar a ação AJAX aqui
        wp_die();
    });
    
    // Evitar acesso direto a arquivos do plugin
    if (!function_exists('wpfww_prevent_direct_access')) {
        function wpfww_prevent_direct_access() {
            if (!defined('ABSPATH')) {
                exit; // Sair se tentarem acessar diretamente
            }
        }
    }
    
    // Verificar permissões para todas as ações administrativas
    if (!function_exists('wpfww_verify_admin_permissions')) {
        function wpfww_verify_admin_permissions() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Você não tem permissão para acessar esta página.', 'whatsapp-flutuante-wp'));
            }
        }
    }
    
    // Sanitizar todas as entradas de usuário
    if (!function_exists('wpfww_sanitize_input')) {
        function wpfww_sanitize_input($input) {
            if (is_array($input)) {
                foreach ($input as $key => $value) {
                    $input[$key] = wpfww_sanitize_input($value);
                }
                return $input;
            }
            
            return sanitize_text_field($input);
        }
    }
}

/**
 * Adiciona filtros para validação extra de dados
 */
function wpfww_add_validation_filters() {
    // Filtrar dados antes de salvar nas opções
    add_filter('pre_update_option_wpfww_options', function($value, $old_value) {
        // Garantir que somente números válidos sejam salvos
        if (isset($value['contacts']) && is_array($value['contacts'])) {
            foreach ($value['contacts'] as $key => $contact) {
                if (!empty($contact['number']) && !wpfww_is_valid_phone_number($contact['number'])) {
                    // Se o número não for válido, usar o valor antigo ou limpar
                    $value['contacts'][$key]['number'] = isset($old_value['contacts'][$key]['number']) ? 
                        $old_value['contacts'][$key]['number'] : '';
                }
            }
        }
        
        return $value;
    }, 10, 2);
    
    // Filtrar saída de texto do usuário
    add_filter('wpfww_output_text', function($text) {
        return esc_html($text);
    });
}

/**
 * Registra hooks para melhorar o desempenho do plugin
 */
function wpfww_performance_hooks() {
    // Carregar scripts apenas quando necessário
    add_action('wp_enqueue_scripts', function() {
        // Verificar se o botão está ativo antes de carregar os scripts
        if (wpfww_is_active_and_configured()) {
            wp_enqueue_script('wpfww-frontend-js');
            wp_enqueue_style('wpfww-frontend-css');
        }
    }, 100); // Prioridade baixa para garantir que seja carregado após scripts principais
    
    // Evitar carregamento de scripts em páginas de administração desnecessárias
    add_action('admin_enqueue_scripts', function($hook) {
        // Carregar apenas na página do plugin
        if ('toplevel_page_whatsapp-flutuante-wp' !== $hook) {
            return;
        }
        
        wp_enqueue_style('wpfww-admin-css');
        wp_enqueue_script('wpfww-admin-js');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    });
    
    // Usar transients para cache
    if (!function_exists('wpfww_get_active_contacts')) {
        function wpfww_get_active_contacts() {
            $cache = get_transient('wpfww_active_contacts');
            
            if (false === $cache) {
                $options = get_option('wpfww_options');
                $active_contacts = array();
                
                if (isset($options['contacts']) && is_array($options['contacts'])) {
                    foreach ($options['contacts'] as $contact) {
                        if (!empty($contact['number']) && isset($contact['enabled']) && $contact['enabled']) {
                            $active_contacts[] = array(
                                'number' => $contact['number'],
                                'message' => $contact['message']
                            );
                        }
                    }
                }
                
                set_transient('wpfww_active_contacts', $active_contacts, HOUR_IN_SECONDS);
                
                return $active_contacts;
            }
            
            return $cache;
        }
    }
    
    // Limpar cache ao salvar opções
    add_action('update_option_wpfww_options', function() {
        delete_transient('wpfww_active_contacts');
    });
}

/**
 * Adiciona tratamento de erros e registro de logs
 */
function wpfww_error_handling() {
    // Função para registrar erros
    if (!function_exists('wpfww_log_error')) {
        function wpfww_log_error($message, $data = array()) {
            if (WP_DEBUG && WP_DEBUG_LOG) {
                error_log('[WhatsApp Flutuante WP] ' . $message . (empty($data) ? '' : ' - Dados: ' . json_encode($data)));
            }
        }
    }
    
    // Registrar ativações do plugin
    add_action('activated_plugin', function($plugin) {
        if (WPFWW_BASENAME === $plugin) {
            wpfww_log_error('Plugin ativado');
        }
    });
    
    // Registrar desativações do plugin
    add_action('deactivated_plugin', function($plugin) {
        if (WPFWW_BASENAME === $plugin) {
            wpfww_log_error('Plugin desativado');
        }
    });
    
    // Tratamento de exceções gerais
    set_exception_handler(function($exception) {
        if (strpos($exception->getFile(), 'whatsapp-flutuante-wp') !== false) {
            wpfww_log_error('Exceção capturada: ' . $exception->getMessage(), array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ));
        }
        
        // Não interromper outros tratadores de exceção
        return false;
    });
}

// Adicionar recursos de validação
wpfww_add_validation_filters();

// Adicionar hooks de desempenho
wpfww_performance_hooks();

// Adicionar tratamento de erros
wpfww_error_handling();

// Adicionar recursos de segurança
wpfww_add_security_features();