;(function() {

    var PAYPAL_BT_CONTAINER_QUERY = '.paypal-bt'
    var SPORTS = {
        '1_volleyball_debutant': {
            id: '1_volleyball_debutant',
            price: 135
        },
        '2_volleyball_intermediaire': {
            id: '2_volleyball_intermediaire',
            price: 135
        },
        '3_ultimate_frisbee': {
            id: '3_ultimate_frisbee',
            price: 110
        },
        '4_hockey_cosum': {
            id: '4_hockey_cosum',
            price: 120
        },
        '5_hockey_cosum_team': {
            id: '5_hockey_cosum_team',
            price: 900
        }
    }

    var _selectedSport = null
    var _buttonActions = null
    var _triedToSubmit = false

    $('input.firstname, input.lastname, input.email').on('change', updateValidation)

    $('.sport-list .item').on('click', selectSportHandler)

    var availableSports = $('.sport-list .item').not('.full')
    
    if (availableSports.length) {
        availableSports.first().trigger('click')
    } else {
        $('.paypal-link').remove()
        $('.form-fields').remove()
    }

    function getPriceWithTax(price) {
        var fixedPrice = price * 1.14975;
        fixedPrice = fixedPrice.toFixed(2)
        console.log(fixedPrice)
        return fixedPrice;
    }
    

    function updateValidation() {
        showFormError()

        if (_buttonActions) {
            if ((formValidationResult()).valid) {
                _buttonActions.enable()
            } else {
                _buttonActions.disable()
            }
        }
    }

    function selectSportHandler(e) {
        if ($(this).hasClass('full')) {
            return
        }
        
        var sportId = $(this).data('sport-id')
        _selectedSport = SPORTS[sportId]

        $('.sport-list .item').removeClass('active')
        $(this).addClass('active')

        renderPaypalBt()
    }

    function getFormInfos() {
        return {
            'firstname': $('input.firstname').val(),
            'lastname': $('input.lastname').val(),
            'email': $('input.email').val(),
            'sport': _selectedSport.id
        }
    }

    function formValidationResult() {
        var infos = getFormInfos()

        var result = {
            valid: true,
            firstname: true,
            lastname: true,
            email: true,
            sport: true
        }

        if ( ! infos.firstname.length) result.firstname = false
        if ( ! infos.lastname.length) result.lastname = false
        if ( ! infos.email.length) result.email = false
        if ( ! infos.sport) result.sport = false

        result.valid = result.firstname && result.lastname && result.email && result.sport

        return result
    }

    function showFormError(showErrorOnEmptyFields) {
        var formInfos = getFormInfos()
        var validationResults = formValidationResult()

        showErrorOnEmptyFields = showErrorOnEmptyFields || _triedToSubmit

        if (validationResults.firstname) {
            $('input.firstname').removeClass('error')
        } else {
            if ( ! showErrorOnEmptyFields && formInfos.firstname.length === 0) {
                $('input.firstname').removeClass('error')
            } else {
                $('input.firstname').addClass('error')
            }
        }

        // ---------------

        if (validationResults.lastname) {
            $('input.lastname').removeClass('error')
        } else {
            if ( ! showErrorOnEmptyFields && formInfos.lastname.length === 0) {
                $('input.lastname').removeClass('error')
            } else {
                $('input.lastname').addClass('error')
            }
        }

        // ---------------

        if (validationResults.email) {
            $('input.email').removeClass('error')
        } else {
            if ( ! showErrorOnEmptyFields && formInfos.email.length === 0) {
                $('input.email').removeClass('error')
            } else {
                $('input.email').addClass('error')
            }
        }
    }

    function renderPaypalBt() {
        $(PAYPAL_BT_CONTAINER_QUERY).empty()

        paypal.Button.render({

            env: 'production', // sandbox | production

            // PayPal Client IDs - replace with your own
            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
            client: {
                sandbox:    'AdfN54s8n5BRrTE0uLQGXA8PTD_uSj0_7k9SeBg6QA8hdHTOkQyhvkxFp1dOOSAVYLm0Fwxm1G8rmBR0',
                production: 'Aa5BJELDd81mYsgprOqBZ2kO68k7QsqqzUhEOQMWOYo_iIjM-kIhaPKBnfR8Pf2hmoPPwmclv4UTuKgx'
            },

            style: {
                label: 'pay', // checkout | credit | pay
                size:  'medium',    // small | medium | responsive
                shape: 'rect',     // pill | rect
                color: 'blue'      // gold | blue | silver
            },

            // Show the buyer a 'Pay Now' button in the checkout flow
            commit: true,

            validate: function(actions) {
                _buttonActions = actions
                updateValidation()
            },

            onClick: function() {
                _triedToSubmit = true
                showFormError(true)
            },

            // payment() is called when the button is clicked
            payment: function(data, actions) {

                // Make a call to the REST api to create the payment
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                amount: { total: getPriceWithTax(_selectedSport.price), currency: 'CAD' },
                                /*amount: { total: 0.02, currency: 'CAD' }*/
                            }
                        ]
                    },

                    experience: {
                        input_fields: {
                            no_shipping: 1
                        }
                    }
                });
            },

            // onAuthorize() is called when the buyer approves the payment
            onAuthorize: function(data, actions) {
                return actions.payment.get().then(function(data) {

                    var formInfos = getFormInfos()

                    console.log(data.payer)

                    var dataToSave = {
                        website_firstname:      formInfos.firstname,
                        website_lastname:      formInfos.lastname,
                        website_email:          formInfos.email,
                        website_sport:          formInfos.sport,
                        payment_cart:           data.cart,
                        payment_create_time:    data.create_time,
                        payment_id:             data.id,
                        payment_state:          data.state,
                        payer_country_code:     data.payer.payer_info.country_code,
                        payer_email:            data.payer.payer_info.email,
                        payer_first_name:       data.payer.payer_info.first_name,
                        payer_last_name:        data.payer.payer_info.last_name,
                        payer_id:               data.payer.payer_info.payer_id,
                        payer_phone:            data.payer.payer_info.phone
                    }

                    $.ajax({
                        method: 'post',
                        url: 'save.php',
                        data: dataToSave,
                        dataType: 'json',
                        cache: false
                    }).done(function(result) {
                        $('.zone-form .form').addClass('hide')

                        if (result.success) {
                            actions.payment.execute().then(function() {

                                $('.zone-form .form-success').addClass('show')

                                actions.payment.get().then(function(data) {
                                    $.ajax({
                                        method: 'post',
                                        url: 'update-phone.php',
                                        data: {
                                            id: result.id,
                                            payer_phone: data.payer.payer_info.phone
                                        },
                                        dataType: 'json',
                                        cache: false
                                    })
                                })
                            });
                        } else {
                            $('.zone-form .form-error').addClass('show')
                        }
                    })
                })
            }

        }, PAYPAL_BT_CONTAINER_QUERY);
    }

})();


