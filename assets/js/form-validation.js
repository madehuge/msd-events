jQuery(document).ready(function($){
    const form = $('#msd-event-form');
    const formMessage = $('#msd-form-message');

    // Real-time validation: remove error and add valid class
    form.find('input, textarea').on('input', function(){
        const $field = $(this);
        if($field.val().trim()){
            $field.removeClass('error').addClass('valid');
            $field.next('.msd-error').remove();
        } else {
            $field.removeClass('valid');
        }
    });

    form.on('submit', function(e){
        e.preventDefault();

        // Clear previous messages
        formMessage.hide().removeClass('error-message success-message').html('');
        form.find('input, textarea').removeClass('error valid');
        form.find('.msd-error').remove();

        let isValid = true;

        // Validate fields dynamically using data attributes
        form.find('[data-required="true"]').each(function(){
            const $field = $(this);
            const value = $field.val().trim();
            const msg = $field.data('error') || 'This field is required.';
            
            if(!value){
                isValid = false;
                $field.addClass('error').after('<div class="msd-error">' + msg + '</div>');
            }
        });

        if(!isValid){
            formMessage.addClass('error-message').removeClass('success-message').html('Please fix the errors below.').show();
            return;
        }

        // AJAX submission
        const formData = form.serialize();
        $.post(msd_ajax_object.ajax_url, formData, function(response){
            if(response.success){
                formMessage.addClass('success-message').removeClass('error-message').html(response.data.message).show();
                form[0].reset();
                form.find('input, textarea').removeClass('error valid');
                form.find('.msd-error').remove();
            } else {
                const errorMsg = response.data?.message || 'An unexpected error occurred.';
                formMessage.addClass('error-message').removeClass('success-message').html(errorMsg).show();
            }
        }, 'json');
    });
});
