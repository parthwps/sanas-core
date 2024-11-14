jQuery(document).ready(function($) {
    $('#upload-video-button').on('click', function(e) {
        e.preventDefault();
        var videoUploader = wp.media({
            title: 'Upload Video',
            button: {
                text: 'Use this Video'
            },
            multiple: false
        });

        videoUploader.on('select', function() {
            var attachment = videoUploader.state().get('selection').first().toJSON();
            $('#video-url').val(attachment.url);
            $('#video-preview').attr('src', attachment.url).show();
            $('#delete-video-button').show();
        });

        videoUploader.open();
    });

    $('#delete-video-button').on('click', function(e) {
        e.preventDefault();
        var videoUrl = $('#video-url').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: videoPreviewVars.ajaxurl,
            data: {
                action: 'sanas_delete_video',
                nonce: videoPreviewVars.delete_nonce,
                post_id: $('#post_ID').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#video-url').val('');
                    $('#video-preview').attr('src', '').hide();
                    $('#delete-video-button').hide();
                } else {
                    console.log('Error deleting video');
                }
            },
            error: function(errorThrown) {
                console.log('Error: ' + errorThrown);
            }
        });
    });
});
