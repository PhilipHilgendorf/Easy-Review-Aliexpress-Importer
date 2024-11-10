jQuery(document).ready(function($) {
    var loaded_reviews = [];
    $('#erai_submit').click(function(e) {
        $('#erai-popup-loading').show();
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: erai_admin_product.ajaxurl,
            data: {
                action: 'erai_load_wc_reviews',
                aliexpress_url: encodeURIComponent($('#erai_aliexpress_review_url').val()),
                product_id: erai_admin_product.product_id
            },
            success: function(response) {
                let result = JSON.parse(response)
                if(result.status != "success") {
                    alert("Error");
                    return;
                } 
                let reviews = result.reviews;
                let reviews_html = "";
                let stars_controls = [];
                loaded_reviews = reviews;
                for(let index = 0; index < reviews.length; index++) {
                    let stars = reviews[index]['buyerEval']/20;
                    if(!stars_controls.includes(stars)) {
                        stars_controls.push(stars);
                    }
                    let feedback = "";
                    if(reviews[index]['buyerFeedback']) {
                        feedback = reviews[index]['buyerFeedback'];
                    }
                    reviews_html += create_review(reviews[index]['buyerName'], stars, feedback, reviews[index]['images'], index)
                }

                let buttons_html = "";

                for(let index = 1;index < 6;index++) {
                    if(!stars_controls.includes(index)) {
                        continue
                    }
                    buttons_html += '<a class="erai-btn" stars="stars-'+index+'">'+index+' Stars</a>';
                }

                $('#erai-popup-review .erai-controlls').html(buttons_html);
                $('#erai-popup-review .erai-popup-body .erai-reviews').html(reviews_html);
                load_btn();
                $('#erai-popup-loading').hide();
                $('#erai-popup-review').show();
            },
            error: function() {
                $('#erai-popup-loading').hide();
                alert('An error occurred.');
            }
        });
    });

    
    $('.erai-popup .close').click(function(e) {
        $('.erai-popup').fadeOut(500);
    });

    function load_btn() {


        $('#erai_add_reviews').unbind('click');
        $('#erai_add_reviews').click(function(e) {
            let reviews = $('.erai-review-container.active')

            $('#erai-popup-review .result-msg').removeClass("erai-result-error");
            $('#erai-popup-review .result-msg').removeClass("erai-result-success");
            var data = [];
            for(let index = 0;index < reviews.length;index++) {
                data.push(loaded_reviews[$(reviews[index]).attr('id')])

            }
            $('#erai-popup-review').hide();
            $('#erai-popup-loading').show();
            $.ajax({
                type: 'POST',
                url: erai_admin_product.ajaxurl,
                data: {
                    action: 'erai_add_wc_reviews',
                    reviews: data,
                    product_id: erai_admin_product.product_id,
                },
                success: function(response) {
                    $('#erai-popup-loading').hide();
                    $('#erai-popup-review').show();
                    $('#erai-selected-reviews').text("0");
                    let result = JSON.parse(response)
                    if(!result.success) {
                        $('#erai-popup-review .result-msg').addClass("erai-result-error");
                    } else {
                        $('#erai-popup-review .result-msg').addClass("erai-result-success");
                    }
                    let reviews = $('.erai-review-container.active')
                    for(let index = 0;index < reviews.length;index++) {
                        if(result.removedIds.includes(index)) {
                            $(reviews[index]).remove();
                        }
                    }
                    $('#erai-popup-review .result-msg').text(result.msg)
                    $('#erai-popup-review .result-msg').show();
                    $('#erai-popup-review .result-msg').fadeIn(500);
                    load_btn();
                    setTimeout(function() {
                        $('#erai-popup-review .result-msg').fadeOut(500);
                    }, 5000)
                },
                error: function() {
                    alert('An error occurred.');
                }
            });
        });

        $('.erai-tranlate-content').unbind('click');
        $('.erai-tranlate-content').click(function(e) {
            var reviewcontainer = $(this).parent();
            $('#erai-popup-review .result-msg').show();
            $('#erai-popup-review .result-msg').fadeIn(500);
            $('#erai-popup-review .result-msg').text("Loading...");
            $(this).remove();
            var reviewId = reviewcontainer.attr('id')
            $.ajax({
                type: 'POST',
                url: erai_admin_product.ajaxurl,
                data: {
                    action: 'erai_translate_review_text',
                    review_text: loaded_reviews[reviewId]["buyerFeedback"],
                },
                success: function(response) {
                    let result = JSON.parse(response);
                    if(!result.success) {
                        $('#erai-popup-review .result-msg').text(response.content)
                        $('#erai-popup-review .result-msg').addClass("erai-result-error");
                        setTimeout(function() {
                            $('#erai-popup-review .result-msg').fadeOut(500);
                        }, 5000)
                        return;
                    }
                    $('#erai-popup-review .result-msg').fadeOut(500);
                    loaded_reviews[reviewId]["buyerFeedback"] = result.content;
                    loaded_reviews[reviewId]["translated"] = true;
                    $(reviewcontainer).find('.review-content').text(result.content);
                },
                error: function() {
                    alert('An error occurred.');
                }
            });
        })

        $('.erai-controlls .erai-btn').unbind('click');
        $('.erai-controlls .erai-btn').click(function(e) {
            $(this).toggleClass('active')
            if($('.erai-controlls .erai-btn.active').length == 0) {
                $('.erai-review-container').show();
                return;
            }

            $('.erai-review-container').hide();
            let controll_btns = $('.erai-controlls .erai-btn.active');

            for(let index = 0;index < controll_btns.length;index++) {
                $("."+$(controll_btns[index]).attr('stars')).show();
            }
            
        });

        $('.erai-review-container .remove-image').unbind('click');
        $('.erai-review-container .remove-image').click(function(e) {
            e.preventDefault();
            let urlToRemove = $(this).parent().attr('href')
            reviewId = $(this).closest('.erai-review-container').attr("id");
            const imageIndex = loaded_reviews[reviewId].images.findIndex(image => image === urlToRemove);

            // Remove the URL if found
            if (imageIndex !== -1) {
                loaded_reviews[reviewId].images.splice(imageIndex, 1);
                $(this).parent().remove();
            }

        });

        $('.erai-review-container').unbind('click');
        $('.erai-review-container').click(function(e) {
            if($(".erai-review-container .remove-image").has($(e.target)).length) {
                return
            }
            console.log("TEST")
            
            if((e.target.className || '').includes('erai-tranlate-content')) {
                return;
            }
            $(this).toggleClass('active');
            let totalselected =$('.erai-review-container.active').length;
            $('#erai-selected-reviews').text(totalselected);
            if(totalselected == 0) {
                $('#erai_add_reviews').attr('disabled', 'disabled');
            } else {
                $('#erai_add_reviews').attr('disabled', false);
            }
        })
    }

    function create_review(username, stars, review, images, id) {
        let starhtml = '<span class="dashicons dashicons-star-filled"></span>'.repeat(stars);

        starhtml += '<span class="dashicons dashicons-star-empty"></span>'.repeat(5-stars);

        var reviews = '<div class="erai-review-container stars-'+stars+'" id="'+id+'">\
            <span class="username">'+username+'</span>\
            <div class="star-container">'+starhtml+'</div>\
            <p class="review-content">'+review+'</p>\
            <div class="product-images">';
            
            if(images) {
                    
                for(let index = 0; index < images.length; index++) {
                    reviews +='<a href="'+ images[index]+'" target="_blank"><div class="remove-image"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></div><img src="'+ images[index]+'" alt=""></a>';
                }
            }
                
        reviews +=    '</div><br><a class="erai-tranlate-content">Translate text</a></div>';
        return reviews;
    }
});
