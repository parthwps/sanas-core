jQuery(document).ready(function ($) {
  //  wishlist-delete-icon
  $(".heart-icon").on("click", function (e) {
    e.preventDefault();
    var $icon = $(this);
    var cardId = $icon.data("card-id");

    $.ajax({
      url: sanas_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "toggle_wishlist",
        card_id: cardId,
        security: sanas_ajax_object.security,
      },
      success: function (response) {
        if (response.success) {
          if (response.data.action === "added") {
            $icon.addClass("active");
          } else if (response.data.action === "removed") {
            $icon.removeClass("active");
          }
        } else {
          console.log("Something went wrong. Please try again.");
        }
      },
    });
  });
});
