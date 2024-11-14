jQuery(document).ready(function($) {
    var registryIndex = $('#registry-container .registry-item').length;

    $('#add_new_registry').on('click', function() {
        var newRegistry = `
            <div class="registry-item">
                <div class="form-group">
                    <label for="registry_name_${registryIndex}">Registry Name</label>
                    <input type="text" id="registry_name_${registryIndex}" name="registries[${registryIndex}][name]" class="form-control">
                </div>
                <div class="form-group">
                    <label for="registry_url_${registryIndex}">Registry URL</label>
                    <input type="url" id="registry_url_${registryIndex}" name="registries[${registryIndex}][url]" class="form-control">
                </div>
                <button type="button" class="btn btn-default remove_registry">Remove</button>
            </div>
        `;
        $('#registry-container').append(newRegistry);
        registryIndex++;
    });

    $('#registry-container').on('click', '.remove_registry', function() {
        $(this).closest('.registry-item').remove();
    });
});
