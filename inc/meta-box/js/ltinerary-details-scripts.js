jQuery(document).ready(function($) {
    // Add new program field
    $('#add_new_program').click(function(e) {
        e.preventDefault();
        
        var newRow = '<div class="row program-details-row">' +
                        '<div class="col-md-3">' +
                            '<div class="form-group">' +
                                '<input type="text" name="program_name[]" class="form-control" placeholder="Program Name">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<div class="form-group">' +
                                '<input type="text" name="program_time[]" class="form-control" placeholder="Program Time">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-1">' +
                            '<button type="button" class="remove-program btn btn-danger">Remove</button>' +
                        '</div>' +
                    '</div>';

        $('#program-details-container').append(newRow);
    });

    // Remove program field 
    $(document).on('click', '.remove-program', function() {
        $(this).closest('.program-details-row').remove();
    });

      $('#add_new_registry').click(function(e) {
        e.preventDefault();
        
        var newRow = '<div class="row program-details-row">' +
                        '<div class="col-md-3">' +
                            '<div class="form-group">' +
                                '<input type="text" name="program_name[]" class="form-control" placeholder="Program Name">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<div class="form-group">' +
                                '<input type="text" name="program_time[]" class="form-control" placeholder="Program Time">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-1">' +
                            '<button type="button" class="remove-program btn btn-danger">Remove</button>' +
                        '</div>' +
                    '</div>';

        $('#program-details-container').append(newRow);
    });

    // Remove program field 
    $(document).on('click', '.remove-program', function() {
        $(this).closest('.program-details-row').remove();
    });
});
