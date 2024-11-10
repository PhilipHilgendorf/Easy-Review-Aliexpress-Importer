jQuery(document).ready(function($) {
    $('#create-review').click(function(e) {
        // Gather data
        var product_id = aliexpress_review.post_id; // Get product ID from post ID
		e.preventDefault();
            $.ajax({
                type: 'POST',
                url: aliexpress_review.ajaxurl,
                data: {
                    action: 'create_review',
                    product_id: product_id,
                    aliexpress_url: encodeURIComponent($('#ali_url_review').val()),
                },
                success: function(response) {
                        alert(response); // Show success message
                },
                error: function() {
                    alert('An error occurred.');
                }
            });
    });
    $('#clear-review').click(function(e) {
        // Gather data
        var product_id = aliexpress_review.post_id; // Get product ID from post ID
		e.preventDefault();
            $.ajax({
                type: 'POST',
                url: aliexpress_review.ajaxurl,
                data: {
                    action: 'clear_review',
                    product_id: product_id,
                },
                success: function(response) {
                        alert(response); // Show success message
                },
                error: function() {
                    alert('An error occurred.');
                }
            });
    });
  
});
