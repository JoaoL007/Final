/**
 * Funções de validação client-side
 * Fornece validação em tempo real dos campos do formulário
 */

// Validação de senha
function validatePassword(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    const errors = [];
    
    if (password.length < minLength) {
        errors.push(`A senha deve ter pelo menos ${minLength} caracteres`);
    }
    if (!hasUpperCase) {
        errors.push('A senha deve conter pelo menos uma letra maiúscula');
    }
    if (!hasLowerCase) {
        errors.push('A senha deve conter pelo menos uma letra minúscula');
    }
    if (!hasNumbers) {
        errors.push('A senha deve conter pelo menos um número');
    }
    if (!hasSpecialChar) {
        errors.push('A senha deve conter pelo menos um caractere especial');
    }

    return errors;
}

// Validação de email
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return 'Email inválido';
    }
    return '';
}

// Validação de nome
function validateName(name) {
    if (name.length < 3) {
        return 'O nome deve ter pelo menos 3 caracteres';
    }
    if (!/^[a-zA-ZÀ-ÿ\s]*$/.test(name)) {
        return 'O nome deve conter apenas letras';
    }
    return '';
}

// Validação de username
function validateUsername(username) {
    if (username.length < 3) {
        return 'O usuário deve ter pelo menos 3 caracteres';
    }
    if (!/^[a-zA-Z0-9_]*$/.test(username)) {
        return 'O usuário deve conter apenas letras, números e underscore';
    }
    return '';
}

// Exibe mensagem de erro
function showError(fieldId, error) {
    const errorDiv = document.getElementById(fieldId + '-error');
    const field = document.getElementById(fieldId);
    
    if (errorDiv && field) {
        errorDiv.textContent = error;
        field.classList.toggle('error', error !== '');
    }
}

// Remove mensagem de erro
function clearError(fieldId) {
    showError(fieldId, '');
}

// Validação genérica de campo
function validateField(fieldId, validationFunction) {
    const field = document.getElementById(fieldId);
    if (!field) return false;

    const error = validationFunction(field.value);
    showError(fieldId, error);
    return error === '';
}

// Validação de formulário completo
function validateForm(formId, validations) {
    let isValid = true;
    
    validations.forEach(({fieldId, validationFunction}) => {
        if (!validateField(fieldId, validationFunction)) {
            isValid = false;
        }
    });
    
    return isValid;
} 