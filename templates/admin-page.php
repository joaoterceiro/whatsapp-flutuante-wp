<?php
/**
 * Template da página administrativa do WhatsApp Flutuante WP
 *
 * @package WhatsappFlutuanteWP
 */

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Garantir que a variável $options esteja disponível
if (!isset($options) || !is_array($options)) {
    $options = get_option('wpfww_options', array(
        'button_enabled' => 0,
        'contacts' => array(
            array(
                'number' => '',
                'enabled' => 0,
                'message' => 'Oi! Tudo bem? Gostaria de saber mais sobre seus serviços e pedir um orçamento. Como a gente pode fazer isso?'
            ),
            array(
                'number' => '',
                'enabled' => 0,
                'message' => 'Oi! Tudo bem? Gostaria de saber mais sobre seus serviços e pedir um orçamento. Como a gente pode fazer isso?'
            )
        ),
        'button_position' => 'right',
        'tooltip_text' => 'Clique para conversar no WhatsApp',
        'button_color' => '#25d366',
        'button_size' => 'medium'
    ));
}
?>

<div class="wrap">
    <div class="wpfww-admin-container">
        <div class="wpfww-admin-header">
            <svg class="whatsapp-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M16 31C23.732 31 30 24.732 30 17C30 9.26801 23.732 3 16 3C8.26801 3 2 9.26801 2 17C2 19.5109 2.661 21.8674 3.81847 23.905L2 31L9.31486 29.3038C11.3014 30.3854 13.5789 31 16 31ZM16 28.8462C22.5425 28.8462 27.8462 23.5425 27.8462 17C27.8462 10.4576 22.5425 5.15385 16 5.15385C9.45755 5.15385 4.15385 10.4576 4.15385 17C4.15385 19.5261 4.9445 21.8675 6.29184 23.7902L5.23077 27.7692L9.27993 26.7569C11.1894 28.0746 13.5046 28.8462 16 28.8462Z" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4904 10.4713C12.2756 9.98513 12.0458 9.97598 11.832 9.96812L11.1464 9.96094C10.9316 9.96094 10.5872 10.0355 10.3021 10.3592C10.017 10.683 9.15137 11.4916 9.15137 13.1386C9.15137 14.7856 10.3473 16.3631 10.5018 16.5783C10.6564 16.7936 12.7955 20.2985 16.2079 21.5765C19.0661 22.6486 19.6203 22.4333 20.2047 22.3638C20.7892 22.2943 22.1525 21.5556 22.3976 20.8164C22.6426 20.0773 22.6426 19.4773 22.5653 19.338C22.488 19.1986 22.2732 19.1291 21.951 18.9902C21.6287 18.8513 20.0068 18.0427 19.7118 17.9534C19.4168 17.864 19.2019 17.8193 18.9871 18.143C18.7723 18.4667 18.1376 19.1986 17.9529 19.4139C17.7681 19.6292 17.5834 19.6515 17.2612 19.5126C16.939 19.3738 15.875 19.0382 14.624 17.9011C13.6487 17.0148 12.9887 15.9166 12.804 15.5929C12.6193 15.2692 12.7837 15.0986 12.9383 14.9402C13.0778 14.7961 13.2475 14.5659 13.4022 14.3804C13.5569 14.195 13.6041 14.0561 13.693 13.8409C13.7819 13.6256 13.7347 13.4401 13.6684 13.3013C13.6022 13.1624 12.9736 11.5078 12.4904 10.4713Z" fill="white"/>
            </svg>
            <h1><?php echo esc_html__('WhatsApp Flutuante WP', 'whatsapp-flutuante-wp'); ?></h1>
        </div>
        
        <div class="wpfww-admin-content">
            <p><?php echo esc_html__('Configure os números de WhatsApp e personalize o botão flutuante.', 'whatsapp-flutuante-wp'); ?></p>
            
            <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=whatsapp-flutuante-wp')); ?>" id="wpfww-form">
                <?php wp_nonce_field('wpfww_save_options', 'wpfww_nonce'); ?>
                
                <!-- Ativar/Desativar Botão -->
                <div class="wpfww-form-group">
                    <h2><?php echo esc_html__('Configurações Gerais', 'whatsapp-flutuante-wp'); ?></h2>
                    
                    <label class="wpfww-switch">
                        <input type="checkbox" name="wpfww_options[button_enabled]" id="wpfww_button_enabled" value="1" <?php checked(1, isset($options['button_enabled']) ? $options['button_enabled'] : 0); ?>>
                        <span class="wpfww-slider"></span>
                    </label>
                    <strong><?php echo esc_html__('Habilitar Botão Flutuante de WhatsApp', 'whatsapp-flutuante-wp'); ?></strong>
                </div>
                
                <div class="wpfww-button-settings">
                    <!-- Configurações dos Contatos -->
                    <div class="wpfww-form-group">
                        <h2><?php echo esc_html__('Contatos', 'whatsapp-flutuante-wp'); ?></h2>
                        <p><?php echo esc_html__('Configure até 2 números de WhatsApp para atendimento. O botão redirecionará randomicamente para um dos contatos ativos.', 'whatsapp-flutuante-wp'); ?></p>
                        
                        <?php for ($i = 0; $i < 2; $i++) : 
                            $contact = isset($options['contacts'][$i]) ? $options['contacts'][$i] : array(
                                'number' => '',
                                'enabled' => 0,
                                'message' => 'Oi! Tudo bem? Gostaria de saber mais sobre seus serviços e pedir um orçamento. Como a gente pode fazer isso?'
                            );
                        ?>
                            <div class="wpfww-contact-card">
                                <div class="wpfww-contact-header">
                                    <div class="wpfww-contact-title">
                                        <label class="wpfww-switch">
                                            <input type="checkbox" name="wpfww_options[contacts][<?php echo $i; ?>][enabled]" value="1" class="wpfww-contact-toggle" data-contact-id="<?php echo $i; ?>" <?php checked(1, isset($contact['enabled']) ? $contact['enabled'] : 0); ?>>
                                            <span class="wpfww-slider"></span>
                                        </label>
                                        <strong><?php echo esc_html__('Contato', 'whatsapp-flutuante-wp'); ?> <?php echo $i + 1; ?></strong>
                                    </div>
                                </div>
                                
                                <div class="wpfww-contact-settings-<?php echo $i; ?>">
                                    <div class="wpfww-settings-field">
                                        <label for="wpfww_contact_number_<?php echo $i; ?>"><?php echo esc_html__('Número do WhatsApp (com código do país)', 'whatsapp-flutuante-wp'); ?></label>
                                        <input type="text" id="wpfww_contact_number_<?php echo $i; ?>" name="wpfww_options[contacts][<?php echo $i; ?>][number]" value="<?php echo esc_attr(isset($contact['number']) ? $contact['number'] : ''); ?>" class="regular-text wpfww-phone-number" data-contact-id="<?php echo $i; ?>" placeholder="5561999786444">
                                        <p class="wpfww-help-text"><?php echo esc_html__('Digite o número completo com código do país, sem espaços, traços ou parênteses. Ex: 5561999786444', 'whatsapp-flutuante-wp'); ?></p>
                                    </div>
                                    
                                    <div class="wpfww-settings-field">
                                        <label for="wpfww_contact_message_<?php echo $i; ?>"><?php echo esc_html__('Mensagem Predefinida', 'whatsapp-flutuante-wp'); ?></label>
                                        <textarea id="wpfww_contact_message_<?php echo $i; ?>" name="wpfww_options[contacts][<?php echo $i; ?>][message]" class="large-text"><?php echo esc_textarea(isset($contact['message']) ? $contact['message'] : ''); ?></textarea>
                                        <p class="wpfww-help-text"><?php echo esc_html__('Esta mensagem aparecerá automaticamente quando o usuário clicar no botão.', 'whatsapp-flutuante-wp'); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Configurações de Aparência -->
                    <div class="wpfww-form-group">
                        <h2><?php echo esc_html__('Aparência do Botão', 'whatsapp-flutuante-wp'); ?></h2>
                        
                        <div class="wpfww-settings-grid">
                            <div>
                                <div class="wpfww-settings-field">
                                    <label><?php echo esc_html__('Posição do Botão', 'whatsapp-flutuante-wp'); ?></label>
                                    <div>
                                        <label>
                                            <input type="radio" name="wpfww_options[button_position]" value="right" <?php checked('right', isset($options['button_position']) ? $options['button_position'] : 'right'); ?>>
                                            <?php echo esc_html__('Direita', 'whatsapp-flutuante-wp'); ?>
                                        </label>
                                        &nbsp;&nbsp;&nbsp;
                                        <label>
                                            <input type="radio" name="wpfww_options[button_position]" value="left" <?php checked('left', isset($options['button_position']) ? $options['button_position'] : 'right'); ?>>
                                            <?php echo esc_html__('Esquerda', 'whatsapp-flutuante-wp'); ?>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="wpfww-settings-field">
                                    <label for="wpfww_button_size"><?php echo esc_html__('Tamanho do Botão', 'whatsapp-flutuante-wp'); ?></label>
                                    <select id="wpfww_button_size" name="wpfww_options[button_size]" class="regular-text">
                                        <option value="small" <?php selected('small', isset($options['button_size']) ? $options['button_size'] : 'medium'); ?>><?php echo esc_html__('Pequeno', 'whatsapp-flutuante-wp'); ?></option>
                                        <option value="medium" <?php selected('medium', isset($options['button_size']) ? $options['button_size'] : 'medium'); ?>><?php echo esc_html__('Médio', 'whatsapp-flutuante-wp'); ?></option>
                                        <option value="large" <?php selected('large', isset($options['button_size']) ? $options['button_size'] : 'medium'); ?>><?php echo esc_html__('Grande', 'whatsapp-flutuante-wp'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <div class="wpfww-settings-field">
                                    <label for="wpfww_button_color"><?php echo esc_html__('Cor do Botão', 'whatsapp-flutuante-wp'); ?></label>
                                    <input type="text" id="wpfww_button_color" name="wpfww_options[button_color]" value="<?php echo esc_attr(isset($options['button_color']) ? $options['button_color'] : '#25d366'); ?>" class="wpfww-color-picker">
                                </div>
                                
                                <div class="wpfww-settings-field">
                                    <label for="wpfww_tooltip_text"><?php echo esc_html__('Texto da Tooltip', 'whatsapp-flutuante-wp'); ?></label>
                                    <input type="text" id="wpfww_tooltip_text" name="wpfww_options[tooltip_text]" value="<?php echo esc_attr(isset($options['tooltip_text']) ? $options['tooltip_text'] : 'Clique para conversar no WhatsApp'); ?>" class="regular-text">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preview do Botão -->
                        <div class="wpfww-preview">
                            <h3><?php echo esc_html__('Preview do Botão', 'whatsapp-flutuante-wp'); ?></h3>
                            <div class="wpfww-preview-button">
                                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16 31C23.732 31 30 24.732 30 17C30 9.26801 23.732 3 16 3C8.26801 3 2 9.26801 2 17C2 19.5109 2.661 21.8674 3.81847 23.905L2 31L9.31486 29.3038C11.3014 30.3854 13.5789 31 16 31ZM16 28.8462C22.5425 28.8462 27.8462 23.5425 27.8462 17C27.8462 10.4576 22.5425 5.15385 16 5.15385C9.45755 5.15385 4.15385 10.4576 4.15385 17C4.15385 19.5261 4.9445 21.8675 6.29184 23.7902L5.23077 27.7692L9.27993 26.7569C11.1894 28.0746 13.5046 28.8462 16 28.8462Z" fill="white"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4904 10.4713C12.2756 9.98513 12.0458 9.97598 11.832 9.96812L11.1464 9.96094C10.9316 9.96094 10.5872 10.0355 10.3021 10.3592C10.017 10.683 9.15137 11.4916 9.15137 13.1386C9.15137 14.7856 10.3473 16.3631 10.5018 16.5783C10.6564 16.7936 12.7955 20.2985 16.2079 21.5765C19.0661 22.6486 19.6203 22.4333 20.2047 22.3638C20.7892 22.2943 22.1525 21.5556 22.3976 20.8164C22.6426 20.0773 22.6426 19.4773 22.5653 19.338C22.488 19.1986 22.2732 19.1291 21.951 18.9902C21.6287 18.8513 20.0068 18.0427 19.7118 17.9534C19.4168 17.864 19.2019 17.8193 18.9871 18.143C18.7723 18.4667 18.1376 19.1986 17.9529 19.4139C17.7681 19.6292 17.5834 19.6515 17.2612 19.5126C16.939 19.3738 15.875 19.0382 14.624 17.9011C13.6487 17.0148 12.9887 15.9166 12.804 15.5929C12.6193 15.2692 12.7837 15.0986 12.9383 14.9402C13.0778 14.7961 13.2475 14.5659 13.4022 14.3804C13.5569 14.195 13.6041 14.0561 13.693 13.8409C13.7819 13.6256 13.7347 13.4401 13.6684 13.3013C13.6022 13.1624 12.9736 11.5078 12.4904 10.4713Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botão de Salvar -->
                <div class="wpfww-submit-btn">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Salvar Configurações', 'whatsapp-flutuante-wp'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>